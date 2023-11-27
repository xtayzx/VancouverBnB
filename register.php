<?php
    require_once("private/initialize.php");
    require_SSL();

    if (is_logged_in()) {
        header("Location: listings.php");
    }

    if(is_post_request()) {
        //check for existing account, if not save entry
        //redirect after done
        if (
            isset($_POST["first_name"]) &&
            isset($_POST["last_name"]) &&
            isset($_POST["email"]) &&
            isset($_POST["username"]) &&
            isset($_POST["password"]) &&
            isset($_POST["password_confirm"]) ) {
            
                if($_POST["password"] == $_POST["password_confirm"]){
                $sql = "SELECT count(*) AS count FROM users WHERE username = ?";

                $stmt = $db->prepare($sql);

                //check for query error
                if(!$stmt) {
                    die("Error is:".$db->error);
                }

                $stmt->bind_param('s',$username);
                $stmt->execute();
                $result = $stmt->get_result();   

                //if username is taken
                if($result){
                    $res = $result->fetch_assoc();
                    if($res["count"] > 0){
                        echo "This username is taken.";
                }

                //on success
                else{
                    $hash_pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (first_name, last_name, email, username, hashed_password) 
                    VALUES (?,?,?,?,?)";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param($stmt,"sssss", 
                    $_POST["first_name"],
                    $_POST["last_name"],
                    $_POST["email"],
                    $_POST["username"],
                    $hash_pass);

                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('sssss',$_POST["first_name"], $_POST["last_name"], $_POST["email"], $_POST["username"], $hash_pass);
                    $res = $stmt->execute();

                    if($res){
                        $_SESSION["valid_user"] = $_POST["username"];
                        header("Location: listings.php");
                        }
                    }
                }
            }
        }
        $stmt->free_result();
    }

    else {
        $page_title = "Register";
        require("header.php");
    }
?>

<!-- generate the form -->
<div class="page-content, text-center">
    <form action="register.php" method="post">
        First Name:<br />
        <input type="text" name="first_name" value="" required /><br><br>
        Last Name:<br />
        <input type="text" name="last_name" value="" required /><br><br>
        Email:<br />
        <input type="text" name="email" value="" required /><br><br>
        Username:<br />
        <input type="text" name="username" value="" required /><br><br>
        Password:<br />
        <input type="password" name="password" value="" required /><br><br>
        Confirm Password:<br />
        <input type="password" name="password_confirm" value="" required /><br><br>
        <input class="main-button" type="submit" />
    </form>
</div>

<?php
    $db->close();
    include_once("footer.php");
?>