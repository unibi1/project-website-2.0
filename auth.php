<?php
session_start();

function wajibLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function userLogin() {
    return isset($_SESSION['user_id']);
}
?>