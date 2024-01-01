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
    ) AND inpatient.status = 'Admitted'
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Patients in need checkup <?= DATE('d-m-Y') ?>:</h1>
    <table class="w-full mt-3 border-collapse border border-gray-300 dark:border-gray-700">
        <thead>
        <tr class="bg-gray-200 dark:bg-slate-800">
            <th class="p-2 border border-gray-300 dark:border-gray-700">Patient Name</th>
            <th class="p-2 border border-gray-300 dark:border-gray-700">Admission Date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($inpatients as $inpatient): ?>
            <tr>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $inpatient['patientName'] ?></td>
                <td class="p-2 border border-gray-300 dark:border-gray-700"><?= $inpatient['admissionDate'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>