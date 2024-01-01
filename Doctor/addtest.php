<?php
session_start();
if (!($_SESSION['role'] == 'Doctor')) {
    header('location: ../error403.php');
    exit();
}
require_once '../config/cnx.php';
$con = cnx_pdo();
$req = $con->prepare("select * from type");
$req->execute();
$types = $req->fetchAll();
$visitID = $_SESSION['visitID'];

if(isset($_POST['submit'])){
    $numTests = $_POST['numTests'];
    $testTypes = $_POST['testType'];
    $str = 'Scheduled';
    for($i = 0; $i < $numTests; $i++) {
        $req = $con->prepare("insert into test (visitID, typeID,status) values (:visitID,:typeID,:status)");
        $req->bindParam(':visitID', $visitID);
        $req->bindParam(':typeID', $testTypes[$i]);
        $req->bindParam(':status', $str);
        $req->execute();
    }
    header('location: ../Doctor/dashboard.php?success=visit');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link href="../Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900">
<?php require '../Assets/components/header.php'?>
<?php require '../Assets/components/doctormenu.php'?>
<div class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
    <form method="post" action="addtest.php" class="space-y-4">
        <div class="mt-3">
            <label class="block text-l mb-2 dark:text-white">Number of tests:
                <input type="number" id="numTests" name="numTests" min="1" onchange="addFields()" class="p-2 border border-gray-300 rounded">
            </label>
        </div>
        <div id="testFields" class="mt-3"></div>
        <div class="mt-3">
            <button type="submit" name="submit" class="p-2 bg-blue-500 text-white rounded">Submit</button>
        </div>
    </form>
</div>
<script>
    function addFields() {
        var container = document.getElementById("testFields");
        container.innerHTML = "";
        var numTests = document.getElementById("numTests").value;
        for (var i = 0; i < numTests; i++) {
            var label2 = document.createElement("label");
            label2.innerHTML = "Test " + (i + 1) + " type:";
            label2.className = "block text-l mb-2 dark:text-white";
            var select = document.createElement("select");
            select.name = "testType[" + i + "]";
            select.className = "p-2 border border-gray-300 rounded";
            <?php foreach ($types as $type): ?>
            var option = document.createElement("option");
            option.value = "<?= $type['typeID'] ?>";
            option.text = "<?= $type['typeName'] ?>";
            select.appendChild(option);
            <?php endforeach; ?>
            container.appendChild(label2);
            container.appendChild(select);
        }
    }
</script>
<script src="../node_modules/preline/dist/preline.js"></script>
</body>
</html>