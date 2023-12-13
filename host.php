<?php
require_once("private/initialize.php");

//Identify what is passed in the URL
$code = trim($_GET["id"]);
@$msg = trim($_GET["message"]);

$page_title = "Host Details";
require("header.php");
?>



<?php
$host_query = "SELECT * FROM hosts WHERE hosts.host_id = ?";
$stmt = $db->prepare($host_query);

//check for query error
if (!$stmt) {
    die("Error is:" . $db->error);
}

$stmt->bind_param('s', $code);
$stmt->execute();
$search_result = $stmt->get_result();

if (!empty($msg)) {
    echo "<p>$msg</p><br>\n";
}

//start the table of details
if ($search_result->fetch_row() != 0) {

    //has to go back to the first of the array
    $search_result->data_seek(0);
    while ($row = $search_result->fetch_assoc()) {

        //IMAGE
        echo "<div class =\"host-info\">";
        echo "<div class =\"host-details\">";
        echo "<div class =\"host-img-container\">";
        echo "<img class=\"host-img\" src=\"" . $row["host_picture_url"] . "\">";
        //NAME
        echo "<h2>" . $row["host_name"] . "</h2>";
        echo "<br>";
        //HOST URL
        echo "<p><a href=\"" . $row["host_url"] . "\">Airbnb Profile Link</a></p>";
        echo "<br>";

        // echo "<div class=\"host-stats\">";
        //HOST RATES
        
        echo "<p class = \"stats\">" . $row["host_response_rate"] . "</p><br>";
        echo "<h3>Response Rate</h3>";
        echo "<br>";
        echo "<p class = \"stats\">" . $row["host_acceptance_rate"] . "</p>";
        echo "<h3>Acceptance Rate</h3>";
        echo "<br>";

        //HOST LISITINGS
        echo "<h3>Listings Count</h3>";
        echo "<p class = \"stats\">" . $row["host_listings_count"] . "</p><br>";
        echo "<h3>Total Listings Count</h3>";
        echo "<p class = \"stats\">" . $row["host_total_listings_count"] . "</p>";
        echo "<br>";


        echo "</div>";
        echo "</div>";
        // echo "</div>";



        //HOST ID
        echo "<h3>Host ID</h3>";
        echo "<p><a href=\"host.php?id=" . $row["host_id"] . "\">" . $row["host_id"] . "</a></p>";
        echo "<br>";

        //HOST SINCE
        echo "<h3>Host Since</h3>";
        echo "<p>" . $row["host_since"] . "</p>";
        echo "<br>";

        //ABOUT
        echo "<h3>About</h3>";
        echo "<p>" . $row["host_about"] . "</p>";
        echo "<br>";

        //HOST LOCATION
        echo "<h3>Host Location</h3>";
        echo "<p>" . $row["host_location"] . "</p>";
        echo "<br>";

        // echo "<div class =\"host-rates\">";


    }
}

//if the table cannot be generated
else {
    echo "<p>The entry cannot be found.</p>";
}

$stmt->free_result();

$db->close();

echo "</div>";

include("footer.php");
?>