<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("SELECT count(*) FROM user");
$req->execute();
$totalUsers = $req->fetchColumn();
$req = $con->prepare("SELECT count(*) FROM user join hms.role r on r.roleID = user.roleID where r.roleName = 'Doctor'");
$req->execute();
$totalDoctors = $req->fetchColumn();
$req = $con->prepare("SELECT count(*) FROM user join hms.role r on r.roleID = user.roleID where r.roleName = 'Nurse'");
$req->execute();
$totalNurses = $req->fetchColumn();
$req = $con->prepare("SELECT count(*) FROM patient");
$req->execute();
$totalPatients = $req->fetchColumn();
$req = $con->prepare("SELECT count(*) FROM appointment");
$req->execute();
$totalAppointments = $req->fetchColumn();
$req = $con->prepare("SELECT count(*) FROM visit");
$req->execute();
$totalVisits = $req->fetchColumn();
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
    <?php require '../Assets/components/adminmenu.php'?>
    <div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
        <header>
            <div class="flex justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Statistics</h2>
                </div>
        </header>
        <main>
            <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto ">
                <div class="mt-1 grid gap-6 grid-cols-2 sm:gap-12 lg:grid-cols-3 lg:gap-8">
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Users</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalUsers ?></p>
                    </div>
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Doctors</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalDoctors ?></p>
                    </div>
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Nurses</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalNurses ?></p>
                    </div>
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Patients</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalPatients ?></p>
                    </div>
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Appointments</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalAppointments ?></p>
                    </div>
                    <div>
                        <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">number of Visits</h4>
                        <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalVisits ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
