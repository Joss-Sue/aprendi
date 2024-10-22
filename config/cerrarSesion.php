<?php
//unset($_COOKIE['correo']);
//unset($_COOKIE['contrasena']);
//setcookie('remember_user', '', -1, '/');

setcookie('correo','',-1, '/');
setcookie('contrasena','',-1, '/');
//echo '<script>alert("You have been logged out.")</script>;'
if(session_status()==PHP_SESSION_NONE){
    session_start();
}
session_destroy();

header("location:../section/index/index.php");
//header("location:testecho.php");
exit();

?>
