<?php
session_start();
if (!($_SESSION['role'] == 'Nurse')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

// Fetch all inpatients who have not been checked up today
$req = $con->prepare("
    SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName
    FROM inpatient
    INNER JOIN patient ON inpatient.patientID = patient.patientID
    WHERE inpatient.inpatientID NOT IN (
        SELECT dailycheckup.inpatientID
        FROM dailycheckup
        WHERE DATE(dailycheckup.checkupDate) = CURDATE()
    )
");
$req->execute();
$inpatients = $req->fetchAll();

// Fetch all vitals from the vitals table
$req = $con->prepare("SELECT * FROM vitals");
$req->execute();
$vitals = $req->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inpatient_id'], $_POST['vital_values'])) {
    $inpatient_id = $_POST['inpatient_id'];
    $vital_values = $_POST['vital_values'];

    // Validate the input data
    foreach ($vital_values as $value) {
        if (!is_numeric($value)) {
            echo "Invalid value. Please enter a number.";
            exit;
        }
    }
    // Insert the checkup date, checkup time, and inpatient ID into the dailycheckup table
    $req = $con->prepare("
        INSERT INTO dailycheckup (inpatientID, checkupDate, checkupTime)
        VALUES (:inpatient_id, CURDATE(), CURTIME())
    ");
    $req->bindValue(':inpatient_id', $inpatient_id);
    $req->execute();

    // Get the ID of the inserted checkup
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
    echo "Daily checkup recorded successfully.";
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECK UP</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/nursemenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form action="checkup.php" method="post" id="checkupForm">
        <label for="inpatient_id">Select an inpatient:</label><br>
        <select id="inpatient_id" name="inpatient_id">
            <?php foreach ($inpatients as $inpatient) { ?>
                <option value="<?= $inpatient['inpatientID'] ?>"><?= $inpatient['patientName'] ?></option>
            <?php } ?>
        </select><br>
        <label for="vital_id">Select vitals:</label><br>
        <select id="vital_id" name="vital_id[]" multiple>
            <?php foreach ($vitals as $vital) { ?>
                <option value="<?= $vital['vitalID'] ?>"><?= $vital['vitalName'] ?></option>
            <?php } ?>
        </select><br>
        <div id="vitalValues"></div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
<script>
    document.getElementById('vital_id').addEventListener('change', function() {
        var vitalValues = document.getElementById('vitalValues');
        vitalValues.innerHTML = '';
        for (var i = 0; i < this.selectedOptions.length; i++) {
            var option = this.selectedOptions[i];
            var label = document.createElement('label');
            label.htmlFor = 'vital_value_' + option.value;
            label.textContent = 'Enter value for ' + option.text + ':';
            var input = document.createElement('input');
            input.type = 'text';
            input.id = 'vital_value_' + option.value;
            input.name = 'vital_values[' + option.value + ']';
            vitalValues.appendChild(label);
            vitalValues.appendChild(input);
            vitalValues.appendChild(document.createElement('br'));
        }
    });
</script>
</body>
</html>