<?php
session_start();
?>

<?php require_once("includes/connection.php"); ?>

<?php include("includes/header.php"); ?>


<?php
if (isset($_SESSION["session_username"])) {
    // echo "Session is set"; // for testing purposes
    header("Location: intropage.php");
}


if (isset($_POST["login"])) {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $input_text = trim($_POST['username']);
        $input_text = htmlspecialchars($input_text);
        $input_text = mysqli_real_escape_string($con, $input_text);
        $username = $input_text;
        //$username = $_POST['username'];
        $input_text2 = trim($_POST['password']);
        $input_text2 = htmlspecialchars($input_text2);
        $input_text2 = mysqli_real_escape_string($con, $input_text2);
        $password = $input_text2;
        //$password = $_POST['password'];
        #$con внешний
        $sql = "SELECT * FROM usertbl WHERE username='" . $username . "'";
        $query = mysqli_query($con, $sql) or trigger_error(mysqli_error($con) . " in " . $sql);

        $numrows = mysqli_num_rows($query);
        if ($numrows != 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $dbusername = $row['username'];
                $dbpassword = $row['password'];
            }

            if ($dbusername && $dbpassword && $username == $dbusername && password_verify($password, $dbpassword)) {


                $_SESSION['session_username'] = $username;

                /* Redirect browser */
                header("Location: intropage.php");
            }
        } else {

            $message = "Invalid username or password!";
        }
    } else {
        $message = "All fields are required!";
    }
}

?>


    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(53745645, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/53745645" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

    <div class="container mlogin">
        <div id="login">
            <h1>LOGIN</h1>
            <form name="loginform" id="loginform" action="" method="POST" style="width: 320px">

                <label for="user_login" style="width: 320px">Username<br/>
                    <input type="text" name="username" id="username" class="input" value="" size="20"/></label>
                <label for="user_pass" style="width: 320px">Password<br/>
                    <input type="password" name="password" id="password" class="input" value="" size="20"/></label>


                <!-- restore pass -->

                <p class="submit">
                    <input type="submit" name="login" class="button" value="Log In" style="margin-top: 17px"/>
                </p>
                <p style="font-size: 13px;   margin-bottom: 2px;    color: #777;">No account yet? <a
                            href="register.php">Register Here</a>!</p>
                <p style="font-size: 13px;    margin-top: 2px;    color: #777;">Forget your password? <a
                            href="restore.php">Restore Here</a>!</p>
            </form>

        </div>

    </div>

<?php include("includes/footer.php"); ?>

<?php if (!empty($message)) {
    echo "<p class=\"error\">" . "MESSAGE: " . $message . "</p>";
} ?>