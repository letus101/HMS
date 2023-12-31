<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

// Fetch the prices from the prices table
$req = $con->prepare("SELECT * FROM prices");
$req->execute();
$prices = $req->fetchAll(PDO::FETCH_ASSOC);
$prices = array_column($prices, 'price', 'itemName');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visit_id'])) {
    $visitID = $_POST['visit_id'];

    // Fetch the prescription for the visit
    $req = $con->prepare("
        SELECT prescription.*
        FROM prescription
        WHERE visitID = :visit_id
    ");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $prescriptions = $req->fetch();

    if ($prescriptions) {
        // Fetch the total price of the prescribed drugs
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

    // Fetch the number of tests for the visit
    $req = $con->prepare("SELECT count(*) as numberoftests FROM test WHERE visitID = :visit_id");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $numberoftests = $req->fetch();

    // Calculate the total price of the tests
    $totaltests = $numberoftests['numberoftests'] * $prices['test'];

    // Fetch the patient ID for the visit
    $req = $con->prepare("SELECT patientID FROM visit JOIN hms.appointment a on a.appointmentID = visit.appointmentID WHERE visitID = :visit_id");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();
    $patientID = $req->fetch();

    // Fetch the admission date for the patient
    $req = $con->prepare("SELECT admissionDate FROM inpatient WHERE patientID = :patient_id");
    $req->bindValue(':patient_id', $patientID['patientID']);
    $req->execute();
    $admissionDate = $req->fetch();
    if (!$admissionDate) {
        $admissionDate['admissionDate'] = date("Y-m-d");
    }

    // Calculate the number of days the patient has been in the hospital and the total price for the days
    $numberofdays = (strtotime(date("Y-m-d")) - strtotime($admissionDate['admissionDate'])) / (60 * 60 * 24);
    $totaldays = $numberofdays * $prices['dailyHospitalStay'];

    // Calculate the total price to pay
    $total = $totaldrugs['total'] + $totaltests + $totaldays+ $prices['visit'];

    // Update the paid attribute of the visit
    $req = $con->prepare("
        UPDATE visit
        SET paid = 'yes'
        WHERE visitID = :visit_id
    ");
    $req->bindValue(':visit_id', $visitID);
    $req->execute();

    echo "<script>alert('Total to pay: $total');</script>";
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Unpaid Visits</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/receptionistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">

</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>