<?php
    require_once("private/initialize.php");

    //query to display the dropdown of order numbers properly
    $neighbourhood_query = "SELECT neighbourhood FROM listings GROUP BY neighbourhood ASC";
    $neighbourhood_result = $db->query($neighbourhood_query);
    $neighbourhood_row = $neighbourhood_result->fetch_assoc();

    //if there is no result, throw the error
    if (!$neighbourhood_result) {
        echo $db->error;
        exit();
    }

    //if one of the query options is filled in
    if (isset($_POST['neighbourhood']) || isset($_POST['startDate']) || isset($_POST['endDate'])) {

        //complete the query by order number
        if (isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
            $neighbourhood = $_POST['neighbourhood'];
        }

        //complete query by date range
        else if (empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
        }

        //complete query by date range
        else if (isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $neighbourhood = $_POST['neighbourhood'];
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
        }

        //ADDING QUERY VALUES STRING
        //by order number
        if (isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
            $str_from = " FROM listings";
            $str_where = " WHERE listings.neighbourhood = ? ";
        }

        //by date range
        else if (empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $str_from = " FROM listings, calendar";
            $str_where = " WHERE calendar.date >= ? AND calendar.date <= ? AND calendar.available = \"t\"";
        }

        //by neighbourhood and date range
        else if (isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $str_from = " FROM listings, calendar";
            $str_where = " WHERE listings.neighbourhood = ? AND calendar.date >= ? AND calendar.date <= ? AND calendar.available = \"t\"";
        }

        if (isset($_POST["sort"])) {
            if ($_POST["sort"] == "Price") {
                $group_by = " GROUP BY listings.price ASC LIMIT 50";
            } else if ($_POST["sort"] == "ID") {
                $group_by = " GROUP BY listings.id ASC LIMIT 50";
            }
        }

        //displaying the query on the page with the filled values (which will be added to the string later)
        $display_query = "SELECT listings.id, listings.name, listings.neighbourhood, listings.price, listings.picture_url";
        $query = $display_query . $str_from . $str_where . $group_by;
        
    }

    $page_title = "Search Listings";
    no_SSL();
    require("header.php");
?>

<form class="listings-container" method="post">
    <h2 class="listings-title ">Where to?</h2>
    <div class="options-row">
        <div class="option">
            <select id="neighbourhood" name="neighbourhood">
                <option value="" selected> Select Neighbourhood </option>

                <!-- display all the neighbourhoods in the database -->
                <?php
                //if the result is greater than zero
                if ($neighbourhood_result->fetch_row() > 0) {
                    //display all the neighbourhoods - if the selected order number is the same when it generates the dropdown, select that value to view as checked
                    while ($row = $neighbourhood_result->fetch_assoc()) {
                        if ($neighbourhood === $row['neighbourhood']) {
                            echo "<option value=\"" . $row['neighbourhood'] . "\" selected>" . $row['neighbourhood'] . "</option>";
                        } else
                            echo "<option value=\"" . $row['neighbourhood'] . "\">" . $row['neighbourhood'] . "</option>";
                    }
                }
                ?>

            </select>
        </div>

        <!-- the date range of the min and max is in reference to what exists in the database -->
        <div class="option date-range">
            <label>From: </label>
            <input type="date" name="startDate" value="<?php echo $startDate ?>" min="2023-10-01" max="2024-03-01">
            <label>To: </label>
            <input type="date" name="endDate" value="<?php echo $endDate ?>" min="2023-10-01" max="2024-03-01">
        </div>

        <input class="submit-listing" type="submit" id="submit" value="Search Listings"/>
    </div>

    <div class="option sort-by">
        <h3>Sort By: </h3>
        <input type="radio" name="sort" value="ID" checked>
        <label>Name</label>
        <input type="radio" name="sort" value="Price">
        <label>Price</label>
    </div>

</form>

<?php
    //PREPARING THE QUERY
    //if there is a query selected, order number must be selected for the query to generate properly
    if (isset($_POST['neighbourhood']) || isset($_POST['startDate']) || isset($_POST['endDate'])) {

        $stmt = $db->prepare($query);

        //check for query error
        if (!$stmt) {
            die("Error is:" . $db->error);
            
        }

        //BIND VALUES
        //for the order number, insert 1 variable
        if (isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
            $stmt->bind_param('s', $neighbourhood);
        }

        else if (empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $stmt->bind_param('ss', $startDate, $endDate);
        }

        //for the date range, insert 2 variables
        else if (isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $stmt->bind_param('sss', $neighbourhood, $startDate, $endDate);
        }

        $stmt->execute();
        $search_result = $stmt->get_result();
        $stmt->free_result();

        if ($search_result->fetch_row() != 0) {
            $count = 0; // Counter for tracking cards in a row
            echo '<h1 class = "listing-results-title">Your Results</h1>';
            start_cards_container();

            //create the table rows
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

        //error message
        else {
            echo "<p class = \"listings-no-results\">Error in the query. Please try again.</p>";
        }
    }

    $db->close();
    include_once("footer.php");
?>