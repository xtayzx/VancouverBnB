<?php
    require_once("private/initialize.php");

//query to display the dropdown of order numbers properly
$neighbourhood_query = "SELECT neighbourhood FROM listings GROUP BY neighbourhood ASC";
$neighbourhood_result = $db->query($neighbourhood_query);
$neighbourhood_row = $neighbourhood_result->fetch_assoc();
    
//if there is no result, throw the error
if(!$neighbourhood_result) {
    echo $db->error;
    exit();
}

//if one of the query options is filled in
if(isset($_POST['neighbourhood']) || isset($_POST['startDate']) || isset($_POST['endDate'])) {
        
    //complete the query by order number
    if(isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
        $neighbourhood = $_POST['neighbourhood'];
    } 

    //complete query by date range
    else if(empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
    } 

    //complete query by date range
    else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $neighbourhood = $_POST['neighbourhood'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
    } 
    
    //return error if both of the queries are set, the user can only select one or the other
    else {
        echo "Error in the query. Please try again.";
        exit();
    }

    //ADDING QUERY VALUES STRING
    //by order number
    if(isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
        $str_from = " FROM listings";
        $str_where = " WHERE listings.neighbourhood = ? ";
    } 

    //by date range
    else if(empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $str_from = " FROM listings, calendar";
        $str_where = " WHERE calendar.date >= ? AND calendar.date <= ? AND calendar.available = \"t\"";
    }   

    //by neighbourhood and date range
    else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $str_from = " FROM listings, calendar";
        $str_where = " WHERE listings.neighbourhood = ? AND calendar.date >= ? AND calendar.date <= ? AND calendar.available = \"t\"";
    }         
    
    if(isset($_POST["sort"])) {
        if($_POST["sort"] == "Price") {
            $group_by = " GROUP BY listings.price ASC LIMIT 50";
        }

        else if($_POST["sort"] == "ID") {
            $group_by = " GROUP BY listings.id ASC LIMIT 50";
        }
    }

    //displaying the query on the page with the filled values (which will be added to the string later)
    $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.picture_url";

    //creating the prepared statement
    //TODO: How to pagenate the query rather than just having 50
    // $query = $display_query.$str_from.$str_where." GROUP BY listings.id ASC LIMIT 50";
    $query = $display_query.$str_from.$str_where.$group_by;
}

    $page_title = "Search Listings";
    no_SSL();
    require("header.php");
    
?>

<form method="post">
            <h3>Search for a Airbnb</h3>
            <label>Neighbourhood: </label>
            <select id="neighbourhood" name="neighbourhood">
            <option value="" selected>---Select Neighbourhood---</option>

            <!-- Display all the order numbers in the database -->
            <?php
                //if the result is greater than zero
                if ($neighbourhood_result->fetch_row() > 0) {

                //display all the order numbers - if the selected order number is the same when it generates the dropdown, select that value to view as checked
                while ($row = $neighbourhood_result->fetch_assoc()) {      
                    if($neighbourhood === $row['neighbourhood']) {
                        echo "<option value=\"".$row['neighbourhood']."\" selected>".$row['neighbourhood']."</option>";
                    }
                    else echo "<option value=\"".$row['neighbourhood']."\">".$row['neighbourhood']."</option>";
                    }
                }
            ?>
            </select>

            <label>or</label>
            <br>
            <br>
            <label>Vacation Dates (YYYY-MM-DD)</label>
            <br>

            <!-- the date range of the min and max is in reference to what exists in the database -->
            <label>From: </label>
            <input type="date" name="startDate" value="<?php echo $startDate?>" min="2023-10-01" max="2024-03-01">
            
            <label>To: </label>
            <input type="date" name="endDate" value="<?php echo $endDate?>" min="2023-10-01" max="2024-03-01">
            <br>
            <br>

            <label>Sort By: </label><br>
            <input type="radio" name="sort" value="ID" checked>
            <label>ID</label><br>
            <input type="radio" name="sort" value="Price">
            <label>Price</label>
            <br>
            <br>
            <input type="submit" id="submit" value="Search Listings"/>
        </form>

        <?php
        //PREPARING THE QUERY
        //if there is a query selected, order number must be selected for the query to generate properly
        if(isset($_POST['neighbourhood']) || isset($_POST['startDate']) || isset($_POST['endDate'])) {

            $stmt = $db->prepare($query);

            //check for query error
            if(!$stmt) {
                die("Error is:".$db->error);
            }

            //BIND VALUES
            //for the order number, insert 1 variable
            if (isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])){
                $stmt->bind_param('s', $neighbourhood);
                $show_query = $display_query.$str_from." WHERE listings.neighbourhood = ".$neighbourhood;
            }

            //TODO: if only the neighbourhood is set - then how many to display
            else if(empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
                $stmt->bind_param('ss', $startDate, $endDate);
                $show_query = $display_query.$str_from." WHERE calendar.date >= ".$startDate." AND calendar.date <= ".$endDate;
            }

            //for the date range, insert 2 variables
            else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
                $stmt->bind_param('sss', $neighbourhood, $startDate, $endDate);
                $show_query = $display_query.$str_from." WHERE listings.neighbourhood = ".$neighbourhood." AND calendar.date >= ".$startDate." AND calendar.date <= ".$endDate;
            }

            $stmt->execute();
            $search_result = $stmt->get_result();            

            //START THE TABLE
            if($search_result->fetch_row() !=0) {

                table_header();

                //create the table rows
                while($row = $search_result->fetch_assoc()) {

                    table_contents($row['id'], $row['picture_url'], $row['name'], $row['neighbourhood'], $row['price']);
                }

                table_end();
            }
          
            else  {
                echo "<p>The entry cannot be found</p>";
            }

            $stmt->free_result();
        }
    $db->close();
    include_once("footer.php");
?>