<?php
session_start();
if (!($_SESSION['role'] == 'Radiologist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("
    SELECT test.* , concat(p.firstName,' ',p.lastName) AS patientName , t.typeName
    FROM test JOIN hms.visit v on v.visitID = test.visitID
    JOIN hms.visit v2 on v2.visitID = test.visitID
    JOIN hms.type t on t.typeID = test.typeID
    JOIN hms.appointment a on v.appointmentID = a.appointmentID
    JOIN hms.patient p on a.patientID = p.patientID
    WHERE test.status = 'Scheduled' AND t.department = 'radiology' AND  v2.paid ='no'
    ORDER BY test.testID
");
$req->execute();
$tests = $req->fetchAll();
?>
<?php
// Existing PHP code
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
<?php require '../Assets/components/radiomenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Upcoming tests to do <?= date('Y-m-d') ?></h1>
    <table class="w-full mt-3 border-collapse border border-gray-300 dark:border-gray-700">
        <thead>
        <tr class="bg-gray-200 dark:bg-slate-800">
            <th class="p-2 border border-gray-300 dark:border-gray-700">Test ID</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Patient Name</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Test Type</th>
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