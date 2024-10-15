<?php

session_start();

if(!isset($_SESSION['usuario_id'])){

    header("location:../login/login.php");
    exit();
}
?>