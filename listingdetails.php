<?php
require_once("private/initialize.php");
date_default_timezone_set('UTC');

//Identify what is passed in the URL
$code = trim($_GET["id"]);
@$msg = trim($_GET["message"]);

$host_id = '';

$page_title = "Listing Details";
require("header.php");

if (is_post_request()) {
    $t = date("Y-m-d H:i:s");
    $sql = "INSERT INTO comments (listing_id, timestamp, username, comment) 
        VALUES (?,?,?,?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ssss",
        $code,
        $t,
        $_SESSION["valid_user"],
        $_POST["comment"]);

    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssss', $code, $t, $_SESSION["valid_user"], $_POST["comment"]);
    $res = $stmt->execute();

    // if($res){
    //     // $_SESSION["valid_user"] = $_POST["username"];
    //     // $_SESSION["neighbourhood_preference"] = $_POST["neighbourhood_preference"];
    //     // header("Location: listings.php");
    // }

    $stmt->free_result();
}
?>

<div class="page-content">
    <?php
    $listing_display_query = "SELECT * FROM listings WHERE listings.id = ?";
    $stmt = $db->prepare($listing_display_query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    $stmt->bind_param('s', $code);
    $stmt->execute();
    $search_result = $stmt->get_result();

    if (!empty($msg)) {
        echo "<p>$msg</p><br>\n";
    }

    //start the table of details
    if ($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);
        while ($row = $search_result->fetch_assoc()) {

            echo "<div class = \"listing-info\">";
            echo "<div class = \"image-column\">";

            //IMAGE
            echo "<img class=\"image-container\" src=\"" . $row["picture_url"] . "\">";
            echo "<h2>" . $row["name"] . "</h2>";
            echo "<br>";
            echo "</div>";

            echo "<div class = \"info-column\">";
            //NAME
            

            //HOST ID
            // echo "<h3>Host ID</h3>";
            // echo "<p><a href=\"host.php?id=".$row["host_id"]."\">".$row["host_id"]."</a></p>";
            $host_id = $row["host_id"];
            // echo "<br>";
    
            //DESCRIPTION
            echo "<h3>Description</h3>";
            echo "<p>" . $row["description"] . "</p>";
            echo "<br>";

            //NEIGHBOURHOOD DESCRIPTION
            echo "<h3>Neighbourhood Description</h3>";
            echo "<p>" . $row["neighborhood_description"] . "</p>";
            echo "<br>";

            //LOCATION COORDINATES
            echo "<h3>Lat and Long</h3>";
            echo "<p>Lat: " . $row["latitude"] . "</p>";
            echo "<p>Long: " . $row["longitude"] . "</p>";
            echo "<br>";

            //PROPERTY TYPE
            echo "<h3>Property Type</h3>";
            echo "<p>" . $row["property_type"] . "</p>";
            echo "<br>";

            //ROOM TYPE
            echo "<h3>Property Type</h3>";
            echo "<p>" . $row["room_type"] . "</p>";
            echo "<br>";

            //GUESTS
            echo "<h3>Accomodates</h3>";
            echo "<p>" . $row["accommodates"] . "</p>";
            echo "<br>";

            //PRICE
            echo "<h3>Price</h3>";
            echo "<p>" . $row["price"] . "</p>";
            echo "<br>";

            //NIGHTS
            echo "<h3>Max and Min Nights</h3>";
            echo "<p>Min Nights: " . $row["minimum_nights"] . "</p>";
            echo "<p>Max Nights: " . $row["maximum_nights"] . "</p>";
            echo "<br>";

            //NUMBER OF REVIEWS
            echo "<h3>Reviews</h3>";
            echo "<p>Number of Reviews: " . $row["number_of_reviews"] . "</p>";
            echo "<p>First Review: " . $row["first_review"] . "</p>";
            echo "<p>Last Review: " . $row["last_review"] . "</p>";
            echo "<br>";

            //NUMBER OF REVIEWS
            echo "<h3>Review Score Ratings</h3>";
            echo "<p>Overall Rating: " . $row["review_scores_rating"] . "</p>";
            echo "<p>Accuracy: " . $row["review_scores_accuracy"] . "</p>";
            echo "<p>Cleanliness: " . $row["review_scores_cleanliness"] . "</p>";
            echo "<p>Check-In: " . $row["review_scores_checkin"] . "</p>";
            echo "<p>Communication: " . $row["review_scores_communication"] . "</p>";
            echo "<p>Location: " . $row["review_scores_location"] . "</p>";
            echo "<p>Value: " . $row["review_scores_value"] . "</p>";
            echo "<p>Reviews Per Month: " . $row["reviews_per_month"] . "</p>";
            echo "<br>";

            //ORIGINAL POSTING
            echo "<h3>Original Posting</h3>";
            echo "<p><a href=\"" . $row["listing_url"] . "\">Click Here</a></p>";
            echo "<br>";

            echo "</div>";
            echo "</div>";

            // echo "<div id =”my-map” style = “width:800px; height:600px;”><p>This Map</p></div>";
    
        }
    }

    //if the table cannot be generated
    else {
        echo "<p>The entry cannot be found.</p>";
    }

    $stmt->free_result();

    $host_query = "SELECT * FROM hosts WHERE hosts.host_id = ?";
    $stmt = $db->prepare($host_query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    $stmt->bind_param('s', $host_id);
    $stmt->execute();
    $search_result = $stmt->get_result();

    if (!empty($msg)) {
        echo "<p>$msg</p><br>\n";
    }

    //start the table of details
    if ($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);
        while ($row = $search_result->fetch_assoc()) {

            //IMAGE
            echo "<img class=\"thumb-img\" src=\"" . $row["host_thumbnail_url"] . "\">";
            echo "<p>Airbnb Host: <a href=\"host.php?id=" . $row["host_id"] . "\">" . $row["host_name"] . "</a></p>";
            echo "<br>";
        }
    }

    //if the table cannot be generated
    else {
        echo "<p>The entry cannot be found.</p>";
    }

    $stmt->free_result();


    //////
    
    //if the product is not in the watchlist, else if there is a message to display, else if the user is logged in
    if (!is_in_watchlist($code)) {
        echo "<form action=\"addtowatchlist.php\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=$code>\n";
        echo "<input type=\"submit\" class=\"main-button\" value=\"Add To Watchlist\">\n";
        echo "</form>\n";
    }
    // else if (!empty($msg) ) {
    //     echo "<p>$msg</p>\n";
    // } 
    else if (is_logged_in()) {
        echo "<p>This listing is already in your <a href=\"profile.php\">watchlist</a>.</p><br><br>";
    }

    echo "<button class=\"main-button margin-top\"><a href=\"listings.php\">Back to All Listings</a></button>";
    ?>


    <h3>All Comments</h3><br>

    <?php
    $comment_query = "SELECT * FROM comments WHERE comments.listing_id = ?";
    $stmt = $db->prepare($comment_query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    $stmt->bind_param('s', $code);
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

            //NAME
            echo "<h5>" . $row["username"] . " | " . $row["timestamp"] . "</h5>";
            echo "<p>" . $row["comment"] . "</p>";
            echo "<br>";

            // echo "<div id =”my-map” style = “width:800px; height:600px;”><p>This Map</p></div>";
    
        }
    }

    //if the table cannot be generated
    else {
        echo "<p>There are no comments for this listing.</p><br>";
    }

    $stmt->free_result();

    if (is_logged_in()) {
        echo "<form method=\"post\">";
        echo "<h4>Submit a Comment</h4><br>";
        echo "<label>Enter Comment: </label><br>";
        echo "<textarea name=\"comment\" rows=\"5\" cols=\"60\"></textarea><br><br>";
        echo "<input type=\"submit\" id=\"submit\" value=\"Submit Comment\"/>";
        echo "</form></div><br>";
    } else {
        echo "</div><br>";
    }

    $db->close();

    include("footer.php");
    ?>