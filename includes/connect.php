<?php

error_reporting(0);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=db_country', 'root', '');
} catch(\PDOException $error) {
    die('Tidak dapat terhubung ke database. Error: '.$error->getMessage());
}

include 'functions.php';

