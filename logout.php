<?php
    require_once("private/initialize.php");
    unset($_SESSION["valid_user"]);
    redirect_to("login.php");
?>