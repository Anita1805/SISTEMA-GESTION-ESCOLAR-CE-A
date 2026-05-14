<?php
/**
 * Created by PhpStorm.
 * User: Ana Sofia Vega
 * Date: 28/11/2025
 * Time: 19:57
 */
include ('../app/config.php');

session_start();

if(isset($_SESSION['sesion_email'])){
    session_destroy();
    header('Location: '.APP_URL.'/login');
}
