<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['cancelUser'])) {
    $username = $_POST['username'];

    $req = $con->prepare("UPDATE user SET status = 'canceled' WHERE username = :username ");
    $req->bindValue(':username', $username);
    $req->execute();
    echo "<script>alert('User canceled successfully')</script>";
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel user</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/adminmenu.php'?>

<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Cancel User</h1>
    <div class="mt-5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <div>
                <label for="username" class="block text-sm mb-2 dark:text-white">Username</label>
                <input type="text" name="username" id="username" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Username" required>
            </div>
            <div>
                <button onclick="return(confirmCancel())" type="submit" name="cancelUser" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Cancel User</button>
            </div>
        </form>
    </div>
</div>

<script src="../node_modules/preline/dist/preline.js"></script>
<script>
    function confirmCancel() {
        return confirm('Are you sure you want to cancel this user?');
    }
</script>
</body>
</html>
