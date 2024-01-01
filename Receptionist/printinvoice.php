<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

$visits = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstName'], $_POST['lastName'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    // Fetch the patient ID
    $req = $con->prepare("
        SELECT patientID
        FROM patient
        WHERE firstName = :firstName AND lastName = :lastName
    ");
    $req->bindValue(':firstName', $firstName);
    $req->bindValue(':lastName', $lastName);
    $req->execute();
    $patient = $req->fetch();

    if ($patient) {
        // Fetch the visits
        $req = $con->prepare("
            SELECT *
            FROM visit 
            JOIN hms.appointment a on a.appointmentID = visit.appointmentID
            JOIN hms.patient p on p.patientID = a.patientID
            WHERE paid = 'no' AND p.patientID = :patient_id
        ");
        $req->bindValue(':patient_id', $patient['patientID']);
        $req->execute();
        $visits = $req->fetchAll();
    }
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Check Unpaid Visits :</h1>
    <form action="printinvoice.php" method="post" class="space-y-4 mt-3">
        <div class="mt-3">
            <label for="firstName" class="block text-l mb-2 dark:text-white">Enter patient's first name:</label>
            <input type="text" id="firstName" name="firstName" class="p-2 border border-gray-300 rounded">
        </div>
        <div class="mt-3">
            <label for="lastName" class="block text-l mb-2 dark:text-white">Enter patient's last name:</label>
            <input type="text" id="lastName" name="lastName" class="p-2 border border-gray-300 rounded">
        </div>
        <div class="mt-3">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded">Check</button>
        </div>
    </form>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstName'], $_POST['lastName'])): ?>
        <?php if (count($visits) > 0): ?>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Unpaid Visits</h2>
            <?php foreach ($visits as $visit): ?>
                <p class="text-l dark:text-white">Visit ID: <?= $visit['visitID'] ?></p>
                <p class="text-l dark:text-white">Visit Date: <?= $visit['visitDate'] ?></p>
                <form action="payvisit.php" method="post" class="space-y-0.5 mt-3">
                    <input type="hidden" name="visit_id" value="<?= $visit['visitID'] ?>">
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Pay</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-l dark:text-white">No unpaid visits found for this patient.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>