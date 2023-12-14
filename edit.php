<?php
    require_once("private/initialize.php");
    require_SSL();

    // query to display the dropdown of order numbers properly
    $neighbourhood_query = "SELECT neighbourhood FROM listings GROUP BY neighbourhood ASC";
    $neighbourhood_result = $db->query($neighbourhood_query);
    $neighbourhood_row = $neighbourhood_result->fetch_assoc();

    //if there is no result, throw the error
    if (!$neighbourhood_result) {
        echo $db->error;
        exit();
    }

    $username = $_SESSION["valid_user"];

    $page_title = "Change Your Preferences";
    require("header.php");

    echo "<div class=\"page-content\">";

    if (isset($message))
        echo "<p>$message</p>";

    if (is_post_request()) {
        $update_query = "UPDATE users SET first_name = ?, last_name = ?, neighbourhood_preference = ? WHERE username = ?";
        $stmt = $db->prepare($update_query);

        //check for query error
        if (!$stmt) {
            die("Error is:" . $db->error);
        }

        $stmt->bind_param('ssss', $_POST["first_name"], $_POST["last_name"], $_POST["neighbourhood_preference"], $_SESSION["valid_user"]);
        $stmt->execute();
        $stmt->free_result();

        unset($_SESSION["neighbourhood_preference"]);
        $_SESSION["neighbourhood_preference"] = $_POST["neighbourhood_preference"];

        header("Location: profile.php");
    }

    $profile_query = "SELECT first_name, last_name, neighbourhood_preference FROM users WHERE username = ?";
    $stmt = $db->prepare($profile_query);

    //check for query error
    if (!$stmt) {
        die("Error is:" . $db->error);
    }

    $stmt->bind_param('s', $_SESSION["valid_user"]);
    $stmt->execute();
    $search_result = $stmt->get_result();

    if (!empty($msg)) {
        echo "<p>$msg</p>\n";
    }

    //start the table of details
    if ($search_result->fetch_row() != 0) {

        //has to go back to the first of the array
        $search_result->data_seek(0);

        while ($row = $search_result->fetch_assoc()) {
            $first_name = $row["first_name"];
            $last_name = $row["last_name"];
            $neighbourhood = $row["neighbourhood_preference"];
        }
    } else {
        echo "<p>The information could not be displayed.</p><br>";
    }

    $stmt->free_result();
    echo "<div class=\"edit\">";
    echo "<img class =\"edit-profile-img\" src=\"image/profile.png\" width=\"40\" height=\"auto\" style=\"width: 100px; height: auto;\">";
    echo "<h2 class = \"user-login\">Edit Name/Preference</h2>";
    echo "<form action=\"edit.php\" method=\"post\">";
    echo "<h5>First Name:</h5>";
    echo "<input class = \"edit-firstname\" type=\"text\" name=\"first_name\" value=\"" . $first_name . "\" required /><br>";
    echo "<h5>Last Name:</h5>";
    echo "<input class = \"edit-lastname\"type=\"text\" name=\"last_name\" value=\"" . $last_name . "\" required /><br>";
    echo "<h5>Neighbourhood Preference</h5>";
?>

<div class="edit-option">
    <select id="neighbourhood_preference" name="neighbourhood_preference">
        <option value="" selected>Select Neighbourhood</option>
        <?php
        //if the result is greater than zero
        if ($neighbourhood_result->fetch_row() > 0) {

            //display all neighbourhoods
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

<input class="submit-edits" type="submit" />
</form>

<?php
    echo "</div>";
    $db->close();
    include_once("footer.php");
?>