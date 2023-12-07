<?php
    require_once("private/initialize.php");
    require_SSL();

    // query to display the dropdown of order numbers properly
    $neighbourhood_query = "SELECT neighbourhood FROM listings GROUP BY neighbourhood ASC";
    $neighbourhood_result = $db->query($neighbourhood_query);
    $neighbourhood_row = $neighbourhood_result->fetch_assoc();
        
    //if there is no result, throw the error
    if(!$neighbourhood_result) {
        echo $db->error;
        exit();
    }

    $username = $_SESSION["valid_user"];

    $page_title = "Change Your Preferences";
    require("header.php");

    echo "<div class=\"page-content\">";

    if (isset($message)) echo "<p>$message</p>";

    if(is_post_request()) {
        $update_query = "UPDATE users SET neighbourhood_preference = ? WHERE username = ?";
        $stmt = $db->prepare($update_query);

        //check for query error
        if(!$stmt) {
            die("Error is:".$db->error);
        }
    
        $stmt->bind_param('ss',$_POST["neighbourhood_preference"], $_SESSION["valid_user"]);
        $stmt->execute();
        $stmt->free_result();

        unset($_SESSION["neighbourhood_preference"]);
        $_SESSION["neighbourhood_preference"] = $_POST["neighbourhood_preference"];

        header("Location: profile.php");
    }


    ?>

<div class="page-content, text-center">
    <form action="edit.php" method="post">
    
        Neighbourhood Preference<br />

        <select id="neighbourhood_preference" name="neighbourhood_preference">
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
            <br><br>
        <input class="main-button" type="submit" />
    </form>
</div>

    <?php

    echo "</div>";

    $db->close();

    include_once("footer.php");
?>