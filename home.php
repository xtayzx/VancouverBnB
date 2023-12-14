<?php
    require_once("private/initialize.php");

    if (is_logged_in() && isset($_SESSION["neighbourhood_preference"])) {
        //displaying the query on the page with the filled values (which will be added to the string later)
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.picture_url";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.neighbourhood = ? ";
        $query = $display_query . $str_from . $str_where . " GROUP BY listings.id ASC LIMIT 20";
    } 

    else if (empty($_SESSION["neighbourhood_preference"])) {
        //display results of those that are the most popular
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.review_scores_rating, listings.picture_url";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.review_scores_rating = ?";
        $query = $display_query . $str_from . $str_where . " GROUP BY listings.id ASC LIMIT 20";
    }

    $page_title = "Welcome";
    no_SSL();
    require("header.php");

    $stmt = $db->prepare($query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    //BIND VALUES
    //for the order number, insert 1 variable
    if (isset($_SESSION["neighbourhood_preference"])) {
        $neighbourhoodPref = $_SESSION["neighbourhood_preference"];
        $stmt->bind_param('s', $neighbourhoodPref);
    } 

    else if (empty($_SESSION["neighbourhood_preference"])) {
        $rating = "5";
        $stmt->bind_param('s', $rating);
    }


    $stmt->execute();
    $search_result = $stmt->get_result();

    if ($search_result->num_rows > 0) {
        $count = 0; // Counter for tracking cards in a row
        if (is_logged_in()) {
            echo "<h1 class = \"welcome-title\">Here are some recommendations in your preferred neighbourhood!</h3><br>";
        } else {
            echo '<h1 class = "welcome-title">Welcome</h1>';
        }
        start_cards_container(); // Start the cards container

        while ($row = $search_result->fetch_assoc()) {
            if ($count % 5 == 0) {
                echo '<div class="card-row">'; // Start a new row for every fifth card
            }

            // Display each listing as a card
            display_listing_card($row['id'], $row['picture_url'], $row['name'], $row['neighbourhood'], $row['price']);

            $count++;

            if ($count % 5 == 0) {
                echo '</div>'; // Close the row after every fifth card
            }
        }

        // Check if the last row is incomplete and close it
        if ($count % 5 !== 0) {
            echo '</div>';
        }

        end_cards_container(); // End of cards container
    } 

    else {
        echo "<h3 class = \"listings-no-results\">Wow. Such empty.</p>";
        echo "<h3 class = \"listings-no-results\">You currently do not have any neighbourhood preferences set to your account. Go to your profile and edit your preferences</p>";
    }

    $stmt->free_result();
    $db->close();
    include_once("footer.php");
?>