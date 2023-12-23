<?php
include "env.php";
function cnx_pdo(){
    $dsn = "mysql:dbname=".DBNAME.";host=".DBHOST;
    try{
        return new PDO($dsn,DBUSER,DBPASS);

    }catch(PDOException $e){
        die($e->getMessage());
    }
}
?>