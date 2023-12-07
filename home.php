<?php
    require_once("private/initialize.php");

    if(is_logged_in() && isset($_SESSION["neighbourhood_preference"])) {
        //displaying the query on the page with the filled values (which will be added to the string later)
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.neighbourhood = ? ";

        //creating the prepared statement
        //TODO: How to pagenate the query rather than just having 50
        $query = $display_query.$str_from.$str_where." GROUP BY listings.id ASC LIMIT 20";
    }

    else if (empty($_SESSION["neighbourhood_preference"])){
        //display results of those that are the most popular
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.review_scores_rating";
        $str_from = " FROM listings";
        $str_where = " WHERE listings.review_scores_rating = ?";

        //creating the prepared statement
        //TODO: How to pagenate the query rather than just having 50
        $query = $display_query.$str_from.$str_where." GROUP BY listings.id ASC LIMIT 20";
    }     

    $page_title = "Welcome";
    no_SSL();
    require("header.php");

            if(is_logged_in()) {
                echo "<h3>Here are some recommendations in your preferred neighbourhood!</h3><br>";
            }

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
            if($search_result->fetch_row() !=0) {

                table_header();

                //create the table rows
                while($row = $search_result->fetch_assoc()) {

                    table_contents($row['id'], $row['name'], $row['neighbourhood'], $row['price']);
                }

                table_end();
            }
          
            else  {
                echo "<p>The entry cannot be found</p>";
            }

            $stmt->free_result();

    $db->close();
    include_once("footer.php");
?>