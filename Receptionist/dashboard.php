<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error404.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['addPatient'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Check if the patient already exists
    $req = $con->prepare("SELECT * FROM patient WHERE firstName = :firstName AND lastName = :lastName AND dateOfBirth = :dateOfBirth");
    $req->bindValue(':firstName', $firstName);
    $req->bindValue(':lastName', $lastName);
    $req->bindValue(':dateOfBirth', $dateOfBirth);
    $req->execute();
    $existingPatient = $req->fetch();

    if ($existingPatient) {
        echo "<script>alert('The patient already exists.');</script>";
    } else {
        // Insert the new patient
        $req = $con->prepare("INSERT INTO patient (firstName, lastName, dateOfBirth, gender, phone, address) VALUES (:firstName, :lastName, :dateOfBirth, :gender, :phone, :address)");
        $req->bindValue(':firstName', $firstName);
        $req->bindValue(':lastName', $lastName);
        $req->bindValue(':dateOfBirth', $dateOfBirth);
        $req->bindValue(':gender', $gender);
        $req->bindValue(':phone', $phone);
        $req->bindValue(':address', $address);
        $req->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD PATIENT</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
    <?php require '../Assets/components/header.php'?>
    <?php require '../Assets/components/receptionistmenu.php'?>
    <div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Patient</h1>
        <div class="mt-5">
            <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
                <div>
                    <label for="firstName" class="block text-sm mb-2 dark:text-white">First Name</label>
                    <input type="text" name="firstName" id="firstName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="First Name" required>
                </div>
                <div class="mt-3">
                    <label for="lastName" class="block text-sm mb-2 dark:text-white">Last Name</label>
                    <input type="text" name="lastName" id="lastName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Last Name" required>
                </div>
                <div class="mt-3">
                    <label for="dateOfBirth" class="block text-sm mb-2 dark:text-white">Date of Birth</label>
                    <input type="date" name="dateOfBirth" id="dateOfBirth" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                </div>
                <div class="mt-3">
                    <label for="gender" class="block text-sm mb-2 dark:text-white">Gender</label>
                    <select name="gender" id="gender" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mt-3">
                    <label for="phone" class="block text-sm mb-2 dark:text-white">Phone</label>
                    <input type="tel" name="phone" id="phone" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Phone" required>
                </div>
                <div class="mt-3">
                    <label for="address" class="block text-sm mb-2 dark:text-white">Address</label>
                    <input type="text" name="address" id="address" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Address" required>
                </div>
                <div class="mt-3">
                    <button type="submit" name="addPatient" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Add Patient</button>
                </div>
            </form>
        </div>
    </div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
