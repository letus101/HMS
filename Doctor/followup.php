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
$inpatients = $req->fetchAll();

if (isset($_POST['submit']) && isset($_POST['inpatients'])) {
    $inpatient_id = $_POST['inpatients'];
    $req = $con->prepare("
        SELECT inpatient.*, concat(patient.firstName,' ',patient.lastName) AS patientName , patient.patientID
        FROM inpatient
        INNER JOIN patient ON inpatient.patientID = patient.patientID
        WHERE inpatient.inpatientID = :inpatient_id
    ");
    $req->bindValue(':inpatient_id', $inpatient_id);
    $req->execute();
    $inp = $req->fetch();
    $req = $con->prepare("
    SELECT visitID
    FROM visit
    WHERE appointmentID = (
        SELECT appointmentID
        FROM appointment
        WHERE patientID = :patient_id
        ORDER BY appointmentDate DESC
        LIMIT 1
    )
");
    $req->bindValue(':patient_id', $inp['patientID']);
    $req->execute();
    $visits = $req->fetchAll();

    foreach ($visits as $visit) {
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
                SELECT drug.drugName, prescriptiondetails.dose, prescriptiondetails.frequency, prescriptiondetails.prescriptionID, prescriptiondetails.drugID
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
                FROM test join type on test.typeID = type.typeID
                WHERE visitID = :visit_id
            ");
            $req->bindValue(':visit_id', $visit_id);
            $req->execute();
            $tests = $req->fetchAll();

            $req = $con->prepare("
                SELECT dailycheckup.*, v.*, v2.vitalName
                FROM dailycheckup JOIN hms.vitaldetails v on dailycheckup.checkupID = v.checkupID
                JOIN hms.vitals v2 on v2.vitalID = v.vitalID
                WHERE inpatientID = :inpatient_id AND checkupDate >= :admission_date
            ");
            $req->bindValue(':inpatient_id', $inpatient_id);
            $req->bindValue(':admission_date', $inp['admissionDate']);
            $req->execute();
            $vitals = $req->fetchAll();
        } else {
            echo "No prescription found for the selected inpatient.";
        }
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
    <?php if (isset($inp) && isset($prescription_details) && isset($diagnosis) && isset($tests)) { ?>
        <div>
            <h2>Patient Information</h2>
            <p>Name: <?= $inp['patientName'] ?></p>
            <p>Admission Date: <?= $inp['admissionDate'] ?></p>
            <h2>Prescription Details</h2>
            <?php foreach ($prescription_details as $detail) { ?>
                <p>Drug: <?= $detail['drugName'] ?></p>
                <p>Dose: <?= $detail['dose'] ?></p>
                <p>Frequency: <?= $detail['frequency'] ?></p>
                <form action="edit_prescription.php" method="post">
                    <input type="hidden" name="prescription_id" value="<?= $detail['prescriptionID'] ?>">
                    <input type="hidden" name="drug_id" value="<?= $detail['drugID'] ?>">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit Prescription</button>
                </form>
            <?php } ?>
            <h2>Diagnosis</h2>
            <p><?= $diagnosis['diagnosis'] ?></p>
            <h2>Tests</h2>
            <?php foreach ($tests as $test) { ?>
                <p>Test ID: <?= $test['typeName'] ?></p>
                <?php if ($test['status'] == 'Scheduled') { ?>
                    <p>Test Result: Not done yet</p>
                <?php } elseif ($test['status'] == 'Completed') { ?>
                    <p>Test Result: <a href="<?= '../storage/tests/' . $test['testResult'] ?>"><?= $test['testResult'] ?></a></p>
                <?php } ?>
            <?php } ?>
            <h2>Vitals</h2>
            <?php foreach ($vitals as $vital) { ?>
                <p><?= $vital['vitalName'] ?>: <?= $vital['vitalValue'] ?></p>
            <?php } ?>
        </div>
    <?php } ?>

</div>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>