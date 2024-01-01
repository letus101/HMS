<?php
session_start();
if (!($_SESSION['role'] == 'Admin')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['addTest'])) {
    $testName = $_POST['testName'];
    $department = $_POST['department'];

    // Check if the testName already exists
    $req = $con->prepare("SELECT * FROM type WHERE typeName = :testName");
    $req->bindValue(':testName', $testName);
    $req->execute();
    $existingTest = $req->fetch();

    if ($existingTest) {
        echo "<script>alert('The test name already exists.');</script>";
    } else {
        // Insert the new testName and department
        $req = $con->prepare("INSERT INTO type (typeName, department) VALUES (:testName, :department)");
        $req->bindValue(':testName', $testName);
        $req->bindValue(':department', $department);
        $req->execute();
        echo "<script>alert('The test has been added successfully.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a test:</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/adminmenu.php'?>

<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Test</h1>
    <div class="mt-5">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <div>
                <label for="testName" class="block text-sm mb-2 dark:text-white">Test Name :</label>
                <input type="text" name="testName" id="testName" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Test Name" required>
            </div>
            <div class="mt-3">
                <label for="department" class="block text-sm mb-2 dark:text-white">Department :</label>
                <select name="department" id="department" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                    <option value="laboratory">Laboratory</option>
                    <option value="radiology">Radiology</option>
                </select>
            </div>
            <div class="mt-3">
                <button type="submit" name="addTest" class="mt-3 w-full px-6 py-3 rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">Add Test</button>
            </div>
        </form>
    </div>
</div>


<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
