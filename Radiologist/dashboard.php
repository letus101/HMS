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
    JOIN hms.type t on t.typeID = test.typeID
    JOIN hms.appointment a on v.appointmentID = a.appointmentID
    JOIN hms.patient p on a.patientID = p.patientID
    WHERE test.status = 'Scheduled' AND t.department = 'radiology'
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
<?php require '../Assets/components/radiomenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <table>
        <tr>
            <th>Test ID</th>
            <th>Patient Name</th>
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