<?php
session_start();
if (!($_SESSION['role'] === 'Admin')) {
    header('location: ../error404.php');
} ?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link href="/Assets/css/tailwind.css" rel="stylesheet">
</head>
<body>

<script src="/node_modules/preline/dist/preline.js"></script>
</body>
</html>
