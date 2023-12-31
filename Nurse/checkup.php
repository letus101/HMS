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
    <form action="checkup.php" method="post">
        <label for="inpatient_id">Select an inpatient:</label><br>
        <select id="inpatient_id" name="inpatient_id">
            <?php foreach ($inpatients as $inpatient) { ?>
                <option value="<?= $inpatient['inpatientID'] ?>"><?= $inpatient['patientName'] ?></option>
            <?php } ?>
        </select><br>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>