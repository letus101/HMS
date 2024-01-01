<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$doctorID = $_SESSION['id'];
$req = $con->prepare("SELECT appointment.*, CONCAT(patient.firstName, ' ', patient.lastName) as patientName FROM appointment
                      JOIN patient ON appointment.patientID = patient.patientID
                      WHERE appointment.userID = :doctorID AND DATE(appointment.appointmentDate) = CURDATE() AND appointment.status = 'Scheduled' ORDER BY appointment.appointmentTime");
$req->bindValue(':doctorID', $doctorID);
$req->execute();
$appointments = $req->fetchAll();

if (isset($_POST['addvisit'])) {
    $appointmentID = $_POST['appointment'];
    $diagnosis = $_POST['diagnosis'];
    $inpatient = isset($_POST['inpatient']) ? 1 : 0;
    $req = $con->prepare("INSERT INTO visit (appointmentID, diagnosis,visitDate,visitTime) VALUES (:appointmentID, :diagnosis, :visitDate, :visitTime)");
    $req->bindValue(':appointmentID', $appointmentID);
    $req->bindValue(':diagnosis', $diagnosis);
    $req->bindValue(':visitDate', date('Y-m-d'));
    $req->bindValue(':visitTime', date('H:i:s'));
    $req->execute();
    $req = $con->prepare("SELECT LAST_INSERT_ID() as visitID");
    $req->execute();
    $visitID = $req->fetch()['visitID'];
    $req = $con->prepare("UPDATE appointment SET status = 'Completed' WHERE appointmentID = :appointmentID");
    $req->bindValue(':appointmentID', $appointmentID);
    $req->execute();
    if ($inpatient) {
        $_SESSION['visitID'] = $visitID;
        $_SESSION['patientID'] = $con->query("SELECT patientID FROM appointment WHERE appointmentID = $appointmentID")->fetch()['patientID'];
        header('location: inpatient.php');
    }
    else {
        header('location: dashboard.php?success=visit');
    }
}
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
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Visit details</h1>
    <form method="post" action="addvisit.php" class="space-y-4">
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white"> Select an Appointment :
                <select name="appointment" class="p-3 border border-gray-300 rounded-l">
                    <?php foreach ($appointments as $appointment): ?>
                        <option value="<?= $appointment['appointmentID'] ?>">
                            <?= $appointment['patientName'] . ' - ' . $appointment['appointmentDate'].' - '.$appointment['appointmentTime'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white"> Diagnosis : <br>
                <textarea name="diagnosis" class="w-full p-2 border border-gray-300 rounded preline"></textarea>
            </label>
        </div>
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white"> Inpatient :
                <input type="checkbox" name="inpatient" value="1" class="p-2 border border-gray-300 rounded">
            </label>
        </div>
        <div class="mt-3">
            <button type="submit" name="addvisit" class="p-2 bg-blue-500 text-white rounded">Add Visit</button>
        </div>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>