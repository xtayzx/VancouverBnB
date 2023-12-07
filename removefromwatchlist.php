<?php
    require_once("private/initialize.php");

    $code = trim($_GET["id"]);
    @$msg = trim($_GET["message"]);

    $username = $_SESSION["valid_user"];

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