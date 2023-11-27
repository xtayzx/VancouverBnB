<?php
    require_once("private/initialize.php");

    $id = !empty($_POST["id"]) ? $_POST["id"] : "";

    //if not logged in when pressed, remember page to go back afterwards
    if(!is_logged_in()) {
        $_SESSION["callback_url"] = "addtowatchlist.php";
        $_SESSION["id"] = $id;
        redirect_to("login.php");
    } 

    $username = $_SESSION["valid_user"];

    //set the variables
    if (isset($_SESSION["callback_url"]) && $_SESSION["callback_url"] == "addtowatchlist.php") {
        $id = $_SESSION["id"];
        unset($_SESSION["callback_url"],$_SESSION["id"]);
    }

    $message = "";
    if (!is_in_watchlist($id)) {
        $query = "INSERT INTO watchlist (username, listing_id) VALUES (?,?)";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss',$username,$id);
        $stmt->execute();
                
        $message = urlencode("The model has been added to your <a href=\"watchlist.php\">watchlist</a>.");
    }
    
    //fetch the watchlist for the user
    redirect_to("listingdetails.php?id=$id&message=$message");
?>