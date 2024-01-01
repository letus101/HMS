<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("SELECT drug.*, SUM(s.quantity) as totalQuantity FROM drug JOIN hms.stock s on drug.drugID = s.drugID WHERE s.expiryDate > CURDATE() GROUP BY s.drugID HAVING totalQuantity > 0");
$req->execute();
$drugs = $req->fetchAll();

if (isset($_POST['addPrescription'])) {
    $doctorID = $_SESSION['id'];
    $patientID = $_SESSION['patientID'];
    $visitID = $_SESSION['visitID'];
    $req = $con->prepare("INSERT INTO inpatient (patientID, admissionDate) VALUES (:patientID, :admissionDate)");
    $req->bindValue(':patientID', $patientID);
    $req->bindValue(':admissionDate', date('Y-m-d'));
    $req->execute();

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

        $req = $con->prepare("SELECT * FROM stock WHERE drugID = :drugID AND expiryDate > CURDATE() AND quantity > 0 ORDER BY expiryDate LIMIT 1");
        $req->bindValue(':drugID', $drug);
        $req->execute();
        $batch = $req->fetch();

        $newQuantity = $batch['quantity'] - 1;

        $req = $con->prepare("UPDATE stock SET quantity = :quantity WHERE stockID = :batchID");
        $req->bindValue(':quantity', $newQuantity);
        $req->bindValue(':batchID', $batch['stockID']);
        $req->execute();
    }

    if ($test) {
        header('location: addtest.php');
    }
    else {
        header('location: dashboard.php?success=visit');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form method="post" action="inpatient.php" class="space-y-4">
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white">Select Drugs :
                <select id="drugs" name="drugs[]" multiple class="w-full p-2 border border-gray-300 rounded bg-white text-gray-700 ">
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?= $drug['drugID'] ?>">
                            <?= $drug['drugName'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div id="drugFields" class="mt-3"></div>
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white"> Do you need to add tests :
                <input type="checkbox" name="test" placeholder="test" class="p-2 border border-gray-300 rounded">
            </label>
        </div>
        <div class="mt-3">
            <button type="submit" name="addPrescription" class="p-2 bg-blue-500 text-white rounded">Add Drugs</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    var choices = new Choices('#drugs', {
        searchEnabled: true,
        removeItemButton: true,
    });

    choices.passedElement.element.addEventListener('change', addFields, false);

    function addFields() {
        var select = document.getElementById("drugs");
        var container = document.getElementById("drugFields");
        container.innerHTML = "";
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                var label1 = document.createElement("label");
                label1.innerHTML = "Dose for " + select.options[i].text + ":";
                label1.className = "block text-l mb-2 dark:text-white";
                var input1 = document.createElement("input");
                input1.type = "text";
                input1.name = "dose[" + select.options[i].value + "]";
                input1.className = "w-full p-2 border border-gray-300 rounded bg-white text-gray-700";
                var label2 = document.createElement("label");
                label2.innerHTML = "Frequency for " + select.options[i].text + ":";
                label2.className = "block text-l mb-2 dark:text-white";
                var input2 = document.createElement("input");
                input2.type = "text";
                input2.name = "frequency[" + select.options[i].value + "]";
                input2.className = "w-full p-2 border border-gray-300 rounded bg-white text-gray-700";
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