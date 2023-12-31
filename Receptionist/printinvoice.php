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
    <form action="printinvoice.php" method="post">
        <label for="firstName">Enter patient's first name:</label><br>
        <input type="text" id="firstName" name="firstName"><br>
        <label for="lastName">Enter patient's last name:</label><br>
        <input type="text" id="lastName" name="lastName"><br>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Check</button>
    </form>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstName'], $_POST['lastName'])): ?>
        <?php if (count($visits) > 0): ?>
            <h2>Unpaid Visits</h2>
            <?php foreach ($visits as $visit): ?>
                <p>Visit ID: <?= $visit['visitID'] ?></p>
                <p>Visit Date: <?= $visit['visitDate'] ?></p>
                <form action="payvisit.php" method="post">
                    <input type="hidden" name="visit_id" value="<?= $visit['visitID'] ?>">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Pay</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No unpaid visits found for this patient.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>