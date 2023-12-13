<?php
require_once("private/initialize.php");
no_SSL();

@$msg = trim($_GET["message"]);

//if the user is not logged in
if (!is_logged_in()) {
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

if (isset($message))
    echo "<p>$message</p><br>";

$profile_query = "SELECT first_name, last_name, email, username, neighbourhood_preference FROM users WHERE username = ?";
$stmt = $db->prepare($profile_query);

//check for query error
if (!$stmt) {
    die("Error is:" . $db->error);
}

$stmt->bind_param('s', $_SESSION["valid_user"]);
$stmt->execute();
$search_result = $stmt->get_result();

if (!empty($msg)) {
    echo "<p>$msg</p>\n";
}

//start the table of details
if ($search_result->fetch_row() != 0) {

    //has to go back to the first of the array
    $search_result->data_seek(0);

    while ($row = $search_result->fetch_assoc()) {

        //PROFILE
        echo "<div class =\"profile\">";
        echo "<div class =\"profile-card\">";
        echo "<img class =\"profile-img\" src=\"image/profile.png\" width=\"40\" height=\"auto\" style=\"width: 100px; height: auto;\">";
        echo "<p><b>First Name: </b></p>";
        echo "<p>" . $row["first_name"] . "</p><br>";
        $first_name = $row["first_name"];

        echo "<p><b>Last Name: </b></p>";
        echo "<p>" . $row["last_name"] . "</p><br>";
        $last_name = $row["last_name"];

        echo "<p><b>Email: </b></p>";
        echo "<p>" . $row["email"] . "</p><br>";

        echo "<p><b>Username: </b></p>";
        echo "<p>" . $row["username"] . "</p><br>";

        echo "<p><b>Neighbourhood Preference: </b></p>";
        echo "<p>" . $row["neighbourhood_preference"] . "</p><br>";

        echo "<br>";
        echo "<button class=\"edit-profile-button\"><a href=\"edit.php\">Change Your Preferences</a></button>";
        echo "</div>";

        //WATHCLIST
       
        echo "<div class=\"profile-watchlist-container\">";
        echo "<h2 class=\"profile-watchlists\" style=\"text-align: center;\">Your Watchlist</h2>";
        echo "<div class=\"watchlist-cards\">";

        $query_str = "SELECT L.id, L.name, L.picture_url, L.neighbourhood, L.price ";
        $query_str .= "FROM listings L INNER JOIN watchlist W ON L.id = W.listing_id ";
        $query_str .= "WHERE W.username='$username'";
        $res = $db->query($query_str);

        $count = 0;
        while ($row = $res->fetch_assoc()) {
            echo "<div class=\"column\" style=\"width: 10%; margin: 10px;\">"; // Adjust width and margins as needed
            display_watchlist_card($row['id'], $row['picture_url'], $row['name'], $row['neighbourhood'], $row['price']);
            watchlist_action($row['id'], "Remove", "removefromwatchlist.php");
            echo "</div>";
            $count++;
        }

        echo "</div>"; // Close watchlist-cards
        echo "</div>";



    }
} else {
    echo "<p>The information could not be displayed.</p><br>";
}

$stmt->free_result();
// echo "<p>$first_name</p>";
// echo "<p>$last_name</p>";

// echo "<form action=\"edit.php\" method=\"post\">\n";
// echo "<input type=\"hidden\" name=\"first_name\" value=$first_name>\n";
// echo "<input type=\"hidden\" name=\"last_name\" value=$last_name>\n";
// echo "<input type=\"submit\" class=\"main-button\" value=\"Change Your Preferences\">\n";
// echo "</form>\n";


///////




// echo "</ul>\n";
// echo "<p id=\"msg\"></p>\n";
echo "</div>";

$res->free_result();
$db->close();

include_once("footer.php");
?>