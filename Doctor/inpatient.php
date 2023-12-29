<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$doctorID = $_SESSION['id'];
$patientID = $_SESSION['patientID'];
$visitID = $_SESSION['visitID'];
$req = $con->prepare("INSERT INTO inpatient (patientID, admissionDate) VALUES (:patientID, :admissionDate)");
$req->bindValue(':patientID', $patientID);
$req->bindValue(':admissionDate', date('Y-m-d'));
$req->execute();

$req = $con->prepare("SELECT drug.*, SUM(s.quantity) as totalQuantity FROM drug JOIN hms.stock s on drug.drugID = s.drugID WHERE s.expiryDate > CURDATE() GROUP BY s.drugID HAVING totalQuantity > 0");
$req->execute();
$drugs = $req->fetchAll();

if (isset($_POST['addPrescription'])) {
    $drugs = $_POST['drugs'];
    $doses = $_POST['dose'];
    $frequencies = $_POST['frequency'];
    $test = isset($_POST['test']) ? 1 : 0;
    $req = $con->prepare("SELECT * FROM visit WHERE visitID = :visitID");
    $req->bindValue(':visitID', $visitID);
    $req->execute();
    $visit = $req->fetch();
    if ($visit) {
        $req = $con->prepare("INSERT INTO prescription (visitID, prescriptionDate) VALUES (:visitID, :prescriptionDate)");
        $req->bindValue(':visitID', $visitID);
        $req->bindValue(':prescriptionDate', date('Y-m-d'));
        $req->execute();
    } else {
        echo "Error: No visit found with ID $visitID";
    }
    $prescriptionID = $con->lastInsertId();
    foreach ($drugs as $drug) {
        $req = $con->prepare("INSERT INTO prescriptiondetails (prescriptionID, drugID, dose, frequency) VALUES (:prescriptionID, :drugID, :dose, :frequency)");
        $req->bindValue(':prescriptionID', $prescriptionID);
        $req->bindValue(':drugID', $drug);
        $req->bindValue(':dose', $doses[$drug]);
        $req->bindValue(':frequency', $frequencies[$drug]);
        $req->execute();
    }
    if ($test) {
        header('location: addtest.php');
    }
    else {
        header('location: dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form method="post" action="inpatient.php">
        <label> Drugs :
            <select id="drugs" name="drugs[]" multiple onchange="addFields()">
                <?php foreach ($drugs as $drug): ?>
                    <option value="<?= $drug['drugID'] ?>">
                        <?= $drug['drugName'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div id="drugFields"></div>
        <label > do you need to add tests :
            <input type="checkbox" name="test" placeholder="test" >
        </label>
        <button type="submit" name="addPrescription">Add Drugs</button>
    </form>
</div>
<script>
    function addFields() {
        var select = document.getElementById("drugs");
        var container = document.getElementById("drugFields");
        container.innerHTML = "";
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                var label1 = document.createElement("label");
                label1.innerHTML = "Dose for " + select.options[i].text + ":";
                var input1 = document.createElement("input");
                input1.type = "text";
                input1.name = "dose[" + select.options[i].value + "]";
                var label2 = document.createElement("label");
                label2.innerHTML = "Frequency for " + select.options[i].text + ":";
                var input2 = document.createElement("input");
                input2.type = "text";
                input2.name = "frequency[" + select.options[i].value + "]";
                container.appendChild(label1);
                container.appendChild(input1);
                container.appendChild(label2);
                container.appendChild(input2);
            }
        }
    }
</script>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
