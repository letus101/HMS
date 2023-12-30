<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}

require_once '../config/cnx.php';
$con =cnx_pdo();
$doctor_id = $_SESSION['id'];
$req = $con->prepare("
    SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName
    FROM inpatient 
    INNER JOIN patient ON inpatient.patientID = patient.patientID 
    WHERE inpatient.status ='Admitted' 
    AND inpatient.admissionDate IN (
        SELECT visit.visitDate 
        FROM visit 
        WHERE visit.appointmentID IN (
            SELECT appointment.appointmentID 
            FROM appointment 
            WHERE appointment.userID = :doctor_id
        )
    )
");
$req->bindValue(':doctor_id', $doctor_id);
$req->execute();
$inpatients = $req->fetchAll();$req->execute();
$inpatients = $req->fetchAll();

if (isset($_POST['submit'])) {
    $inpatient_id = $_POST['inpatients'];
    $req = $con->prepare("
        SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName
        FROM inpatient 
        INNER JOIN patient ON inpatient.patientID = patient.patientID 
        WHERE inpatient.inpatientID = :inpatient_id
    ");
    $req->bindValue(':inpatient_id', $inpatient_id);
    $req->execute();
    $inpatient = $req->fetch();
    $req = $con->prepare("
        SELECT visitID
        FROM visit
        WHERE visitDate = (
            SELECT admissionDate
            FROM inpatient
            WHERE inpatientID = :inpatient_id
        )
    ");
    $req->bindValue(':inpatient_id', $inpatient_id);
    $req->execute();
    $visit = $req->fetch();

    if ($visit) {
        $visit_id = $visit['visitID'];

        // Fetch the prescription ID
        $req = $con->prepare("
            SELECT prescriptionID
            FROM prescription
            WHERE visitID = :visit_id
        ");
        $req->bindValue(':visit_id', $visit_id);
        $req->execute();
        $prescription = $req->fetch();

        if ($prescription) {
            $prescription_id = $prescription['prescriptionID'];

            // Fetch the prescription details
            $req = $con->prepare("
                SELECT drug.drugName, prescriptiondetails.dose, prescriptiondetails.frequency
                FROM prescriptiondetails
                JOIN drug ON prescriptiondetails.drugID = drug.drugID
                WHERE prescriptiondetails.prescriptionID = :prescription_id
            ");
            $req->bindValue(':prescription_id', $prescription_id);
            $req->execute();
            $prescription_details = $req->fetchAll();

            // Fetch the diagnosis
            $req = $con->prepare("
                SELECT diagnosis
                FROM visit
                WHERE visitID = :visit_id
            ");
            $req->bindValue(':visit_id', $visit_id);
            $req->execute();
            $diagnosis = $req->fetch();

            // Fetch the tests
            $req = $con->prepare("
                SELECT *
                FROM test
                WHERE visitID = :visit_id
            ");
            $req->bindValue(':visit_id', $visit_id);
            $req->execute();
            $tests = $req->fetchAll();

            // Now you have the prescription details in the $prescription_details variable,
            // the diagnosis in the $diagnosis variable,
            // and the tests in the $tests variable
        } else {
            echo "No prescription found for the selected inpatient.";
        }
    } else {
        echo "No visit found for the selected inpatient.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form action="followup.php" method="post">
    <label for="inpatients">Select an inpatient:</label>
    <select id="inpatients" name="inpatients">
        <?php foreach ($inpatients as $inpatient) { ?>
            <option value="<?= $inpatient['inpatientID'] ?>"><?= $inpatient['patientName'] ?></option>
        <?php } ?>
    </select>
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" name="submit">Submit</button>
    </form>

</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>
