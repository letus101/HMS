<?php
session_start();
if (!($_SESSION['role'] == 'Pharmacist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

$drugName = '';
$drugDescription = '';
$drugPrice = '';

if (isset($_POST['addDrug'])) {
    $drugName = strtolower($_POST['drugName']);
    $drugDescription = strtolower($_POST['drugDescription']);
    $drugPrice = $_POST['drugPrice'];

    $req = $con->prepare("SELECT * FROM drug WHERE drugName = :drugName");
    $req->bindValue(':drugName', $drugName);
    $req->execute();
    $drug = $req->fetch();
    if ($drug) {
        echo "<script>alert('Drug already exists.')</script>";
    } else {
        $req = $con->prepare("INSERT INTO drug (drugName, drugDescription, drugPrice) VALUES (:drugName, :drugDescription, :drugPrice)");
        $req->bindValue(':drugName', $drugName);
        $req->bindValue(':drugDescription', $drugDescription);
        $req->bindValue(':drugPrice', $drugPrice);
        $req->execute();
        echo "<script>alert('Drug added successfully.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drug</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/pharmasistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form action="adddrug.php" method="post">
        <div>
            <label for="drugName" class="block text-sm mb-2 dark:text-white">Drug Name</label>
            <input type="text" name="drugName" id="drugName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <label for="drugDescription" class="block text-sm mb-2 dark:text-white">Drug Description</label>
            <input type="text" name="drugDescription" id="drugDescription" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <label for="drugPrice" class="block text-sm mb-2 dark:text-white">Drug Price</label>
            <input type="number" step="0.01" name="drugPrice" id="drugPrice" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <button type="submit" name="addDrug" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Drug</button>
        </div>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>