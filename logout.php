<?php
require_once 'includes/functions.php';

session_start();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit();
?>