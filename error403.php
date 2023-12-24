<?php
 session_start();
 if (!isset($_SESSION['role'])){
     $location = 'index.php';
 } else{
     $location = $_SESSION['role'].'/dashboard.php';}   ;
?>
<head>
    <meta charset="UTF-8">
    <title>404</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link href="Assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="flex h-full">
<div class="max-w-[50rem] flex flex-col mx-auto w-full h-full">
    <header class="mb-auto flex justify-center z-50 w-full py-4">
        <nav class="px-4 sm:px-6 lg:px-8" aria-label="Global">
            <a class="flex-none text-xl font-semibold sm:text-3xl dark:text-white" href="index.php" aria-label="Brand">Hospitalink</a>
        </nav>
    </header>

    <div class="text-center py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="block text-9xl font-bold text-gray-800 sm:text-9xl dark:text-white">403</h1>
        <h1 class="block text-2xl font-bold text-white"></h1>
        <p class="mt-3 text-gray-600 dark:text-gray-400">Oops, something went wrong.</p>
        <p class="text-gray-600 dark:text-gray-400">Sorry, you are not allowed here.</p>
        <div class="mt-5 flex justify-center items-center">
            <a href="<?=$location?>" class="ml-5 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Go Back</a>
        </div>

    </div>

    <footer class="mt-auto text-center py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-gray-500">© All Rights Reserved. 2022.</p>
        </div>
    </footer>
</div>
<script src="./node_modules/preline/dist/preline.js"></script>
</body>