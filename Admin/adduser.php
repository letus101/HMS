<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$req_role = $con->prepare("SELECT * FROM role WHERE roleName != 'Admin'");
$req_role->execute();
$role = $req_role->fetchAll();

if (isset($_POST['adduser'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $roleID = $_POST['role'];
    $address = $_POST['address'];
    $username = strtolower($firstName).'.'.strtolower($lastName);
    $password = $_POST['password'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Get the file extension
    $file_extension = pathinfo($image, PATHINFO_EXTENSION);

    // Check if the file extension is jpg or jpeg
    if ($file_extension == 'jpg' || $file_extension == 'jpeg') {
        // Rename the file with the username
        $new_image_name = $username . '.' . $file_extension;

        // Upload the file
        move_uploaded_file($image_tmp, "../storage/user_img/$new_image_name");

        // Use the new image name in the SQL query
        $req = $con->prepare("INSERT INTO user (firstName, lastName, phone, roleID, address, passwordHash, image,status,username) VALUES (:firstName, :lastName, :phone, :roleID, :address, :passwordHash, :image,:status,:username)");
        $req->bindValue(':firstName', $firstName);
        $req->bindValue(':lastName', $lastName);
        $req->bindValue(':phone', $phone);
        $req->bindValue(':roleID', $roleID);
        $req->bindValue(':address', $address);
        $req->bindValue(':passwordHash', $passwordHash);
        $req->bindValue(':status', 'active');
        $req->bindValue(':image', $new_image_name);
        $req->bindValue(':username', $username);
        $req->execute();
    } else {
        echo "<script>alert('Invalid file extension. Only jpg and jpeg files are allowed.');</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD USER</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/adminmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <header class="flex justify-between">
        <h1 class="text-5xl font-bold leading-tight text-gray-900 dark:text-white">
            Create new user
        </h1>
    </header>
    <main class="mt-1.5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-6 p-4">
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="firstName" class="block text-sm mb-2 dark:text-white">First Name</label>
                            <input type="text" name="firstName" id="firstName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="First Name" required>
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm mb-2 dark:text-white">Last Name</label>
                            <input type="text" name="lastName" id="lastName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm mb-2 dark:text-white">Phone</label>
                            <input type="tel" name="phone" id="phone" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Phone" required>
                        </div>
                        <div>
                            <label for="role" class="block text-sm mb-2 dark:text-white">Role</label>
                            <select name="role" id="role" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                                <option value="" disabled selected>Select role</option>
                                <?php foreach ($role as $r): ?>
                                    <option value="<?= $r['roleID'] ?>"><?= $r['roleName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="address" class="block text-sm mb-2 dark:text-white">Address</label>
                        <input type="text" name="address" id="address" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Address" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"  required>
                    </div>
                    <div>
                        <label for="image" class="block text-sm mb-2 dark:text-white">Upload Image</label>
                        <input type="file" name="image" id="image" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="adduser" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-1 dark:focus:ring-gray-600">
                            Add user
                        </button>
                    </div>
                </div>
            </div>
        </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
