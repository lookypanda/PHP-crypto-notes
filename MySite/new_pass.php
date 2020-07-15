<?php require_once("includes/connection.php"); ?>
<?php include("includes/header.php"); ?>

<?php
//Проверяем, если существует переменная token в глобальном массиве GET
if (isset($_GET['hash_login']) && !empty($_GET['hash_login'])) {
    $input_text3 = "";
    $input_text3 = trim($_GET['hash_login']);
    $input_text3 = htmlspecialchars($input_text3);
    $input_text3 = mysqli_real_escape_string($con, $input_text3);
    $hash_login = $input_text3;
    if (isset($_GET['hash_pass']) && !empty($_GET['hash_pass'])) {
        $input_text3 = "";
        $input_text3 = trim($_GET['hash_pass']);
        $input_text3 = htmlspecialchars($input_text3);
        $input_text3 = mysqli_real_escape_string($con, $input_text3);
        $hash_pass = $input_text3;
        $n1 = $con; #mysqli_connect(DB_SERVER,DB_USER, DB_PASS) or die(mysqli_error());
    #mysqli_select_db($con,DB_NAME) or die("Cannot select DB");
        $sql = "SELECT * FROM usertbl WHERE password='" . $hash_pass . "'";
        $query = mysqli_query($n1, $sql) or trigger_error(mysqli_error($n1) . " in " . $sql);
        $numrows = mysqli_num_rows($query);
        if($row = mysqli_fetch_assoc($query)) {
            $dbusername = $row['username'];
            $dbpassword = $row['password'];
        }else {
            exit("<p><strong>Error!</strong> Old Link.</p>");
        }

    } else {
        exit("<p><strong>Error!</strong> Отсутствует проверочный код.</p>");
    }
} else {
    exit("<p><strong>Error!</strong> Отсутствует проверочный код эектронного адреса.</p>");
}

//Проверяем, если существует переменная email в глобальном массиве GET


?>
<?php

if (isset($_POST["pass_restore"])) {


    if (!empty($_POST['password'])) {
        $input_text4 = "";

        $input_text4 = trim($_POST['password']);
        $input_text4 = htmlspecialchars($input_text4);
        $input_text4 = mysqli_real_escape_string($con, $input_text4);
        $password = $input_text4;
        //$password = $_POST['password'];

        // Given password


        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $message = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
        } else {
            //echo 'Strong password.';
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            if ($hash_password) {
                $n1 = $con; #mysqli_connect(DB_SERVER,DB_USER, DB_PASS) or die(mysqli_error());
                #mysqli_select_db($con,DB_NAME) or die("Cannot select DB");
               // $sql = "UPDATE usertbl SET password='".$hash_password."' WHERE username='" . $dbusername ." && username= ". $dbusername. "'";
                $sql = "UPDATE usertbl SET password='".$hash_password."'  WHERE username= '".$dbusername."'  && password=  '".$hash_pass."' ";
                $query = mysqli_query($n1, $sql) or trigger_error(mysqli_error($n1) . " in " . $sql);
                if($query){
                    $message = "Password changed";
                } else {
                    $message = "SQL err";
                }

                /*$numrows = mysqli_num_rows($query);
                if ($numrows == 0) {
                    $sql = "SELECT * FROM usertbl WHERE username='" . $username . "'";
                    $query = mysqli_query($n1, $sql) or trigger_error(mysqli_error($n1) . " in " . $sql);
                    $numrows = mysqli_num_rows($query);
                    if ($numrows == 0) {

                        $sql = "INSERT INTO usertbl (full_name, email, username,password) VALUES('$full_name','$email', '$username', '$hash_password')";
                        $result = mysqli_query($n1, $sql);
                        if ($result) {
                            $message = "Account Successfully Created";
                        } else {
                            $message = "Failed to insert data information!";
                        }
                    } else {
                        $message = "That username already exists! Please try another one!";
                    }
                } else {
                    $message = "That email already exists! Please try another one!";
                }*/

            } else {
                $message = "Hash err";
            }

        }
    } else {
        $message = "All fields are required!";
    }
}
?>


<?php if (!empty($message)) {
    echo "<p class=\"error\">" . "MESSAGE: " . $message . "</p>";
} ?>

<div class="container mregister">
    <div id="login">
        <h1>NEW PASSWORD</h1>
        <form name="pass_restore" id="pass_restore" action="new_pass.php?hash_login=<?php echo $hash_login;?>&hash_pass=<?php echo $hash_pass;?>" method="post">

            <p>
                <label for="user_pass">Password<br/>
                    <input type="password" name="password" id="password" class="input" value="" size="32"/></label>
            </p>


            <p class="submit">
                <input type="submit" name="pass_restore" id="pass_restore" class="button" value="pass_restore"/>
            </p>

            <p class="regtext">Already have an account? <a href="login.php">Login Here</a>!</p>
        </form>

    </div>
</div>
