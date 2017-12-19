<?php
session_start();

require_once('fonctions.inc.php');

// Connexion à la base de donnée
$pdo = new PDO("mysql:host=localhost;dbname=vtc", 'root' , '', array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    // initialisation de variables
    $page = '';
    $title = '';
    $msg = '';
