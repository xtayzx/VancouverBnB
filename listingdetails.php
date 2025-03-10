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
        echo "<p class = \"listings-no-results\">$msg</p>\n";
    }

    //start the table of details
    if ($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);
        while ($row = $search_result->fetch_assoc()) {
    
            $host_id = $row["host_id"];

            echo "<div class = \"listing-info\">";
            echo "<button class=\"back-to-listings\"><a href=\"listings.php\"> ← Back to All Listings</a></button>";
            echo "<div class = \"image-column\">";
            
            //IMAGE
            echo "<img class=\"image-container\" src=\"" . $row["picture_url"] . "\">";
            echo "</div>";
            echo "<div class = \"info-column\">";

            //NAME
            echo "<h2 class =\"name-title\">" . $row["name"] . "</h2>";

            // ADD TO WATCHLIST
            if (!is_in_watchlist($code)) {
                echo "<form action=\"addtowatchlist.php\" method=\"post\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=$code>\n";
                echo "<input type=\"submit\" class=\"add-to-watchlist\" value=\"+ Add To Watchlist\">\n";
                echo "</form>\n";
            }

            else if (is_logged_in()) {
                echo "<a class =\"added-to-watchlist\" href=\"profile.php\">Added to watchlist</a><br><br><br>";
            }

            //PRICE
            echo "<h3 class = \"price\">" . $row["price"] . "/ day </h3>";


            //NUMBER OF REVIEWS
            $rating = floatval($row['review_scores_rating']);
            $filledStars = round($rating / 5 * 5); // Convert to a scale out of 5
            $decimal = $rating - $filledStars;
            $hasHalfStar = ($decimal >= 0.25 && $decimal < 0.75);

            $threshold = 0.25;
            echo "<div class=\"star-ratings-css\">";
            echo "<span class=\"rating-value\">" . floatval($row['review_scores_rating']) . "</span>";

            for ($i = 0; $i < 5; $i++) {
                if ($filledStars > 0) {
                    echo "<span class=\"star filled\">★</span>";
                    $filledStars--;
                } elseif ($hasHalfStar && $i == 4) {
                    echo "<span class=\"star half-filled\">★</span>";
                    $hasHalfStar = false; // To avoid displaying more than one half star
                } else {
                    echo "<span class=\"star empty\">★</span>";
                }
            }
            echo "</div>";

            //ORIGINAL POSTING
            echo "<div class = \"posting\">";
            echo "<h3>Original Posting</h3>";
            echo "<p><a href=\"" . $row["listing_url"] . "\">Click Here</a></p>";
            echo "<br>";
            echo "</div>";

            //DESCRIPTION
            echo "<h3>Description</h3>";
            echo "<p>" . $row["description"] . "</p>";
            echo "<br>";

            //NEIGHBOURHOOD DESCRIPTION
            echo "<h3>Neighbourhood Description</h3>";
            echo "<p>" . $row["neighborhood_description"] . "</p>";
            echo "<br>";


            // RATINGS
            echo "<h3>Review Score Ratings</h3>";
            $categories = [
                "Accuracy" => floatval($row["review_scores_accuracy"]),
                "Cleanliness" => floatval($row["review_scores_cleanliness"]),
                "Check-In" => floatval($row["review_scores_checkin"]),
                "Communication" => floatval($row["review_scores_communication"]),
                "Location" => floatval($row["review_scores_location"]),
                "Value" => floatval($row["review_scores_value"]),
            ];

            $maxValue = 5; // Maximum rating
    
            echo "<div class=\"reviews-graph\">";
            echo "<div class=\"reviews\">";

            foreach ($categories as $category => $rating) {
                $clampedRating = max(min($rating, 5), 0); // Ensure rating is between 0 and 5
                $filledStars = min(floor($clampedRating), 5); // Get the integer part, limited to 5 stars
                $decimal = $clampedRating - $filledStars; // Get the decimal part
    
                echo "<div class=\"bar\">";
                echo "<span class=\"category\">" . str_pad($category . ":", 15, " ", STR_PAD_RIGHT) . "</span>"; // Adjust the width as needed
    
                // Output filled stars
                for ($i = 0; $i < $filledStars; $i++) {
                    echo "★";
                }

                // Output half-filled star or empty star based on the decimal
                if ($decimal >= 0.75) {
                    echo "★";
                } elseif ($decimal >= 0.25) {
                    echo "☆";
                }

                // Output the decimal rating
                echo "<span class=\"rating-decimal\">$rating</span>";
                echo "</div>";
            }

            echo "</div>";
            echo "</div>";


            //LOCATION COORDINATES
            echo "<h3>Approximate Coordinates</h3>";
            echo "<p>Latitude: " . $row["latitude"] . "</p>";
            echo "<p>Longitude: " . $row["longitude"] . "</p>";
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

            //NIGHTS
            echo "<h3>Max and Min Nights</h3>";
            echo "<p>Min Nights: " . $row["minimum_nights"] . "</p>";
            echo "<p>Max Nights: " . $row["maximum_nights"] . "</p>";
            echo "<br>";

            // COMMENTS
            echo "<br>";
            echo "<br>";
            echo "<h3>All Comments</h3>";

            $comment_query = "SELECT * FROM comments WHERE comments.listing_id = ?";
            $stmt = $db->prepare($comment_query);

            //check for query error
            if (!$stmt) {
                die("Error is:" . $db->error);
            }

            $stmt->bind_param('s', $code);
            $stmt->execute();
            $search_result = $stmt->get_result();

            //start the table of details
            if ($search_result->fetch_row() != 0) {

                //has to go back to the first of the array
                $search_result->data_seek(0);

                while ($row = $search_result->fetch_assoc()) {
                    //NAME
                    echo "<h5>" . $row["username"] . " | " . $row["timestamp"] . "</h5>";
                    echo "<p>" . $row["comment"] . "</p>";
                    echo "<br>";
                }
            }

            //if the table cannot be generated
            else {
                echo "<p>There are no comments for this listing.</p><br>";
            }

            $stmt->free_result();

            if (is_logged_in()) {
                echo "<form method=\"post\">";
                echo "<h4>Submit a Comment</h4>";
                echo "<textarea placeholder = \"Write your comment\"class = \"comment-textbox\" name=\"comment\" rows=\"10\" cols=\"80\"></textarea><br><br>";
                echo "<input type=\"submit\" id=\"submit\" value=\"Submit Comment\" class = \"submit-edits\"/>";
                echo "</form></div><br>";
            } else {
                echo "</div><br>";
            }
            echo "</div>";
            echo "</div>";
        }
    }

    $host_query = "SELECT * FROM hosts WHERE hosts.host_id = ?";
    $stmt = $db->prepare($host_query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    $stmt->bind_param('s', $host_id);
    $stmt->execute();
    $search_result = $stmt->get_result();

    if ($search_result->fetch_row() != 0) {
        $search_result->data_seek(0);
        while ($row = $search_result->fetch_assoc()) {

            // Display the host's thumbnail image and name
            echo "<div class=\"host-listingdetails\">";
            echo "<div class=\"host-info-listingdetails\">";
            echo "<img class=\"thumb-img\" src=\"" . $row["host_thumbnail_url"] . "\">";
            echo "<div class=\"host-link\">";
            echo "<h3>Hosted by <a href=\"host.php?id=" . $row["host_id"] . "\">" . $row["host_name"] . "</a></h3>";
            echo "</div>"; 
            echo "</div>"; 
            echo "</div>"; 
        }
    } 
    
    else {
        echo "<p class = \"listings-no-results\" >The entry cannot be found.</p>";
    }

    $stmt->free_result();
    $db->close();
    include_once("footer.php");
?>