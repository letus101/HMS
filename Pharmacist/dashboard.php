<?php
session_start();
if (!($_SESSION['role'] == 'Pharmacist')) {
    header('location: ../error403.php');
    exit();
}
    require_once '../config/cnx.php';
    $con = cnx_pdo();

// Number of drugs
    $req = $con->query("SELECT COUNT(*) as count FROM drug");
    $drugCount = $req->fetch()['count'];

// Total quantity of all drugs
    $req = $con->query("SELECT SUM(quantity) as total FROM stock");
    $totalQuantity = $req->fetch()['total'];

// Number of expired drugs
$req = $con->query("SELECT SUM(quantity) as total FROM stock WHERE expiryDate < CURDATE()");
$expiredQuantity = $req->fetch()['total'];
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
<?php require '../Assets/components/pharmasistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto ">
        <div class="mt-1 grid gap-6 grid-cols-2 sm:gap-12 lg:grid-cols-3 lg:gap-8">
            <div>
                <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">Number of Drugs</h4>
                <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $drugCount ?></p>
            </div>
            <div>
                <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">Total Quantity of All Drugs</h4>
                <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $totalQuantity ?></p>
            </div>
            <div>
                <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">Number of Expired Drugs</h4>
                <p class="mt-2 sm:mt-3 text-4xl sm:text-6xl font-bold text-blue-600"><?= $expiredQuantity ?></p>
            </div>
        </div>
    </div>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
