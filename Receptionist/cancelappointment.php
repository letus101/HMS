<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();

$patient = null;
$appointments = [];

if (isset($_POST['searchPatient'])) {
    $firstName = strtolower($_POST['firstName']);
    $lastName = strtolower($_POST['lastName']);

    $req = $con->prepare("SELECT * FROM patient WHERE firstName = :firstName AND lastName = :lastName");
    $req->bindValue(':firstName', $firstName);
    $req->bindValue(':lastName', $lastName);
    $req->execute();
    $patient = $req->fetch();

    if ($patient) {
        $req = $con->prepare("SELECT * FROM appointment WHERE patientID = :patientID AND status != 'Cancelled' AND  status != 'Completed'");
        $req->bindValue(':patientID', $patient['patientID']);
        $req->execute();
        $appointments = $req->fetchAll();
    }
}

if (isset($_POST['cancelAppointment'])) {
    $appointmentID = $_POST['cancelAppointment'];
    $req = $con->prepare("UPDATE appointment SET status = 'Canceled' WHERE appointmentID = :appointmentID");
    $req->bindValue(':appointmentID', $appointmentID);
    $req->execute();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Appointment</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/receptionistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form method="POST" class="space-y-4">
        <input type="text" name="firstName" placeholder="First Name" required class="p-2 border border-gray-300 rounded">
        <input type="text" name="lastName" placeholder="Last Name" required class="p-2 border border-gray-300 rounded">
        <button type="submit" name="searchPatient" class="p-2 bg-blue-500 text-white rounded">Search Patient</button>
    </form>
    <?php if ($patient): ?>
        <h2 class="text-2xl font-bold mt-6">Appointments for <?= $patient['firstName'] . ' ' . $patient['lastName'] ?>:</h2>
        <table class="w-full mt-4 border-collapse border border-gray-300">
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">Appointment ID</th>
                <th class="p-2 border border-gray-300">Date</th>
                <th class="p-2 border border-gray-300">Time</th>
                <th class="p-2 border border-gray-300">Doctor</th>
                <th class="p-2 border border-gray-300">Cancel</th>
            </tr>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td class="p-2 border border-gray-300"><?= $appointment['appointmentID'] ?></td>
                    <td class="p-2 border border-gray-300"><?= $appointment['appointmentDate'] ?></td>
                    <td class="p-2 border border-gray-300"><?= $appointment['appointmentTime'] ?></td>
                    <td class="p-2 border border-gray-300"><?= $appointment['userID'] ?></td>
                    <td class="p-2 border border-gray-300">
                        <form method="POST">
                            <button type="submit" name="cancelAppointment" value="<?= $appointment['appointmentID'] ?>" class="p-2 bg-blue-600 text-white rounded">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>