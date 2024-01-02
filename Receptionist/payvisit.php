<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

$req = $con->prepare("SELECT * FROM prices");
$req->execute();
$prices = $req->fetchAll(PDO::FETCH_ASSOC);
$prices = array_column($prices, 'price', 'itemName');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visit_id'])) {
    $visitID = $_POST['visit_id'];

    $req = $con->prepare("
        SELECT prescription.*
        FROM prescription
        WHERE visitID = :visit_id
    ");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $prescriptions = $req->fetch();

    if ($prescriptions) {
        $req = $con->prepare("
            SELECT SUM(d.drugPrice) as total
            FROM prescriptiondetails
            JOIN hms.drug d on d.drugID = prescriptiondetails.drugID
            WHERE prescriptionID = :prescription_id
        ");
        $req->bindValue(':prescription_id', $prescriptions['prescriptionID']);
        $req->execute();
        $totaldrugs = $req->fetch();
    } else {
        $totaldrugs['total'] = 0;
    }

    $req = $con->prepare("SELECT count(*) as numberoftests FROM test WHERE visitID = :visit_id");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $numberoftests = $req->fetch();

    $totaltests = $numberoftests['numberoftests'] * $prices['test'];

    $req = $con->prepare("SELECT patientID FROM visit JOIN hms.appointment a on a.appointmentID = visit.appointmentID WHERE visitID = :visit_id");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $patientID = $req->fetch();

    $req = $con->prepare("SELECT admissionDate FROM inpatient WHERE patientID = :patient_id");
    $req->bindValue(':patient_id', $patientID['patientID']);
    $req->execute();
    $admissionDate = $req->fetch();
    if (!$admissionDate) {
        $admissionDate['admissionDate'] = date("Y-m-d");
    }

    $numberofdays = (strtotime(date("Y-m-d")) - strtotime($admissionDate['admissionDate'])) / (60 * 60 * 24);
    $totaldays = $numberofdays * $prices['dailyHospitalStay'];

    $total = $totaldrugs['total'] + $totaltests + $totaldays+ $prices['visit'];

    $req = $con->prepare("
        UPDATE visit
        SET paid = 'yes'
        WHERE visitID = :visit_id
    ");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();

    $req = $con->prepare("
        UPDATE inpatient
        SET status = 'discharged'
        WHERE patientID = :patient_id
    ");
    $req->bindValue(':patient_id', $patientID['patientID']);
    $req->execute();

    $req = $con->prepare("
        SELECT firstName, lastName
        FROM patient
        WHERE patientID = :patient_id
    ");
    $req->bindValue(':patient_id', $patientID['patientID']);
    $req->execute();
    $patient = $req->fetch();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <link href="../Assets/css/tailwind.css" rel="stylesheet">
    </head>
    <body class="bg-gray-50 dark:bg-slate-900">
    <?php require '../Assets/components/header.php'?>
    <?php require '../Assets/components/receptionistmenu.php'?>
    <div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Invoice :</h1>
        <div class="mt-4">
            <p><strong>Invoice Number:</strong> <?= $visitID ?></p>
            <p><strong>Invoice Date:</strong> <?= date('Y-m-d') ?></p>
            <p><strong>Bill To:</strong> <?= $patient['firstName'] . ' ' . $patient['lastName'] ?></p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-4">Details</h2>
            <p><strong>Prescribed Drugs Price:</strong> <?= $totaldrugs['total'] ?> MAD</p>
            <p><strong>Tests Quantity:</strong> <?= $numberoftests['numberoftests'] ?></p>
            <p><strong>Tests Rate:</strong> <?= $prices['test'] ?> MAD</p>
            <p><strong>Tests Amount:</strong> <?= $totaltests ?> MAD</p>
            <p><strong>Hospital Stay Quantity:</strong> <?= $numberofdays ?> days</p>
            <p><strong>Hospital Stay Rate:</strong> <?= $prices['dailyHospitalStay'] ?> MAD per day</p>
            <p><strong>Hospital Stay Amount:</strong> <?= $totaldays ?> MAD</p>
            <p><strong>Visit Quantity:</strong> 1</p>
            <p><strong>Visit Rate:</strong> <?= $prices['visit'] ?> MAD</p>
            <p><strong>Visit Amount:</strong> <?= $prices['visit'] ?> MAD</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-4">Total</h2>
            <p><strong>Total Amount:</strong> <?= $total ?> MAD</p>
        </div>
    </div>
    <script src="../node_modules/preline/dist/preline.js"></script>
    </body>
    </html>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>