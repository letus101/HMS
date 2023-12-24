<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error404.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['addVital'])) {
    $vitalName = $_POST['vitalName'];

    // Check if the vital name already exists
    $req = $con->prepare("SELECT * FROM vitals WHERE vitalName = :vitalName");
    $req->bindValue(':vitalName', $vitalName);
    $req->execute();
    $existingVital = $req->fetch();

    if ($existingVital) {
        echo "<script>alert('The vital name already exists.');</script>";
    } else {
        // Insert the new vital name
        $req = $con->prepare("INSERT INTO vitals (vitalName) VALUES (:vitalName)");
        $req->bindValue(':vitalName', $vitalName);
        $req->execute();
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
<?php require '../Assets/components/adminmenu.php'?>

<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Vital</h1>
    <div class="mt-5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <div>
                <label for="vitalName" class="block text-sm mb-2 dark:text-white">Vital Name</label>
                <input type="text" name="vitalName" id="vitalName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Vital Name" required>
            </div>
            <div class="mt-3">
                <button type="submit" name="addVital" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Add Vital</button>
            </div>
        </form>
    </div>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
