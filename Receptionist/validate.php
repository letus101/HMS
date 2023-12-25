<?php
session_start();
if (!($_SESSION['role'] == 'Receptionist')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_POST['validateappointment'])){
    $doctorID = $_POST['doctor'];
    $firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    $appointmentDate = $_SESSION['appointmentDate'];
    $appointmentTime = $_SESSION['appointmentTime'];
    $description = $_SESSION['description'];
    $req = $con->prepare("INSERT INTO appointment (userID, patientID, appointmentDate, appointmentTime, appointmentDescription,status) VALUES (:doctorID, (SELECT patientID FROM patient WHERE firstName = :firstName AND lastName = :lastName), :appointmentDate, :appointmentTime, :description ,:status)");
    $req->bindValue(':doctorID', $doctorID);
    $req->bindValue(':firstName', $firstName);
    $req->bindValue(':lastName', $lastName);
    $req->bindValue(':appointmentDate', $appointmentDate);
    $req->bindValue(':appointmentTime', $appointmentTime);
    $req->bindValue(':description', $description);
    $req->bindValue(':status', 'Scheduled');
    $req->execute();
    header('location: dashboard.php');
}
