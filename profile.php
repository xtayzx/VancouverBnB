<?php
    require_once("private/initialize.php");
    no_SSL();

    @$msg = trim($_GET["message"]);

    //if the user is not logged in
    if(!is_logged_in()) {
        $_SESSION["callback_url"] = "profile.php";
        header("Location: login.php");
    }

    $username = $_SESSION["valid_user"];

    if (isset($_SESSION["callback_url"]) && $_SESSION["callback_url"] == "profile.php") {
        unset($_SESSION["callback_url"]);
    }

    $page_title = "Your Profile";
    require("header.php");

    echo "<div class=\"page-content\">";

    if (isset($message)) echo "<p>$message</p><br>";

    $profile_query = "SELECT first_name, last_name, email, username, neighbourhood_preference FROM users WHERE username = ?";
    $stmt = $db->prepare($profile_query);

    //check for query error
    if(!$stmt) {
        die("Error is:".$db->error);
    }

    $stmt->bind_param('s',$_SESSION["valid_user"]);
    $stmt->execute();
    $search_result = $stmt->get_result();

    if (!empty($msg) ) {
        echo "<p>$msg</p>\n";
    }

    //start the table of details
    if($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);

        while($row = $search_result->fetch_assoc()) {

        echo "<p><b>First Name: </b></p>";
        echo "<p>".$row["first_name"]."</p><br>";

        echo "<p><b>Last Name: </b></p>";
        echo "<p>".$row["last_name"]."</p><br>";

        echo "<p><b>Email: </b></p>";
        echo "<p>".$row["email"]."</p><br>";

        echo "<p><b>Username: </b></p>";
        echo "<p>".$row["username"]."</p><br>";

        echo "<p><b>Neighbourhood Preference: </b></p>";
        echo "<p>".$row["neighbourhood_preference"]."</p><br>";

        echo "<br>";
        }
    }

    else  {
        echo "<p>The information could not be displayed.</p><br>";
    }

    $stmt->free_result();

    echo "<button class=\"main-button margin-top\"><a href=\"edit.php\">Change Your Preferences</a></button>";

    ///////

    echo "<h2>Your Watchlist</h2>";

    $query_str = "SELECT L.id, L.name ";
    $query_str .= "FROM listings L INNER JOIN watchlist W ON L.id = W.listing_id ";
    $query_str .= "WHERE W.username='$username'";
    $res = $db->query($query_str);

    echo "<ul>\n";

    //function to generate all the products in the users watchlist
    function model_link($code,$name,$page) {
        echo "<a href=\"$page?id=$code\">$name</a>";
    }

    function watchlist_action($code, $name, $page) {
        echo "<a class=\"action\" href=\"$page?id=$code\">$name</a>";
    }

    while ($row = $res->fetch_row()) {
        echo "<li id=\"". $row['0'] . "\">";
        model_link($row[0], $row[1],"listingdetails.php");
        
        //SOMETHING HERE TO HELP DIFFERENTICATE

        echo "    -      ";
        watchlist_action($row[0], "Remove", "removefromwatchlist.php");
        echo "</li>\n<br>";
    };

    echo "</ul>\n";
    echo "<p id=\"msg\"></p>\n";
    echo "</div><br><br>";

    $res->free_result();
    $db->close();

    include_once("footer.php");
?>