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

    // Validate the uploaded file
    if ($file['type'] != 'application/pdf') {
        echo "Invalid file type. Please upload a PDF file.";
        exit;
    }

    // Move the uploaded file to the /storage/tests directory
    $destination = '../storage/tests/' . $file['name'];
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo "Failed to upload file.";
        exit;
    }
    //find the typeNAme
    $req = $con->prepare("
        SELECT type.typeName
        FROM test
        JOIN type ON test.typeID = type.typeID
        WHERE test.testID = :test_id
    ");
    $req->bindValue(':test_id', $test_id);
    $req->execute();
    $type = $req->fetch();
    //find the patientName
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
    // Rename the file
    $new_filename = 'radiology-'.$type['typeName']."-".$patient['patientName']."-".date('Ymd'). '.pdf';
    rename($destination, '../storage/tests/' . $new_filename);

    // Update the testResult, status, and testDate fields in the test table
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
    <form action="uploadresult.php" method="post" enctype="multipart/form-data">
        <label for="test_id">Select a test:</label><br>
        <select id="test_id" name="test_id">
            <?php foreach ($tests as $test) { ?>
                <option value="<?= $test['testID'] ?>"><?= $test['patientName'] . ' - ' . $test['typeName'] ?></option>
            <?php } ?>
        </select><br>
        <label for="file">Upload a PDF file:</label><br>
        <input type="file" id="file" name="file" accept="application/pdf"><br>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Upload Result</button>
    </form>
</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>