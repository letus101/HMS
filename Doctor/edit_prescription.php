<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con =cnx_pdo();

if (isset($_POST['prescription_id']) && isset($_POST['drug_id'])) {
    $prescription_id = $_POST['prescription_id'];
    $drug_id = $_POST['drug_id'];

    $req = $con->prepare("
        SELECT prescriptiondetails.drugID, prescriptiondetails.dose, prescriptiondetails.frequency
        FROM prescriptiondetails
        WHERE prescriptiondetails.prescriptionID = :prescription_id AND prescriptiondetails.drugID = :drug_id
    ");
    $req->bindValue(':prescription_id', $prescription_id);
    $req->bindValue(':drug_id', $drug_id);
    $req->execute();
    $prescription_details = $req->fetch();

    $req = $con->prepare("SELECT * FROM drug");
    $req->execute();
    $drugs = $req->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drugID'], $_POST['dose'], $_POST['frequency'])) {
        $new_drugID = $_POST['drugID'] != "" ? $_POST['drugID'] : $prescription_details['drugID'];

        if ($new_drugID == $prescription_details['drugID']) {
            // If the drug ID is the same, update only the dose and frequency
            $req = $con->prepare("
                UPDATE prescriptiondetails
                SET dose = :dose, frequency = :frequency
                WHERE prescriptionID = :prescription_id AND drugID = :drugID
            ");
            $req->bindValue(':dose', $_POST['dose']);
            $req->bindValue(':frequency', $_POST['frequency']);
            $req->bindValue(':prescription_id', $prescription_id);
            $req->bindValue(':drugID', $new_drugID);
            $req->execute();
        } else {
            // If the drug ID is different, update all fields
            $req = $con->prepare("
                UPDATE prescriptiondetails
                SET drugID = :new_drugID, dose = :dose, frequency = :frequency
                WHERE prescriptionID = :prescription_id AND drugID = :old_drugID
            ");
            $req->bindValue(':new_drugID', $new_drugID);
            $req->bindValue(':dose', $_POST['dose']);
            $req->bindValue(':frequency', $_POST['frequency']);
            $req->bindValue(':prescription_id', $prescription_id);
            $req->bindValue(':old_drugID', $drug_id);
            $req->execute();
        }

        header('Location: followup.php');
        exit;
    }
} else {
    header('Location: followup.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prescription</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form action="edit_prescription.php" method="post">
        <input type="hidden" name="prescription_id" value="<?= $prescription_id ?>">
        <input type="hidden" name="drug_id" value="<?= $drug_id ?>">
        <label for="drugID">Drug (optional):</label><br>
        <select id="drugID" name="drugID">
            <option value="">--Select a drug--</option>
            <?php foreach ($drugs as $drug) { ?>
                <option value="<?= $drug['drugID'] ?>" <?= $drug['drugID'] == $prescription_details['drugID'] ? 'selected' : '' ?>><?= $drug['drugName'] ?></option>
            <?php } ?>
        </select><br>
        <label for="dose">Dose:</label><br>
        <input type="text" id="dose" name="dose" value="<?= $prescription_details['dose'] ?>"><br>
        <label for="frequency">Frequency:</label><br>
        <input type="text" id="frequency" name="frequency" value="<?= $prescription_details['frequency'] ?>"><br>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Prescription</button>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>