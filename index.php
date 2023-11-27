<?php
    require_once("private/initialize.php");

    $query = "SELECT listings.name, listings.neighbourhood FROM listings ORDER BY listings.neighbourhood ASC LIMIT 50";
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    
    //if there is no result, throw the error
    if(!$result) {
        echo $db->error;
        exit();
    }

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
    $order_number = $_POST['neighbourhood'];
    } 

    //complete query by date range
    else if(empty($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    } 
    
    //return error if both of the queries are set, the user can only select one or the other
    else {
        echo "Error in the query. Please try again and ensure only one of the parameters is set (by Neighbourhood or by Date Range).";
        exit();
    }

    //initialize all the variables to ensure no errors are thrown - they have nothing but they are not null
    // $str_order_number = "";
    // $str_order_date = "";
    // $str_order_shipped_date = "";
    // $str_order_product_name = "";
    // $str_order_product_desc = "";
    // $str_order_qty_ordered = "";
    // $str_order_price_each = "";
    // $str_from = "";
    // $str_ij_orderNum = "";
    // $str_ij_prodCode ="";
    // $str_where = "";


    //writing the queries - if the checkbox is selected, then create the string to add to the passed query
    //CHECKBOXES
    // if (isset($_POST['orderNum'])) {
    //     $str_order_number = "orders.orderNumber";
    // }

    // if (isset($_POST['orderDate'])) {
    //     $str_order_date = ", orders.orderDate";
    // }

    // if (isset($_POST['shipDate'])) {
    //     $str_order_shipped_date = ", orders.shippedDate";
    // }

    // if (isset($_POST['prodName'])) {
    //     $str_order_product_name = ", products.productName";
    // }

    // if (isset($_POST['prodDes'])) {
    //     $str_order_product_desc = ", products.productDescription";
    // }

    // if (isset($_POST['qtyOrd'])) {
    //     $str_order_qty_ordered = ", orderdetails.quantityOrdered";
    // }

    // if (isset($_POST['priceEach'])) {
    //     $str_order_price_each = ", orderdetails.priceEach";
    // }
    
    //WHICH TABLE
    // if (isset($_POST['orderNum']) || isset($_POST['orderDate']) || isset($_POST['shipDate'])) {
    //     //FROM is set to orders
    //     $str_from = " FROM orders ";
    // }

    // else if (isset($_POST['prodName']) || isset($_POST['prodDes'])) {
    //     //FROM is set to products
    //     $str_from = " FROM products ";
    // }

    // else if (isset($_POST['qtyOrd']) || isset($_POST['priceEach'])) {
    //     //FROM is set to orderdetails
    //     $str_from = " FROM orderdetails ";

    //     //if there is a query that contains an attribute from the products table, then join the table
    //     if (isset($_POST['prodName']) || isset($_POST['prodDes'])) {
    //         $str_ij_prodCode = "INNER JOIN products ON orderdetails.productCode = products.productCode ";
    //     }
    // }

    // //if the order number is selected in the query, then join the order numbers from the orders table and order details
    // if (isset($_POST['orderNum'])) {
    //     $str_ij_orderNum = "INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber ";
        
    //     //if there is a query that contains an attribute from the products table, then join the table
    //     if (isset($_POST['prodName']) || isset($_POST['prodDes'])) {
    //         $str_ij_prodCode = "INNER JOIN products ON orderdetails.productCode = products.productCode ";
    //     }
    // }

    //ADDING QUERY VALUES STRING
    //by order number
    if(isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])) {
        $str_where = "WHERE listings.neighbourhood = ? ";
    } 

    //by date range
    else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
        $str_where = "WHERE listings.neighbourhood = ? AND calendar.date >= ? AND calendar.date <= ? AND calendar.avaliable = \"t\"";
    }              

    //displaying the query on the page with the filled values (which will be added to the string later)
    $display_query = "SELECT listings.name, listings.neighbourhood, listings.price FROM listings, calendar";
    // .$str_order_number
    // .$str_order_date
    // .$str_order_shipped_date
    // .$str_order_product_name
    // .$str_order_product_desc
    // .$str_order_qty_ordered
    // .$str_order_price_each
    // .$str_from
    // .$str_ij_orderNum
    // .$str_ij_prodCode;

    //creating the prepared statement
    $query = $display_query.$str_where;
}

    $page_title = "Welcome";
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
            <input type="submit" id="submit" value="Search Listings"/>
        </form>

        <?php
        //PREPARING THE QUERY
        //if there is a query selected, order number must be selected for the query to generate properly
        if(isset($_POST['neighbourhood']) || isset($_POST['startDate']) || isset($_POST['endDate'])) {
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
            if (isset($_POST['neighbourhood']) && empty($_POST['startDate']) && empty($_POST['endDate'])){
                $stmt->bind_param('s', $neighbourhood);
                $show_query = $display_query."WHERE listings.neighbourhood = ".$neighbourhood;
            }

            //TODO: if only the neighbourhood is set - then how many to display

            //for the date range, insert 2 variables
            else if(isset($_POST['neighbourhood']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
                $stmt->bind_param('sss', $neighbourhood, $startDate, $endDate);
                $show_query = $display_query."WHERE listings.neighbourhood = ".$neighbourhood." AND calendar.date >= ".$startDate." AND calendar.date <= ".$endDate;
            }

            $stmt->execute();
            $search_result = $stmt->get_result();            

            //START THE TABLE
            if($search_result->fetch_row() !=0) {
                // delete later
                echo "<h4>SQL Query: </h4>";
                echo "<p>".$show_query."</p>";
                //------

                echo "<h2>Result</h2>";

                //table headings
                echo "<table>
                <tr>";

                
                    echo "<td><b>Name</b></td>";
                
        
            
                    echo "<td><b>Neighbourhood</b></td>";
                
        
             
                    echo "<td><b>Price</b></td>";
                
        
                // if (isset($_POST['prodName'])) {
                //     echo "<td><b>Product Name</b></td>";
                // }
        
                // if (isset($_POST['prodDes'])) {
                //     echo "<td><b>Product Description</b></td>";
                // }
        
                // if (isset($_POST['qtyOrd'])) {
                //     echo "<td><b>Quantity Ordered</b></td>";
                // }
        
                // if (isset($_POST['priceEach'])) {
                //     echo "<td><b>Price Each</b></td>";
                // }
                "</tr>";

                //create the table rows
                while($row = $search_result->fetch_assoc()) {
                    echo "<tr>";

                    // if (isset($_POST['orderNum'])) {
                        echo "<td>" . $row['name'] ."</td>";
                    // }
            
                    // if (isset($_POST['orderDate'])) {
                        echo "<td>" . $row['neighbourhood'] ."</td>";
                    // }
            
                    // if (isset($_POST['shipDate'])) {
                        echo "<td>" . $row['price'] ."</td>";
                    // }
            
                    // if (isset($_POST['prodName'])) {
                    //     echo "<td>" . $row['productName'] ."</td>";
                    // }
            
                    // if (isset($_POST['prodDes'])) {
                    //     echo "<td>" . $row['productDescription'] ."</td>";
                    // }
            
                    // if (isset($_POST['qtyOrd'])) {
                    //     echo "<td>" . $row['quantityOrdered'] ."</td>";
                    // }
            
                    // if (isset($_POST['priceEach'])) {
                    //     echo "<td>" . $row['priceEach'] ."</td>";
                    // }
                    
                    echo "</tr>";
                }

                echo "</tr>
                </table>";
            }
          
            //if the table cannot be generated
            else  {
                echo "<p>The entry cannot be found</p>";
            }

            $stmt->free_result();
            $db->close();
        }
        ?>

<!-- <form method="post" action="listingdetails.php">
    <?php
        //table_header();
        //if ($result->fetch_row() > 0) {
            //while ($row = $result->fetch_assoc()) {
                //insert value in function to pass values for the rows
                //table_contents($row["id"], $row["name"], $row["neighbourhood"]);
            //}
       // }
        //table_end();
    ?>
</form> -->

<?php
    // $db->close();
    include_once("footer.php");
?>