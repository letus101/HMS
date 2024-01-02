<?php
session_start();
if (!($_SESSION['role'] == 'Nurse')) {
    header('location: ../error403.php');
    exit();
}
$nurseID = $_SESSION['id'];
require_once '../config/cnx.php';
$con = cnx_pdo();

$req = $con->prepare("
    SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName
    FROM inpatient
    INNER JOIN patient ON inpatient.patientID = patient.patientID
    WHERE inpatient.inpatientID NOT IN (
        SELECT dailycheckup.inpatientID
        FROM dailycheckup
        WHERE DATE(dailycheckup.checkupDate) = CURDATE()
    ) AND  inpatient.status = 'Admitted'
");
$req->execute();
$inpatients = $req->fetchAll();

$req = $con->prepare("SELECT * FROM vitals");
$req->execute();
$vitals = $req->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inpatient_id'], $_POST['vital_values'])) {
    $inpatient_id = $_POST['inpatient_id'];
    $vital_values = $_POST['vital_values'];

    foreach ($vital_values as $value) {
        if (!is_numeric($value)) {
            echo "Invalid value. Please enter a number.";
            exit;
        }
    }
    $req = $con->prepare("
        INSERT INTO dailycheckup (inpatientID, checkupDate, checkupTime,userID)
        VALUES (:inpatient_id, CURDATE(), CURTIME(), :nurseID)
    ");
    $req->bindValue(':inpatient_id', $inpatient_id);
    $req->bindValue(':nurseID', $nurseID);
    $req->execute();

    $checkup_id = $con->lastInsertId();
    foreach ($vital_values as $vital_id => $value) {
        $req = $con->prepare("
            INSERT INTO vitaldetails (checkupID, vitalID, vitaldetails.vitalValue)
            VALUES (:checkup_id, :vital_id, :value)
        ");
        $req->bindValue(':checkup_id', $checkup_id);
        $req->bindValue(':vital_id', $vital_id);
        $req->bindValue(':value', $value);
        $req->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECK UP</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/nursemenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Check Up</h1>
    <form action="checkup.php" method="post" id="checkupForm" class="space-y-4">
        <div class="mt-3">
            <label for="inpatient_id" class="block text-l mb-2 dark:text-white">Select an inpatient:</label>
            <select id="inpatient_id" name="inpatient_id" class="p-3 border border-gray-300 rounded">
                <?php foreach ($inpatients as $inpatient) { ?>
                    <option value="<?= $inpatient['inpatientID'] ?>"><?= $inpatient['patientName'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mt-3">
            <label for="vital_id" class="block text-l mb-2 dark:text-white">Select vitals:</label>
            <select id="vital_id" name="vital_id[]" multiple class="w-full p-2 border border-gray-300 rounded bg-white text-gray-700 ">
                <?php foreach ($vitals as $vital) { ?>
                    <option value="<?= $vital['vitalID'] ?>"><?= $vital['vitalName'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div id="vitalValues" class="mt-3"></div>
        <div class="mt-3">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded">Submit</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    var choices = new Choices('#vital_id', {
        searchEnabled: true,
        removeItemButton: true,
    });

    choices.passedElement.element.addEventListener('change', addFields, false);

    function addFields() {
        var select = document.getElementById("vital_id");
        var container = document.getElementById("vitalValues");
        container.innerHTML = "";
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                var label = document.createElement("label");
                label.innerHTML = "Enter value for " + select.options[i].text + ":";
                label.className = "block text-l mb-2 dark:text-white";
                var input = document.createElement("input");
                input.type = "text";
                input.name = "vital_values[" + select.options[i].value + "]";
                input.className = "w-full p-2 border border-gray-300 rounded bg-white text-gray-700";
                container.appendChild(label);
                container.appendChild(input);
            }
        }
    }
</script>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>