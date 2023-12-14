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
        echo "<div class =\"host-info-card\">";
        echo "<div class =\"host-details\">";
        echo "<div class =\"host-img-container\">";
        echo "<img class=\"host-img\" src=\"" . $row["host_picture_url"] . "\">";
        echo "</div>";
        //NAME
        echo "<h2>" . $row["host_name"] . "</h2>";
        echo "<br>";
        //HOST URL
        echo "<p><a href=\"" . $row["host_url"] . "\">Airbnb Profile Link</a></p>";
        echo "<br>";



        //HOST RATES
        echo "<div class=\"host-stats\">";
        echo "<div class=\"rate\">";
        echo "<p class=\"stats\"><strong>" . $row["host_response_rate"] . "</strong></p>";
        echo "<p>Response Rate</p>";
        echo "</div>";

        // echo "<p class =\"host-stats-divider\">|</p>";
        echo "<div class=\"rate\">";
        echo "<p class=\"stats\"><strong>" . $row["host_acceptance_rate"] . "</strong></p>";
        echo "<p>Acceptance Rate</p>";
        echo "</div>";
        echo "</div>";

        //HOST LISTINGS
        echo "<div class=\"host-stats\">";
        echo "<div class=\"rate\">";
        echo "<span class=\"stats\"><strong>" . $row["host_listings_count"] . "</strong></p>";
        echo "<p>Listings Count</p>";
        echo "</div>";

        echo "<div class=\"rate\">";
        echo "<span class=\"stats\"><strong>" . $row["host_total_listings_count"] . "</strong></p>";
        echo "<p>Total Listings Count</p>";
        echo "</div>";
        echo "</div>";

        echo "</div>";
        echo "</div>";


        // Host ID, Host Since, Host Location
        echo "<div class=\"about-section\">";
        echo "<h3 class =\"host-about-name\">About ". $row["host_name"] . "</h3>";


        echo "<div class=\"host-details-info\">";
        echo "<div class=\"column\">";
        echo "<h3>Host Location</h3>";
        echo "<p>" . $row["host_location"] . "</p>";
        echo "</div>";
        
        echo "<div class=\"column\">";
        echo "<h3>Host Since</h3>";
        echo "<p>" . $row["host_since"] . "</p>";
        echo "</div>";
        
        echo "<div class=\"column\">";
        echo "<h3>Host ID</h3>";
        echo "<p><a href=\"host.php?id=" . $row["host_id"] . "\">" . $row["host_id"] . "</a></p>";
        echo "</div>";
        echo "</div>";
        
        echo "<p class =\"about\">" . $row["host_about"] . "</p>";
        echo "</div>";
    
        
    }
}

//if the table cannot be generated
else {
    echo "<p class = \"listings-no-results\" >The entry cannot be found.</p>";
}

$stmt->free_result();

$db->close();

echo "</div>";

include("footer.php");
?>