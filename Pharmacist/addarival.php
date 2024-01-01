<?php
session_start();
if (!($_SESSION['role'] == 'Pharmacist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

$drugID = '';
$quantity = '';
$expiryDate = '';

if (isset($_POST['addArrival'])) {
    $drugID = $_POST['drugID'];
    $quantity = $_POST['quantity'];
    $expiryDate = $_POST['expiryDate'];

    $req = $con->prepare("INSERT INTO stock (drugID, quantity, expiryDate,arrivalDate) VALUES (:drugID, :quantity, :expiryDate,:arrivalDate)");
    $req->bindValue(':drugID', $drugID);
    $req->bindValue(':quantity', $quantity);
    $req->bindValue(':expiryDate', $expiryDate);
    $req->bindValue(':arrivalDate', date('Y-m-d'));
    $req->execute();
    echo "<script>alert('New arrival added successfully.')</script>";
}

$drugs = $con->query("SELECT * FROM drug ")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Arrival</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/pharmasistmenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form action="addarival.php" method="post">
        <div>
            <label for="drugID" class="block text-sm mb-2 dark:text-white">Drug</label>
            <select name="drugID" id="drugID" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
                <?php foreach ($drugs as $drug) { ?>
                    <option value="<?php echo $drug['drugID']; ?>"><?php echo $drug['drugName']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mt-3">
            <label for="quantity" class="block text-sm mb-2 dark:text-white">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <label for="expiryDate" class="block text-sm mb-2 dark:text-white">Expiry Date</label>
            <input type="date" name="expiryDate" id="expiryDate" class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600" required>
        </div>
        <div class="mt-3">
            <button type="submit" name="addArrival" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Arrival</button>
        </div>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>