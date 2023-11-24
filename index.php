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

    $page_title = "Welcome";
    no_SSL();
    require("header.php");
    
?>

<form method="post" action="listingdetails.php">
    <?php
        table_header();
        if ($result->fetch_row() > 0) {
            while ($row = $result->fetch_assoc()) {
                //insert value in function to pass values for the rows
                table_contents($row["id"], $row["name"], $row["neighbourhood"]);
            }
        }
        table_end();
    ?>
</form>

<?php
    $db->close();
    include_once("footer.php");
?>