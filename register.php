<?php
require_once("private/initialize.php");
require_SSL();

if (is_logged_in()) {
    header("Location: listings.php");
}

// query to display the dropdown of order numbers properly
$neighbourhood_query = "SELECT neighbourhood FROM listings GROUP BY neighbourhood ASC";
$neighbourhood_result = $db->query($neighbourhood_query);
$neighbourhood_row = $neighbourhood_result->fetch_assoc();

//if there is no result, throw the error
if (!$neighbourhood_result) {
    echo $db->error;
    exit();
}

if (is_post_request()) {
    //check for existing account, if not save entry
    //redirect after done
    if (
        isset($_POST["first_name"]) &&
        isset($_POST["last_name"]) &&
        isset($_POST["email"]) &&
        isset($_POST["username"]) &&
        isset($_POST["password"]) &&
        isset($_POST["password_confirm"]) &&
        isset($_POST["neighbourhood_preference"])
    ) {

        if ($_POST["password"] == $_POST["password_confirm"]) {
            $sql = "SELECT count(*) AS count FROM users WHERE username = ?";

            $stmt = $db->prepare($sql);

            //check for query error
            if (!$stmt) {
                die("Error is:" . $db->error);
            }

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            //if username is taken
            if ($result) {
                $res = $result->fetch_assoc();
                if ($res["count"] > 0) {
                    echo "This username is taken.";
                }

                //on success
                else {
                    $hash_pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (first_name, last_name, email, username, hashed_password, neighbourhoodPreference) 
                    VALUES (?,?,?,?,?,?)";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssss",
                        $_POST["first_name"],
                        $_POST["last_name"],
                        $_POST["email"],
                        $_POST["username"],
                        $hash_pass,
                        $_POST["neighbourhood_preference"]);

                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('ssssss', $_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["username"], $hash_pass, $_POST["neighbourhood_preference"]);
                    $res = $stmt->execute();

                    if ($res) {
                        $_SESSION["valid_user"] = $_POST["username"];
                        $_SESSION["neighbourhood_preference"] = $_POST["neighbourhood_preference"];
                        header("Location: listings.php");
                    }
                }
            }
        }
    }
    $stmt->free_result();
} else {
    $page_title = "Register";
    require("header.php");
}
?>

<!-- generate the form -->
<div class="register">
    <h2 class="user-login">Create Your Account</h2>
    <form action="register.php" method="post">

        <input type="text" name="first_name" value="" class="register-fields" required
            placeholder="First Name" /><br><br>

        <input type="text" name="last_name" value="" class="register-fields" required placeholder="Last Name" /><br><br>

        <input type="text" name="email" value="" class="register-fields" required placeholder="E-mail" /><br><br>

        <input type="text" name="username" value="" class="register-fields" required placeholder="Username" /><br><br>

        <input type="password" name="password" value="" class="register-fields" required
            placeholder="Password" /><br><br>

        <input type="password" name="password_confirm" value="" class="register-fields" required
            placeholder="Confirm Password" /><br><br>


        <div class="neighbourhood-preference">
            <br>
            Neighbourhood Preference<br />
            <select id="neighbourhood_preference" name="neighbourhood_preference">
                <option value="" selected>Select Neighbourhood</option>

                <!-- Display all the order numbers in the database -->
                <?php
                //if the result is greater than zero
                if ($neighbourhood_result->fetch_row() > 0) {

                    //display all the order numbers - if the selected order number is the same when it generates the dropdown, select that value to view as checked
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

        <br><br>
        <input class="submit-login" type="submit" value="Create Your Account" />
    </form>
</div>

<?php
$db->close();
include_once("footer.php");
?>