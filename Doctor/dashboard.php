<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$doctorID = $_SESSION['id']; // Assuming the doctor's ID is stored in the session
$req = $con->prepare("SELECT appointment.*, CONCAT(patient.firstName, ' ', patient.lastName) as patientName FROM appointment
                      JOIN patient ON appointment.patientID = patient.patientID
                      WHERE appointment.userID = :doctorID AND DATE(appointment.appointmentDate) = CURDATE()");
$req->bindValue(':doctorID', $doctorID);
$req->execute();
$appointments = $req->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <div id='calendar'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridDay',
                    events: [
                        <?php foreach ($appointments as $appointment) {
                        $endTime = date("H:i:s", strtotime($appointment['appointmentTime']) + 30*60); // Add 30 minutes to appointment time
                        ?>
                        {
                            title: 'Appointment with <?= $appointment['patientName'] ?>',
                            start: '<?= $appointment['appointmentDate'] ?>T<?= $appointment['appointmentTime'] ?>',
                            end: '<?= $appointment['appointmentDate'] ?>T<?= $endTime ?>'
                        },
                        <?php } ?>
                    ]
                });

                calendar.render();
            });
    </script>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
