<?php
session_start();
function require_login(){
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
}
function require_admin(){
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}
?>