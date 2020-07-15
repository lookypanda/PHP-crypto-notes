<?php require_once("includes/connection.php"); ?>
<?php
function send_notes($con, $login)
{
    $global_sort_mode = 1;
    mysqli_select_db($con, "users_notes") or die("Cannot select NOTE DB");

    if (isset($_SESSION["sort_mode"]))
        $global_sort_mode = $_SESSION["sort_mode"];

    if (isset($_POST['CreateDateSort'])) {
        $global_sort_mode = 1;
        $_SESSION['sort_mode'] = $global_sort_mode;


    }
    if (isset($_POST['DeadlineDateSort'])) {
        $global_sort_mode = 0;
        $_SESSION['sort_mode'] = $global_sort_mode;


    }

//$user_name = $_SESSION['session_username'];
    $input_text = trim($login);
    $input_text = htmlspecialchars($input_text);
    $input_text = mysqli_real_escape_string($con, $input_text);
    $user_name = $input_text;
    if ($global_sort_mode == 0) {

        $sql2 = "SELECT * FROM notes WHERE user='" . $user_name . "' AND todo_data IS NOT NULL" . " ORDER BY todo_data ASC;";
        $query2 = mysqli_query($con, $sql2) or trigger_error(mysqli_error($con) . " in " . $sql2);
        $numrows2 = mysqli_num_rows($query2);
        $note_mass2 = mysqli_fetch_all($query2);
        $sql = "SELECT * FROM notes WHERE user='" . $user_name . "'AND todo_data IS NULL";

    } else {
        $sql = "SELECT * FROM notes WHERE user='" . $user_name . "';";
    }


    $query = mysqli_query($con, $sql) or trigger_error(mysqli_error($con) . " in " . $sql);
    $numrows = mysqli_num_rows($query);
    $note_mass = mysqli_fetch_all($query);
    if ($global_sort_mode == 0) {
        $numrows = $numrows + $numrows2;
        $note_mass = array_merge($note_mass2, $note_mass);
    }


//код формирования строк заметок

    echo "<div id='numrows'>$numrows</div>";
    echo "<!-- содержание заметок -->";
    echo "<div id='all_notes'>";
    for ($temp = 0;
         $temp < $numrows;
         $temp++) {
        echo "<!-- начало заметки -->";
        $border_color = "border_color0";
        $warning_text = "";
        if ($note_mass[$temp][3] != Null) {
            $date1 = new DateTime("now");
            $date2 = new DateTime($note_mass[$temp][3]);
            $interval = $date1->diff($date2);
            $warning_time = $interval->m * 30 * 24 + $interval->d * 24 + $interval->h;
            if ($date1 > $date2) {
                $color = "color1";
                $warning_text = "Deadline is OVER";
                $border_color = "border_color1";
            } elseif ($warning_time >= 24 * 28 * 12) {
                $border_color = "border_color5";
                $color = "color5";
                $warning_text = "Till deadline: " . $interval->y . " y. " . $interval->m . "month" . $interval->days . " d.  " . $interval->h . " h.";
            } elseif ($warning_time >= 24 * 28) {
                $border_color = "border_color5";
                $color = "color5";
                $warning_text = "Till deadline: " . $interval->m . " m. " . $interval->days . " d. " . $interval->h . " h." . "</span></h5>";
            } elseif ($warning_time >= 73) {
                $border_color = "border_color4";
                $color = "color4";
                $warning_text = "Till deadline: " . $interval->days . " days   " . $interval->h . " hours";
            } elseif ($warning_time > 25) {
                $border_color = "border_color3";
                $color = "color3";
                $warning_text = "Till deadline: " . $interval->days . " days   " . $interval->h . " hours" . "</span></h5>";
            } elseif ($warning_time <= 25) {
                $border_color = "border_color2";
                $color = "color2";
                $warning_text = "Till deadline: " . $interval->h . " hours" . "</span></h4>";
            }
            //echo "<script type=\"text/javascript\">","gtag('event','page_view'{'page_path':'/addtocartvirt'});", "</script>";

        }

        ?>
        <div class="note_card <?php echo $border_color ?>" id="note_card_<?php echo($note_mass[$temp][4]); ?>">
            <div class="Id"><?php echo($note_mass[$temp][4]); ?></div>
            <div class="username"><?php echo($note_mass[$temp][0]); ?></div>

            <?php if ($note_mass[$temp][3] != Null) { ?>
                <div class="time_left"><?php echo $warning_text; ?></div>
                <div class="deadline_date"><?php echo($note_mass[$temp][3]); ?></div>
                <div class="deadline_color"><?php echo $color; ?></div>
                <?php
            }
            $disp_date = new DateTime($note_mass[$temp][2]); ?>
            <div class="create_date"><?php echo $disp_date->format("d.m.y H:i") ?></div>


            <?php if ($note_mass[$temp][5] == "1") { ?>
                <div class="encrypt">1</div>
                <textarea class="text_note" id="msg_text_<?php echo($note_mass[$temp][4]); ?>"
                          readonly><?php echo($note_mass[$temp][1]); ?></textarea>


            <?php } else { ?>
                <div class="encrypt">0</div>
                <textarea class="text_note" id="msg_text_<?php echo($note_mass[$temp][4]); ?>"
                          readonly><?php echo($note_mass[$temp][1]); ?></textarea>
            <?php } ?>


            <form method="post">
                <p class="submit">
                    <input type="hidden" name="Id"
                           value="<?php echo($note_mass[$temp][4]); ?>"/>
                    <input type="hidden" name="UserName"
                           value="<?php echo($note_mass[$temp][0]); ?>"/>
                    <button type="submit" name="Delete" class="rounded  bg-light border-0"
                            value="Delete"
                            title="Delete"></button>
                </p>
            </form>

            <?php if ($note_mass[$temp][5] == "1") { ?>

                <label>Password<br/>
                    <textarea id="text_pass_<?php echo($note_mass[$temp][4]); ?>" value=""></textarea></label>
                <br>
                <input type="button" class="btn btn-secondary" id="js-note-decrypt2" name=""
                       value=" Decrypt "
                       onclick="decrypt2('<?php echo($note_mass[$temp][1]); ?>','#text_pass_<?php echo($note_mass[$temp][4]); ?>','#msg_text_<?php echo($note_mass[$temp][4]); ?>')"/>
            <?php } ?>
        </div>
    <?php }
    echo "</div>";
//echo($numrows);

}

