<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['modifyPassword'])) {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword != $confirmPassword) {
        echo "<script>alert('The new password and confirmation password do not match.');</script>";
        exit();
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $adminId = $_SESSION['id'];

    $req = $con->prepare("UPDATE user SET passwordHash = :newPasswordHash WHERE userID = :adminId");
    $req->bindValue(':newPasswordHash', $newPasswordHash);
    $req->bindValue(':adminId', $adminId);
    $req->execute();
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modify Password</h1>
    <div class="mt-5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <div>
                <label for="newPassword" class="block text-sm mb-2 dark:text-white">New Password</label>
                <input type="password" name="newPassword" id="newPassword" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="New Password" required>
            </div>
            <div class="mt-3">
                <label for="confirmPassword" class="block text-sm mb-2 dark:text-white">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Confirm Password" required>
            </div>
            <div class="mt-3">
                <button type="submit" name="modifyPassword" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Modify Password</button>
            </div>
        </form>
    </div>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
