<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['modifyPassword'])) {
    $username = $_POST['username'];
    $newPassword = $_POST['newPassword'];
    $retypePassword = $_POST['retypePassword'];

    if ($newPassword != $retypePassword) {
        echo "<script>alert('The new password and the retyped password do not match.');</script>";
        exit();
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $req = $con->prepare("UPDATE user SET passwordHash = :newPasswordHash WHERE username = :username");
    $req->bindValue(':newPasswordHash', $newPasswordHash);
    $req->bindValue(':username', $username);
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modify User Password</h1>
    <div class="mt-5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <div>
                <label for="username" class="block text-sm mb-2 dark:text-white">Username</label>
                <input type="text" name="username" id="username" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Username" required>
            </div>
            <div class="mt-3">
                <label for="newPassword" class="block text-sm mb-2 dark:text-white">New Password</label>
                <input type="password" name="newPassword" id="newPassword" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="New Password" required>
            </div>
            <div class="mt-3">
                <label for="retypePassword" class="block text-sm mb-2 dark:text-white">Retype Password</label>
                <input type="password" name="retypePassword" id="retypePassword" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Retype Password" required>
            </div>
            <div class="mt-3">
                <button type="submit" name="modifyPassword" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Modify Password</button>
            </div>
        </form>
    </div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
