<?php
session_start();
if (!($_SESSION['role'] == 'Lab Technician')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("
    SELECT test.*, type.typeName, concat(patient.firstName,' ',patient.lastName) AS patientName
    FROM test
    JOIN type ON test.typeID = type.typeID
    JOIN visit ON test.visitID = visit.visitID
    JOIN appointment ON visit.appointmentID = appointment.appointmentID
    JOIN patient ON appointment.patientID = patient.patientID
    WHERE test.status = 'Scheduled' AND type.department = 'laboratoy'
    ORDER BY test.testID
");
$req->execute();
$tests = $req->fetchAll();
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
<?php require '../Assets/components/labtechmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <table>
        <tr>
            <th>Test ID</th>
            <th>Patient Name</th>
            <th>Type Name</th>
            <th>Status</th>
        </tr>
        <?php foreach ($tests as $test): ?>
            <tr>
                <td><?= $test['testID'] ?></td>
                <td><?= $test['patientName'] ?></td>
                <td><?= $test['typeName'] ?></td>
                <td><?= $test['status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>