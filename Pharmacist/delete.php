<?php
require_once '../config/cnx.php';
$con = cnx_pdo();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $req = $con->prepare("DELETE FROM stock WHERE stockID = ?");
    $req->execute([$id]);
    header('location: dashboard.php');
    exit();
}