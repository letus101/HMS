<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();

$firstName = '';
$lastName = '';
$appointmentDate = '';
$appointmentTime = '';
$description = '';
$patient = '';

if (isset($_POST['addAppointment'])) {
    $firstName = strtolower($_POST['firstName']);
    $lastName = strtolower($_POST['lastName']);
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $description = $_POST['description'];

    $_SESSION['firstName'] = $firstName;
    $_SESSION['lastName'] = $lastName;
    $_SESSION['appointmentDate'] = $appointmentDate;
    $_SESSION['appointmentTime'] = $appointmentTime;
    $_SESSION['description'] = $description;
    $req = $con->prepare("SELECT * FROM patient WHERE firstName = :firstName AND lastName = :lastName");
    $req->bindValue(':firstName', $firstName);
    $req->bindValue(':lastName', $lastName);
    $req->execute();
    $patient = $req->fetch();
    if (!($patient)) {
        header('location: addappointment.php?error=patientnotfound');
        exit();
    }
}
    $req = $con->prepare("Select * FROM user WHERE roleID = 2 AND status = 'active' AND userID NOT IN (SELECT userID FROM appointment WHERE appointmentTime = :appointmentTime)");
    $req->bindValue(':appointmentTime', $appointmentTime);
    $req->execute();
    $doctors = $req->fetchAll();

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
    <form action="validate.php" method="post">
        <div>
            <label class="block text-2xl mb-2 dark:text-white"> available doctors :
                <select name="doctor" class="mt-1.5 py-3 px-4 pe-9 block w-full border-gray-200 rounded-full text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                    <?php
                    foreach ($doctors as $doctor) { // Loop through all doctors
                        echo "<option value='".$doctor['userID']."'>".$doctor['firstName']." ".$doctor['lastName']."</option>";
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="mt-3">
            <button type="button" name="goBack" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" onclick="window.location.href='addappointment.php'">Go Back</button>
            <button type="submit" name="validateappointment" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Appointment</button>
        </div>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>