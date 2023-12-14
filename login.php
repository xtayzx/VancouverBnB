<?php
    include("private/initialize.php");
    require_SSL();

    $username = '';
    $password = '';
    $neighbourhood = '';

    //if session is detected, then redirect
    if(is_logged_in()) {
        redirect_to($_SESSION["callback_url"]);
    }

    if(is_post_request()) {

        $username = $_POST["username"];
        $sql = "SELECT username, hashed_password, neighbourhood_preference FROM users WHERE username = ?";
        $stmt = $db->prepare($sql);

        //check for query error
        if(!$stmt) {
            die("Error is:".$db->error);
        }

        $stmt->bind_param('s',$username);
        $stmt->execute();
        $result = $stmt->get_result();      

        //START THE TABLE
        if($result->fetch_row() > 0) {

            // //has to go back to the first of the array
            $result->data_seek(0);

            while($row = $result->fetch_assoc()) {
                $password = $row["hashed_password"];
                $neighbourhood = $row["neighbourhood_preference"];
            }

            if(password_verify($_POST["password"], $password)) {
                $_SESSION["valid_user"] = $username;
                $_SESSION["neighbourhood_preference"] = $neighbourhood;

                if (!isset($_SESSION["callback_url"])) {
                    $_SESSION["callback_url"] = "profile.php";
                }

                redirect_to($_SESSION["callback_url"]);
            }

            else {
                echo "<p class = \"listings-no-results\">Incorrect password. Please try again.</p>";
            }
        }
    
        //if the table cannot be generated
        else  {
            echo "<p class = \"listings-no-results\">Incorrect username or this user does not exist. Please try again.</p>";
        }

        $stmt->free_result();
    }

    $page_title = "Login";
    require("header.php");

    if (!empty($msg) ) {
        echo "<p>$msg</p>\n";
    }
?>

<!-- generate the form -->

<div class="login">
    <h2 class = "user-login">Login</h2>
    <form action="login.php" method="post" class="text-center">
        <input class = "login-username" type="text" name="username" value="" placeholder="username" /><br />
        <br>
        <input class = login-password type="password" name="password" value="" placeholder="password"/><br />
        <input type="submit" class="submit-login" value = "Login">
    </form>
    <button class="register-button"><a href="register.php">Not registered yet? Register</a></button>
</div>

<?php
    $db->close();
    include("footer.php");
?>