function sign_mobile($con)
{
    if (isset($_POST['login'])) {
        $login = $_POST['login'];
        if ($login == '') {
            unset($login);
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
            if ($password == '') {
                unset($password);
            }
        }
        $login = trim(htmlspecialchars(stripslashes($login)));
        $password = trim(htmlspecialchars(stripslashes($password)));
        $sql = "SELECT * FROM usertbl WHERE username='" . $login . "'";
        $query = mysqli_query($con, $sql) or trigger_error(mysqli_error($con) . " in " . $sql);

        $numrows = mysqli_num_rows($query);
        if ($numrows != 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $dbusername = $row['username'];
                $dbpassword = $row['password'];
            }

            if ($dbusername && $dbpassword && $login == $dbusername && password_verify($password, $dbpassword)) {

                session_start();

                $_SESSION['session_username'] = $login;


                echo "<div id='OK'>good</div>";
                /* Redirect browser */
//header("Location: intropage.php");

            } else {

                echo "<div id='Err'>Invalid username or password!</div>";
            }
        } else {

            echo "<div id='Err'>Invalid username or password!</div>";
        }
    } else {
        echo "<div id='Err'>All fields are required!</div>";
    }

}


if (!empty($_POST["sign_mobile"]) && $_POST["sign_mobile"] == "sign_mobile") {
    sign_mobile($con);
    if (isset($_SESSION['session_username']))
        send_notes($con, $_SESSION['session_username']);
} else {
    if (isset($_POST['Delete'])) {
        sign_mobile($con);
        //echo($_POST['Id']);
        if (isset($_SESSION['session_username'])) {
            mysqli_select_db($con, "users_notes") or die("Cannot select NOTE DB");

            $input_text = trim($_SESSION['session_username']);
            $input_text = htmlspecialchars($input_text);
            $input_text = mysqli_real_escape_string($con, $input_text);
            $user_name = $input_text;

            $input_text2 = trim($_POST['Id']);
            $input_text2 = htmlspecialchars($input_text2);
            $input_text2 = mysqli_real_escape_string($con, $input_text2);
            $id = $input_text2;
            $sql = "DELETE FROM notes  WHERE ID ='$id' && user='$user_name'";
            $query = mysqli_query($con, $sql) or trigger_error(mysqli_error($con) . " in " . $sql);
            send_notes($con, $user_name);

        }


    }
    if (isset($_POST['Text'])) {
        //if (!empty($_POST['text'])) {
        sign_mobile($con);
        if (isset($_SESSION['session_username'])) {
            mysqli_select_db($con, "users_notes") or die("Cannot select NOTE DB");
            $input_text = trim($_SESSION['session_username']);
            $input_text = htmlspecialchars($input_text);
            $input_text = mysqli_real_escape_string($con, $input_text);
            $user_name = $input_text;

            $input_text2 = trim($_POST['text']);
            $input_text2 = htmlspecialchars($input_text2);
            $input_text2 = mysqli_real_escape_string($con, $input_text2);
            $text = $input_text2;
            if (isset($_POST['EncryptedCheck1'])) {
                $EncryptedCheck = $_POST["EncryptedCheck1"];
                if ($EncryptedCheck == "on")
                    $EncryptedCheck = 1;
                else $EncryptedCheck = 0;
            } else $EncryptedCheck = 0;
            if (isset($_POST['deadline_date'])) {
                $dedline_date = $_POST["deadline_date"];
                if ($dedline_date) {
                    $sql = "INSERT INTO notes (user, note_text,Encrypted,todo_data) 	VALUES('$user_name','$text','$EncryptedCheck','$dedline_date')";
                } else
                    $sql = "INSERT INTO notes (user, note_text,Encrypted) 	VALUES('$user_name','$text','$EncryptedCheck')";

            } else
                $sql = "INSERT INTO notes (user, note_text,Encrypted) 	VALUES('$user_name','$text','$EncryptedCheck')";
            $result = mysqli_query($con, $sql);


            send_notes($con, $user_name);
        }
        //}
        // header("Location: " . $_SERVER['REQUEST_URI']);
    }

}


