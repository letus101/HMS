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
    WHERE test.status = 'Scheduled' AND type.department = 'laboratory' AND visit.paid = 'no'
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Upcoming tests for <?= date('Y-m-d') ?></h1>
    <table class="w-full mt-3 border-collapse border border-gray-300 dark:border-gray-700">
        <thead>
        <tr class="bg-gray-200 dark:bg-slate-800">
            <th class="p-2 border border-gray-300 dark:border-gray-700">Test ID</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Patient Name</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Type Name</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tests as $test): ?>
            <tr>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $test['testID'] ?></td>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $test['patientName'] ?></td>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $test['typeName'] ?></td>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $test['status'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>