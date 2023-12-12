<?php
    require_once("private/initialize.php");

    // $start = 0;
    // $rows_per_page = 20;

    // $records = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.review_scores_rating, listings.picture_url FROM listings";
    // $records_result = $db->query($records);
    // // $records_row = $records_result->fetch_assoc();
    
    // $nr_of_rows = $records_result->num_rows;
    // $pages = ceil($nr_of_rows /$rows_per_page);

    // if(isset($_GET['page-nr'])) {
    //     $page = $_GET['page-nr'] - 1;
    //     $start = $page * $rows_per_page;
    // }

    // $records_result->free_result();

    if(is_logged_in() && isset($_SESSION["neighbourhood_preference"])) {
        //displaying the query on the page with the filled values (which will be added to the string later)
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.picture_url";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.neighbourhood = ? ";

        //creating the prepared statement
        //TODO: How to pagenate the query rather than just having 50
        $query = $display_query.$str_from.$str_where." GROUP BY listings.id ASC LIMIT 20";
    }

    else if (empty($_SESSION["neighbourhood_preference"])){
        //display results of those that are the most popular
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.review_scores_rating, listings.picture_url";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.review_scores_rating = ?";

        //creating the prepared statement
        //TODO: How to pagenate the query rather than just having 50
        $query = $display_query.$str_from.$str_where." GROUP BY listings.id ASC LIMIT 20"; 
    }
    
    $page_title = "Welcome";
    no_SSL();
    require("header.php");

    // if(isset($_GET['page-nr'])) {
    //     $id = $_GET['page-nr'];
    // }
    // else {
    //     $id = 1;
    // }

            if(is_logged_in()) {
                echo "<h3>Here are some recommendations in your preferred neighbourhood!</h3><br>";
            }

            // echo "<div id=$id>";

            $stmt = $db->prepare($query);

            //check for query error
            if(!$stmt) {
                die("Error is:".$db->error);
            }

            //BIND VALUES
            //for the order number, insert 1 variable
            if(isset($_SESSION["neighbourhood_preference"])){
                $neighbourhoodPref = $_SESSION["neighbourhood_preference"];
                $stmt->bind_param('s', $neighbourhoodPref);
                // $show_query = $display_query.$str_from." WHERE listings.neighbourhood = ".$neighbourhoodPref;
            }

            else if (empty($_SESSION["neighbourhood_preference"])) {
                $rating = "5";
                $stmt->bind_param('s', $rating);
            }


            $stmt->execute();
            $search_result = $stmt->get_result();            

            //START THE TABLE
            // if($search_result->fetch_row() !=0) {

            //     table_header();

            //     //create the table rows
            //     while($row = $search_result->fetch_assoc()) {

            //         table_contents($row['id'], $row['picture_url'], $row['name'], $row['neighbourhood'], $row['price']);
            //     }

            //     table_end();
            // }
          
            // else  {
            //     echo "<p>The entry cannot be found</p>";
            // }

            if ($search_result->num_rows > 0) {
                $count = 0; // Counter for tracking cards in a row
                
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
            } else {
                echo "<p>No listings found.</p>";
            }

            // echo "</div>";
            
            $stmt->free_result();
            ?>


<?php

    $db->close();
    include_once("footer.php");
?>