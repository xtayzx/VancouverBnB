<?php
    if(!isset($page_title)) {
        $page_title = "Title";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>VancouverBnB - <?php echo heading($page_title);?></title>
        <link rel="stylesheet" href="styles.css">
    </head>

    <body>
        <table class="header-size">
                <tr class="header-background">
                    <th class="header-title">VancouverBnB</th>
                </tr>
                <tr class="menu-size">
                    <td class="menu-font">
                        <strong><p class="user-text">User: <?php

                        //check for if session is set, if is, display the username
                        echo $_SESSION['valid_user'] ?? '';
                        ?></p>
                        <a href="listings.php">Listings</a> |
                        <a href="watchlist.php">Watchlist</a> |
                        <?php
                            if(is_logged_in()) {
                                echo "<a href=\"logout.php\">Logout</a>";
                            }

                            else echo "<a href=\"login.php\">Login</a>";
                        ?>
                    </td>
                <tr>
        </table>

        <!-- set the page title -->
        <h2><?php echo $page_title ?></h2>

    