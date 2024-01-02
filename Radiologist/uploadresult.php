<?php
session_start();
if (!($_SESSION['role'] == 'Radiologist')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con = cnx_pdo();

$req = $con->prepare("
    SELECT test.* , concat(p.firstName,' ',p.lastName) AS patientName , t.typeName
    FROM test JOIN hms.visit v on v.visitID = test.visitID
    JOIN hms.type t on t.typeID = test.typeID
    JOIN hms.appointment a on v.appointmentID = a.appointmentID
    JOIN hms.patient p on a.patientID = p.patientID
    WHERE test.status = 'Scheduled' AND t.department = 'radiology'
    ORDER BY test.testID
");
$req->execute();
$tests = $req->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_id'], $_FILES['file'])) {
    $test_id = $_POST['test_id'];
    $file = $_FILES['file'];

    if ($file['type'] != 'application/pdf') {
        echo "Invalid file type. Please upload a PDF file.";
        exit;
    }

    $destination = '../storage/tests/' . $file['name'];
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo "Failed to upload file.";
        exit;
    }
    $req = $con->prepare("
        SELECT type.typeName
        FROM test
        JOIN type ON test.typeID = type.typeID
        WHERE test.testID = :test_id
    ");
    $req->bindValue(':test_id', $test_id);
    $req->execute();
    $type = $req->fetch();
    $req = $con->prepare("
        SELECT concat(patient.firstName,' ',patient.lastName) AS patientName
        FROM test
        JOIN visit ON test.visitID = visit.visitID
        JOIN appointment ON visit.appointmentID = appointment.appointmentID
        JOIN patient ON appointment.patientID = patient.patientID
        WHERE test.testID = :test_id
    ");
    $req->bindValue(':test_id', $test_id);
    $req->execute();
    $patient = $req->fetch();
    $new_filename = 'radiology-'.$type['typeName']."-".$patient['patientName']."-".date('Ymd'). '.pdf';
    rename($destination, '../storage/tests/' . $new_filename);

    $req = $con->prepare("
        UPDATE test
        SET testResult = :testResult, status = 'Completed', testDate = CURDATE()
        WHERE testID = :test_id
    ");
    $req->bindValue(':testResult', $new_filename);
    $req->bindValue(':test_id', $test_id);
    $req->execute();

    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Result</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/radiomenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Upload Result</h1>
    <form action="uploadresult.php" method="post" enctype="multipart/form-data" class="space-y-4 mt-3">
        <?php if (!empty($tests)): ?>
            <div class="mt-3">
                <label for="test_id" class="block text-l mb-2 dark:text-white">Select a test:</label>
                <select id="test_id" name="test_id" class="p-3 border border-gray-300 rounded">
                    <?php foreach ($tests as $test): ?>
                        <option value="<?= $test['testID'] ?>"><?= $test['patientName'] . ' - ' . $test['typeName'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mt-3">
                <label for="file" class="block text-l mb-2 dark:text-white">Upload a PDF file:</label>
                <input type="file" id="file" name="file" accept="application/pdf" class="p-2 border border-gray-300 rounded">
            </div>
            <div class="mt-3">
                <button type="submit" class="p-2 bg-blue-500 text-white rounded">Upload Result</button>
            </div>
        <?php else: ?>
            <p class="text-l dark:text-white">No Scheduled tests available.</p>
        <?php endif; ?>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>