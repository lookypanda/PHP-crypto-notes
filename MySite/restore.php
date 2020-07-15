<?php
session_start();
?>

<?php require_once("includes/connection.php"); ?>

<?php include("includes/header.php"); ?>

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
<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require 'vendor/autoload.php';


if (isset($_POST["send"])) {

    if (!empty($_POST['mail']) && !empty($_POST['captcha'])) {
        //$dest_mail = $_POST['mail'];
        $captcha = $_POST['captcha'];
        $input_text = trim($_POST['mail']);
        $input_text = htmlspecialchars($input_text);
        $input_text = mysqli_real_escape_string ($con,$input_text);
        $dest_mail=$input_text;
        if ($captcha == 4) {
            //$message = "captcha correct";


            $sql = "SELECT * FROM usertbl WHERE email='" . $dest_mail . "'";
            $query = mysqli_query($con, $sql) or trigger_error(mysqli_error($con) . " in " . $sql);

            $numrows = mysqli_num_rows($query);
            if ($numrows != 0) {
                $restor_msg = "";
                while ($row = mysqli_fetch_assoc($query)) {

                    $dbusername = $row['username'];
                    $dbpassword = $row['password'];
                    $hash_login=password_hash($dbusername,PASSWORD_DEFAULT);
                    $restor_msg = $restor_msg . "login: " . $dbusername . "<br/>link: <a href=http://localhost/new_pass.php?hash_login=".$hash_login."&hash_pass=".$dbpassword.">Recover password</a>: " . $dbpassword . "<br/><br/>";

                }
                $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
                try {
                    //Server settings
                    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'jljrmanjuk@gmail.com';                 // SMTP username
                    $mail->Password = 'jljrman170897';                          // SMTP password
                    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 587;                                    // TCP port to connect to

                    //Recipients
                    $mail->setFrom('jljrmanjuk@gmail.com', 'My Notes restore password');
                    //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
                    $mail->addAddress($dest_mail);               // Name is optional
                    //$mail->addReplyTo('jljrmanjuk@gmail.com', 'Information');
                    //$mail->addCC('jljrmanjuk@gmail.com');
                    //$mail->addBCC('jljrmanjuk@gmail.com');

                    //Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                    //Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Login restore';
                    $mail->Body = $restor_msg;
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                    $mail->send();
                    $message = 'Message has been sent';
                    if (isset($_POST["mobile"]))
                    {
                        echo "<div id='OK'>OK</div>";
                    }else{
                        header("Location: login.php");
                    }

                } catch (Exception $e) {
                    $message = 'Message could not be sent. \n Mailer Error: ' . $mail->ErrorInfo;
                }
            } else {
                if (isset($_POST["mobile"]))
                {
                    echo "<div id='Err'>Err</div>";
                }
                $message = "Wrong email!";

            }


        } else {
            if (isset($_POST["mobile"]))
            {
                echo "<div id='Err'>Err</div>";
            }
            $message = "Wrong answer!";
        }


    } else {
        if (isset($_POST["mobile"]))
        {
            echo "<div id='Err'>Err</div>";
        }
        $message = "All fields are required!";
    }
}

?>


    <div class="container mlogin">
        <div id="Restore">
            <h1>Restore password</h1>
            <form name="loginform" id="loginform" action="" method="POST">
                <p>
                    <label for="e-mail">E-mail<br/>
                        <input type="text" name="mail" id="mail" class="input" value="" size="32"/></label>
                </p>
                <p>
                    <label for="captcha">Enter the answer: 2+2 = <br/>
                        <input type="captcha" name="captcha" id="captcha" class="input" value="" size="32"/></label>
                </p>


                <p class="submit">
                    <input type="submit" name="send" class="button" value="Send to e-mail"/>
                </p>
                <p  style="font-size: 13px;    margin-top: 16px; margin-bottom: 2px;    color: #777;">Recall your password?<br> <a href="login.php">Login Here</a>!</p>

            </form>

        </div>

    </div>

<?php include("includes/footer.php"); ?>

<?php if (!empty($message)) {
    echo "<p class=\"error\">" . "MESSAGE: " . $message . "</p>";
} ?>