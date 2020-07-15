<?php require_once("includes/connection.php"); ?>
<?php
function send_notes($con)
{
    echo "<div id='OK'>test0</div>";
    echo "test0";
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
    $input_text = trim($_SESSION['session_username']);
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
    echo "<div id='OK'>test3</div>";
    for ($temp = 0;
         $temp < $numrows;
         $temp++) {
        $border_color = "border_color0";
        $warning_text = "";
        if ($note_mass[$temp][3] != Null) {
            $date1 = new DateTime("now");
            $date2 = new DateTime($note_mass[$temp][3]);
            $interval = $date1->diff($date2);
            $warning_time = $interval->m * 30 * 24 + $interval->d * 24 + $interval->h;
            if ($date1 > $date2) {
                $color = "color1";
                $warning_text = "<h3><span class=\"badge\" id=\"" . $color . "\">Deadline is OVER </span></h3>";
                $border_color = "border_color1";
            } elseif ($warning_time >= 24 * 28 * 12) {
                $border_color = "border_color5";
                $color = "color5";
                $warning_text = "<h6><span class=\"badge text-light\" id=\"" . $color . "\">" . "Until deadline: " . $interval->y . " y. " . $interval->m . "month" . $interval->days . " d.  " . $interval->h . " h." . "</span></h6>";
            } elseif ($warning_time >= 24 * 28) {
                $border_color = "border_color5";
                $color = "color5";
                $warning_text = "<h5><span class=\"badge text-light\" id=\"" . $color . "\">" . "Until deadline: " . $interval->m . " m. " . $interval->days . " d. " . $interval->h . " h." . "</span></h5>";
            } elseif ($warning_time >= 73) {
                $border_color = "border_color4";
                $color = "color4";
                $warning_text = "<h5><span class=\"badge\" id=\"" . $color . "\">" . "Until deadline: " . $interval->days . " days   " . $interval->h . " hours" . "</span></h5>";
            } elseif ($warning_time > 25) {
                $border_color = "border_color3";
                $color = "color3";
                $warning_text = "<h5><span class=\"badge\" id=\"" . $color . "\">" . "Until deadline: " . $interval->days . " days   " . $interval->h . " hours" . "</span></h5>";
            } elseif ($warning_time <= 25) {
                $border_color = "border_color2";
                $color = "color2";
                $warning_text = "<h4><span class=\"badge\" id=\"" . $color . "\">" . "Until deadline: " . $interval->h . " hours" . "</span></h4>";
            }
            echo "<div id='OK'>test</div>";

        }
        echo "<div id='OK'>test1</div>";
        ?>
        <div class="card" style="min-width: 250px">
            <div class="  <?php echo $border_color ?> "
                 id="note_frame">
                <?php echo $warning_text; ?>

                <?php if ($note_mass[$temp][5] == "1") { ?>
                    <button class="rounded  bg-light border-0"
                            onclick='show_toggle("#msg_text_<?php echo($note_mass[$temp][4]); ?>")'>
                        <i class="fas fa-lock"
                           style="    text-shadow: 1px 1px 1px #ccc;    font-size: 1.5em;"></i>
                    </button>
                    <textarea class="form-control z-depth-3 "
                              id="msg_text_<?php echo($note_mass[$temp][4]); ?>"
                              style="min-width: 200px height: auto; resize: vertical; display: none; "
                              onclick="textAreaAdjust(this)"
                              readonly><?php echo($note_mass[$temp][1]); ?></textarea>


                <?php } else { ?>
                    <textarea class="form-control z-depth-3 "
                              id="msg_text_<?php echo($note_mass[$temp][4]); ?>"
                              style="min-width: 200px height: auto; resize: vertical;" onclick="textAreaAdjust(this)"
                              readonly><?php echo($note_mass[$temp][1]); ?></textarea>


                <?php }
                $disp_date = new DateTime($note_mass[$temp][2]); ?>
                <div><?php echo $disp_date->format("d.m.y H:i") ?></div>
                <form method="post">
                    <p class="submit">
                        <input type="hidden" name="Id"
                               value="<?php echo($note_mass[$temp][4]); ?>"/>
                        <input type="hidden" name="UserName"
                               value="<?php echo($note_mass[$temp][0]); ?>"/>

                        <?php if ($note_mass[$temp][3] != Null){ ?>
                    <div class="form-group" style=" margin-bottom: 0px">
                        <label for=" inputDate">Deadline date:<input type="date" name="deadline_date"
                                                                     class="form-control"
                                                                     value="<?php echo($note_mass[$temp][3]); ?>"
                                                                     readonly> </label>

                    </div>

                    <?php } ?>
                    <?php if ($note_mass[$temp][5] == "1") { ?>

                        <label for="note msg">Password<br/>
                            <textarea id="text_pass_<?php echo($note_mass[$temp][4]); ?>" value=""
                                      style="width:auto;height:30px;resize: none;"></textarea></label>
                        <br>
                        <input type="button" class="btn btn-secondary" id="js-note-decrypt2" name=""
                               value=" Decrypt " style=" margin-bottom: 0px"
                               onclick="decrypt2('<?php echo($note_mass[$temp][1]); ?>','#text_pass_<?php echo($note_mass[$temp][4]); ?>','#msg_text_<?php echo($note_mass[$temp][4]); ?>')"/>
                    <?php } ?>
                    <button type="submit" name="Delete" class="rounded  bg-light border-0"
                            value="Delete"
                            title="Delete"
                            style=" position: absolute; bottom: 10px; right: 10px; box-shadow: inset -2px -2px 0 rgba(0, 0, 0, 0.1);">
                        <i class="fas fa-trash"></i></button>
                    </p>
                </form>

            </div>
        </div>


    <?php echo "test";}
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


                $_SESSION['session_username'] = $login;

                echo "<div id='OK'>good</div>";
                /* Redirect browser */
//header("Location: intropage.php");
                send_notes($con);
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
}

