<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error404.php');
} ?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-slate-900">
<header class="sticky top-0 inset-x-0 flex flex-wrap sm:justify-start sm:flex-nowrap z-[48] w-full bg-white border-b text-sm py-2.5 sm:py-4 lg:ps-64 dark:bg-gray-800 dark:border-gray-700">
    <?php require '../Assets/components/header.php'?>
</header>
<div>
    <?php require '../Assets/components/adminmenu.php'?>
    <div class="container w-full">
    <p class="text-center text-2xl font-bold text-gray-800 dark:text-white">Welcome <?= $_SESSION['username'] ?></p>
</div>
</div>

<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
