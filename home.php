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
        //collect the preference of the neighbourhood


        // $_SESSION["callback_url"] = "watchlist.php";
        // header("Location: login.php");
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
    
?>

        <?php
        //PREPARING THE QUERY
        //if there is a query selected, order number must be selected for the query to generate properly
        // if(isset()) {
            
            // if(!isset($_POST['orderNum'])) {
            //     echo "The column \"Order Number\" is mandatory for the results to run properly. Please try again.";
            //     exit();
            // }

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

            //TODO: if only the neighbourhood is set - then how many to display
            // else if(empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            //     $stmt->bind_param('ss', $startDate, $endDate);
            //     $show_query = $display_query.$str_from." WHERE calendar.date >= ".$startDate." AND calendar.date <= ".$endDate;
            // }

            //for the date range, insert 2 variables
            // else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            //     $stmt->bind_param('sss', $neighbourhood, $startDate, $endDate);
            //     $show_query = $display_query.$str_from." WHERE listings.neighbourhood = ".$neighbourhood." AND calendar.date >= ".$startDate." AND calendar.date <= ".$endDate;
            // }

            $stmt->execute();
            $search_result = $stmt->get_result();            

            //START THE TABLE
            if($search_result->fetch_row() !=0) {
                // delete later
                // echo "<br>";
                // echo "<h4>SQL Query: </h4>";
                // echo "<p>".$show_query."</p>";
                //------

                // echo "<h2>Result</h2>";

                //table headings
                // echo "<table>
                // <tr>";

                //     echo "<td><b>Name</b></td>";
                //     echo "<td><b>Neighbourhood</b></td>";
                //     echo "<td><b>Price</b></td>";

                // "</tr>";

                table_header();

                //create the table rows
                while($row = $search_result->fetch_assoc()) {
                    // echo "<tr>";

                    //     echo "<td><a href=\"listingdetails.php?id=$id\"" . $row['name'] ."</td>";
                    //     echo "<td>" . $row['neighbourhood'] ."</td>";
                    //     echo "<td>" . $row['price'] ."</td>";
                    
                    // echo "</tr>";

                    table_contents($row['id'], $row['name'], $row['neighbourhood'], $row['price']);
                }

                // echo "</tr>
                // </table>";
                table_end();
            }
          
            else  {
                echo "<p>The entry cannot be found</p>";
            }

            $stmt->free_result();
            // $db->close();
        // }
    $db->close();
    include_once("footer.php");
?>