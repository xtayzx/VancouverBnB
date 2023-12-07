<?php
    require_once("private/initialize.php");
    no_SSL();

    //if the user is not logged in
    if(!is_logged_in()) {
        $_SESSION["callback_url"] = "profile.php";
        header("Location: login.php");
    }

    $username = $_SESSION["valid_user"];

    if (isset($_SESSION["callback_url"]) && $_SESSION["callback_url"] == "profile.php") {
        unset($_SESSION["callback_url"]);
    }

    $query_str = "SELECT L.id, L.name ";
    $query_str .= "FROM listings L INNER JOIN watchlist W ON L.id = W.listing_id ";
    $query_str .= "WHERE W.username='$username'";
    $res = $db->query($query_str);

    $page_title = "Your Profile & Watchlist";
    require("header.php");

    echo "<div class=\"page-content\">";

    if (isset($message)) echo "<p>$message</p>";


//


    echo "<ul>\n";

    //function to generate all the products in the users watchlist
    function model_link($code,$name,$page) {
        echo "<a href=\"$page?id=$code\">$name</a>";
    }

    while ($row = $res->fetch_row()) {
        echo "<li id=\"". $row['0'] . "\">";
        model_link($row[0], $row[1],"listingdetails.php");
        //TODO - Below needs to change on what is displayed
        echo "<span class=\"quantity\">" . "</span> - Avaliable ???";
        echo "</li>\n<br>";
    };

    echo "</ul>\n";
    echo "<p id=\"msg\"></p>\n";
    echo "</div><br><br>";

    $res->free_result();
    $db->close();

    include_once("footer.php");
?>