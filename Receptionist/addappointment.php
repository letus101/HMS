<?php
session_start();

if ($_SESSION['role'] !== 'Receptionist') {
    header('location: ../error403.php');
    exit();
}
if (isset($_GET['error']) && $_GET['error'] === "patientnotfound") {
    echo "<script>alert('Patient not found.')</script>";
}
if (isset($_GET['error']) && $_GET['error'] === "doctornotfound") {
    echo "<script>alert('no doctor is free at this time.')</script>";
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD APPOINTMENT</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/receptionistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add an Appointment</h1>
    <form action="choosedoctor.php" method="post">
        <div class="mt-3">
            <label for="firstName" class="block text-sm mb-2 dark:text-white">First Name :</label>
            <input type="text" name="firstName" id="firstName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="First Name" required>
        </div>
        <div class="mt-3">
            <label for="lastName" class="block text-sm mb-2 dark:text-white">Last Name :</label>
            <input type="text" name="lastName" id="lastName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Last Name" required>
        </div>
        <div class="mt-3">
            <label for="appointmentDate" class="block text-sm mb-2 dark:text-white">Appointment Date :</label>
            <input type="date" id="appointmentDate" name="appointmentDate" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <label for="appointmentTime" class="block text-sm mb-2 dark:text-white">Appointment Time :</label>
            <input type="time" id="appointmentTime" name="appointmentTime" step="1800" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <label for="description" class="block text-sm mb-2 dark:text-white">Appointment Description :</label>
            <input type="text" id="description" name="description" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <button  type="submit" name="addAppointment" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Appointment</button>
        </div>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>

</body>
</html>
