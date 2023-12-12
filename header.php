<?php
// echo error_reporting();

if(!isset($page_title)) {
    $page_title = "Title";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>VancouverBnB -
        <?php echo heading($page_title); ?>
    </title>
    <link rel="stylesheet" href="styles.css">

    <!-- <link rel = "stylesheet" href = "http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" /> -->
</head>

<body>
    <div class="header-size">

        <div class="header-container">
            <div class="header-title" h1>vancouverBnB</div>

            <div class="menu-font">
                <a href="home.php">Home</a> |
                <a href="listings.php">Search Listings</a> |
                <?php
                if(is_logged_in()) {
                    echo "<a href=\"logout.php\">Logout</a>";
                } else
                    echo "<a href=\"login.php\">Login</a>";
                ?>
            </div>

            <div class="user-profile">
                <strong>
                    <p class="user-text">User:
                        <?php echo $_SESSION['valid_user'] ?? ''; ?>
                    </p>
                </strong>
                <div class="user-profile-img">
                    <a href="profile.php">
                        <img src="image/profile.png" width="40" height="auto" style="width: 40px; height: auto;">
                    </a>
                </div>
            </div>
        </div>

        <!-- set the page title -->
        <h2>
            <?php echo $page_title ?>
        </h2>