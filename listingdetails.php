<?php
    require_once("private/initialize.php");

    //Identify what is passed in the URL
    $code = trim($_GET["id"]);
    @$msg = trim($_GET["message"]);

    $page_title = "Listing Details";
    require("header.php");
 ?>

<div class="page-content">
    <?php
    $listing_display_query = "SELECT * FROM listings WHERE listings.id = ?";
    $stmt = $db->prepare($listing_display_query);

    //check for query error
    if(!$stmt) {
        die("Error is:".$db->error);
    }

    $stmt->bind_param('s',$code);
    $stmt->execute();
    $search_result = $stmt->get_result();

    //start the table of details
    if($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);
        while($row = $search_result->fetch_assoc()) {

        //NAME
        // echo "<h3>Listing Name</h3>";
        echo "<h2>".$row["name"]."</h2>";
        echo "<br>";

        //DESCRIPTION
        echo "<h3>Description</h3>";
        echo "<p>".$row["description"]."</p>";
        echo "<br>";

        //NEIGHBOURHOOD DESCRIPTION
        echo "<h3>Neighbourhood Description</h3>";
        echo "<p>".$row["neighborhood_description"]."</p>";
        echo "<br>";

        //LISTING ID
        echo "<h3>Listing ID</h3>";
        echo "<p><a href=\"".$row["listing_url"]."\">Click Here</a></p>";
        echo "<br>";

        //PICTURE URL
        //TODO: something with this to display it
        echo "<h3>Picture URL</h3>";
        echo "<p><a href=\"".$row["picture_url"]."\">Click Here</a></p>";
        echo "<br>";

        //LOCATION COORDINATES
        echo "<h3>Lat and Long</h3>";
        echo "<p>Lat: ".$row["latitude"]."</p>";
        echo "<p>Long: ".$row["longitude"]."</p>";
        echo "<br>";

        //PROPERTY TYPE
        echo "<h3>Property Type</h3>";
        echo "<p>".$row["property_type"]."</p>";
        echo "<br>";

        //ROOM TYPE
        echo "<h3>Property Type</h3>";
        echo "<p>".$row["room_type"]."</p>";
        echo "<br>";

        //GUESTS
        echo "<h3>Accomodates</h3>";
        echo "<p>".$row["accommodates"]."</p>";
        echo "<br>";

        //PRICE
        echo "<h3>Price</h3>";
        echo "<p>".$row["price"]."</p>";
        echo "<br>";

        //NIGHTS
        echo "<h3>Max and Min Nights</h3>";
        echo "<p>Min Nights: ".$row["minimum_nights"]."</p>";
        echo "<p>Max Nights: ".$row["maximum_nights"]."</p>";
        echo "<br>";

        //NUMBER OF REVIEWS
        echo "<h3>Reviews</h3>";
        echo "<p>Number of Reviews: ".$row["number_of_reviews"]."</p>";
        echo "<p>First Review: ".$row["first_review"]."</p>";
        echo "<p>Last Review: ".$row["last_review"]."</p>";
        echo "<br>";

        //NUMBER OF REVIEWS
        echo "<h3>Review Score Ratings</h3>";
        echo "<p>Overall Rating: ".$row["review_scores_rating"]."</p>";
        echo "<p>Accuracy: ".$row["review_scores_accuracy"]."</p>";
        echo "<p>Cleanliness: ".$row["review_scores_cleanliness"]."</p>";
        echo "<p>Check-In: ".$row["review_scores_checkin"]."</p>";
        echo "<p>Communication: ".$row["review_scores_communication"]."</p>";
        echo "<p>Location: ".$row["review_scores_location"]."</p>";
        echo "<p>Value: ".$row["review_scores_value"]."</p>";
        echo "<p>Reviews Per Month: ".$row["reviews_per_month"]."</p>";
        echo "<br>";
        }
    }
        
    //if the table cannot be generated
    else  {
        echo "<p>The entry cannot be found.</p>";
    }

    $stmt->free_result();

    //TODO: figure out the comments

    //if the product is not in the watchlist, else if there is a message to display, else if the user is logged in
    if(!is_in_watchlist($code) ) {
        echo "<form action=\"addtowatchlist.php\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=$code>\n";
        echo "<input type=\"submit\" class=\"main-button\" value=\"Add To Watchlist\">\n";
        echo "</form>\n";
    } else if (!empty($msg) ) {
        echo "<p>$msg</p>\n";
    } else if (is_logged_in()) {
        echo "<p>This listing is already in your <a href=\"watchlist.php\">watchlist</a>.</p><br><br>";
    }

    echo "<button class=\"main-button margin-top\"><a href=\"listings.php\">Back to All Listings</a></button>";

    echo "</div><br>";

    $db->close();
    include("footer.php");
?>