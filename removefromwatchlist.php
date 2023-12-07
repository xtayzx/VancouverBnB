<?php
    require_once("private/initialize.php");

    $code = trim($_GET["id"]);
    @$msg = trim($_GET["message"]);

    // $id = !empty($_POST["id"]) ? $_POST["id"] : "";

    //if not logged in when pressed, remember page to go back afterwards
    // if(!is_logged_in()) {
    //     $_SESSION["callback_url"] = "addtowatchlist.php";
    //     $_SESSION["id"] = $id;
    //     redirect_to("login.php");
    // } 

    $username = $_SESSION["valid_user"];

    //set the variables
    // if (isset($_SESSION["callback_url"]) && $_SESSION["callback_url"] == "addtowatchlist.php") {
    //     $id = $_SESSION["id"];
    //     unset($_SESSION["callback_url"],$_SESSION["id"]);
    // }

    $message = "";

    if (is_in_watchlist($code)) {
        $query = "DELETE FROM watchlist WHERE listing_id = ? AND username = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss',$code, $username);
        $stmt->execute();
                
        $message = urlencode("The model you selected has been removed from your <a href=\"profile.php\">watchlist</a>.");
    }
    
    //fetch the watchlist for the user
    redirect_to("profile.php?message=$message");
?>