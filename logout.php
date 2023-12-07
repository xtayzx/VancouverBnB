<?php
    require_once("private/initialize.php");
    unset($_SESSION["valid_user"]);
    unset($_SESSION["neighbourhood_preference"]);
    redirect_to("login.php");
?>