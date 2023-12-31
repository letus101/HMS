<?php
session_start();
if (!($_SESSION['role'] == 'Nurse')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("
    SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName
    FROM inpatient
    INNER JOIN patient ON inpatient.patientID = patient.patientID
    WHERE inpatient.inpatientID NOT IN (
        SELECT d.inpatientID
        FROM dailycheckup d
        WHERE DATE(d.checkupDate) = CURDATE()
    )
");
$req->execute();
$inpatients = $req->fetchAll();
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
    <table>
        <tr>
            <th>Patient Name</th>
            <th>Admission Date</th>
        </tr>
        <?php foreach ($inpatients as $inpatient): ?>
            <tr>
                <td><?= $inpatient['patientName'] ?></td>
                <td><?= $inpatient['admissionDate'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>