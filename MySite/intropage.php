<?php include("includes/intro_header.php"); ?>

<link rel="stylesheet" href="css/dashboard.css">

<body>

<?php
if (isset($_POST['sess_id'])) {
    $input_text = trim($_POST['sess_id']);
    $input_text = htmlspecialchars($input_text);
    $input_text = mysqli_real_escape_string($con, $input_text);
    session_id($_POST['sess_id']); //starts session with given session id
    session_start();
    $_SESSION['count']++;
} else {
    session_start();
}

if (!isset($_SESSION["session_username"])) {
    header("location:login.php");
} else {


    include("includes/intro_header.php");

    require_once("includes/connection.php");
    mysqli_select_db($con, "users_notes") or die("Cannot select NOTE DB");

    $global_sort_mode = 1;
    if (isset($_SESSION["sort_mode"]))
        $global_sort_mode = $_SESSION["sort_mode"];


    if (isset($_POST['Delete'])) {
        $user_name = $_SESSION['session_username'];
        if ($user_name == $_POST['UserName']) {
            //echo($_POST['Id']);
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

            header("Location: " . $_SERVER['REQUEST_URI']);//header("Location: " . $_SERVER['REQUEST_URI']);

        }

    }
    if (isset($_POST['CreateDateSort'])) {
        $global_sort_mode = 1;
        $_SESSION['sort_mode'] = $global_sort_mode;
        header("Location: " . $_SERVER['REQUEST_URI']);

    }
    if (isset($_POST['DeadlineDateSort'])) {
        $global_sort_mode = 0;
        $_SESSION['sort_mode'] = $global_sort_mode;
        header("Location: " . $_SERVER['REQUEST_URI']);

    }

    if (isset($_POST['Text'])) {
        //if (!empty($_POST['text'])) {
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


        header("Location: " . $_SERVER['REQUEST_URI']);

        //}
        // header("Location: " . $_SERVER['REQUEST_URI']);
    } ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/crypto-js/3.1.2/components/core.js"></script>
    <script src="https://cdn.bootcss.com/crypto-js/3.1.2/components/enc-base64.js"></script>
    <script src="https://cdn.bootcss.com/crypto-js/3.1.2/rollups/tripledes.js"></script>
    <script src="https://cdn.bootcss.com/crypto-js/3.1.2/components/mode-ecb.js"></script>
    <script src="https://cdn.bootcss.com/crypto-js/3.1.2/components/pad-nopadding.js"></script>
    <script type="text/javascript">
        //var key = 'E821752166E916AEEF940855';
        //CBC Mode encryption


        function encryptByDESModeCBC(message, key) {
            var ivHex = 'E8217521';//CryptoJS.enc.Utf8.parse(key);
            console.log(ivHex);
            var keyHex = CryptoJS.enc.Utf8.parse(key);
            var encrypted = CryptoJS.DES.encrypt(message, keyHex, {
                mode: CryptoJS.mode.ECB,
                padding: CryptoJS.pad.Pkcs7
            });
            console.log(encrypted.toString());
            return encrypted.toString();

        }


        function decryptByDESModeCBC(ciphertext, key) {
            var keyHex = CryptoJS.enc.Utf8.parse(key);
            // direct decrypt ciphertext
            var decrypted = CryptoJS.DES.decrypt({
                ciphertext: CryptoJS.enc.Base64.parse(ciphertext)
            }, keyHex, {
                mode: CryptoJS.mode.ECB,
                padding: CryptoJS.pad.Pkcs7
            });
            return decrypted.toString(CryptoJS.enc.Utf8);
        }

        $(document).on('click', '#js-note-encypt', function () {
            var source = $("#text").val();
            var key = $("#text_pass").val();
            if (key == "") {
                alert("wrong pass");
                return
            }
            var cripttext = encryptByDESModeCBC(CryptoJS.enc.Utf8.parse(source), CryptoJS.enc.Utf8.parse(key));
            $("#text").val(cripttext);
        });
        $(document).on('click', '#js-note-decrypt', function () {
            var source = $("#text").val();
            var key = $("#text_pass").val();
            if (key == "") {
                alert("wrong pass");
                return
            }
            var cripttext = decryptByDESModeCBC(source, CryptoJS.enc.Utf8.parse(key));
            $("#text").val(cripttext);
        });

        function decrypt2(source, pass, destination) {

            var key = $(pass).val();
            var cripttext = decryptByDESModeCBC(source, CryptoJS.enc.Utf8.parse(key));
            if (key == "") {
                alert("wrong pass");
                return
            }
            //alert(cripttext);
            $(destination).val(cripttext);
            $(destination).show();
        };


    </script>

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


    <!--
    -->
    <!-- Код получения заметок с сервера
    -->
<?php
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

//echo($numrows);
?>

    <div class="container-fluid text-center ">
        <div class="row ">


            <!-- desktop sidebar-->
            <nav class="col-xl-2 col-lg-3 col-md-4  col-sm-5  d-md-block bg-light sidebar" id="side_bar_col">


                <div class="sidebar-sticky">
                    <!-- Image and text -->

                    <a class="navbar-brand mr-0 p-2" href="#" id="main_sidebar">
                        <img src="favicon.ico" width="40" height="40"
                             class="d-inline-block align-top rounded border" alt="">
                        My Note</a>
                    <input class="form-control form-control-dark w-100 shadow-textarea" type="text" placeholder="Search"
                           aria-label="Search" id="search">


                    <script>

                        $(document).ready(function () {
                            $("#wrong_search").hide()
                            $("#search").keyup(function () {
                                _this = this;
                                var count = 0;
                                $.each($("#all_notes_frame div.card "), function () {
                                    if ($(this).find("#note_frame").find("textarea").text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1) {
                                        $(this).hide();
                                    } else {
                                        $(this).show();
                                        count++;
                                    }
                                    ;
                                });
                                if (count === 0 && $(_this).val().toLowerCase() != "") {
                                    $("#wrong_search").show();
                                } else
                                    $("#wrong_search").hide();
                            });
                        });
                    </script>


                    <div class="card" style=" margin-bottom: 15px;margin-top: 1.3rem;">

                        <div class="card-body"><!-- Начало текстового контента -->
                            <a href="logout.php" class="card-link">Logout</a>
                            <h5 class="card-title">Hi, <?php
                                $input_text = trim($_SESSION['session_username']);
                                $input_text = htmlspecialchars($input_text);
                                $input_text = mysqli_real_escape_string($con, $input_text);
                                echo $input_text; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Number of
                                notes: <?php echo($numrows); ?></h6>
                            <?php if ($global_sort_mode == 1) { ?>
                                <form name="DeadlineDateSort" method="POST">
                                    <input type="submit" name="DeadlineDateSort" class="btn btn-primary"
                                           value="Sort by deadline"/>
                                </form>
                            <?php } else { ?>
                                <form name="CreateDateSort" method="POST">
                                    <input type="submit" name="CreateDateSort" class="btn btn-primary"
                                           value="Sort by creation date"/>
                                </form>
                            <?php } ?>
                        </div><!-- Конец текстового контента -->
                    </div><!-- Конец карточки -->

                    <div class="card" style="margin-bottom: 15px;">
                        <div class="card-body" style="  display:inline-block; margin:0 auto;">
                            <div id="sandbox-container" data-provide="datepicker"
                                 data-date-today-highlight="true" data-date-week-start="1">
                            </div>
                        </div>

                    </div>

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
                    <script>
                        function calendar_add(dates) {
                            $('#sandbox-container').datepicker({
                                beforeShowDay: function (date) {
                                    var r = {
                                        enabled: true,
                                        classes: 'CalendarClass',
                                        weekStart: 1,
                                        todayHighlight: true,
                                        format: "dd/mm/yyyy",
                                        startDate: "-7d",
                                        endDate: "+3d"

                                    };

                                    return dates.indexOf(date.toISOString()) != -1 ? r : {
                                        enabled: true,
                                        todayHighlight: true
                                    };
                                }
                            })
                        }


                    </script>

                    <div class="card" style="">
                        <div class="card-body">
                            <div id="note_msg">
                                <label>New note</label>

                                <form name="note_msg_form" id="note_msg_form" action="" method="POST">
                                             <textarea type="text" class="rounded" name="text" id="text" rows="2"
                                                       placeholder="Input some text" value=""
                                                       style="width:198px; height:auto;resize: vertical;"></textarea>
                                    <script>
                                        jQuery.fn.extend({
                                            autoHeight: function () {
                                                function autoHeight_(element) {
                                                    return jQuery(element).css({
                                                        'height': 'auto',
                                                        'overflow-y': 'hidden'
                                                    }).height(element.scrollHeight);
                                                }

                                                return this.each(function () {
                                                    autoHeight_(this).on('input', function () {
                                                        autoHeight_(this);
                                                    });
                                                });
                                            }
                                        });
                                        $('#text').autoHeight();
                                    </script>

                                    <br>

                                    <input type="checkbox" id="EncryptedCheck1_id"
                                           name="EncryptedCheck1" autocomplete="off">
                                    Encrypted&nbsp
                                    <label class="form-check-label" for="exampleCheck1"><input type="checkbox"
                                                                                               id="DeadlineCheck_id"
                                                                                               name="DeadlineCheck">
                                        Deadline</label>
                                    <script>
                                        $(document).ready(function () {
                                            $('#text_pass').prop('hidden', true);
                                            $('#js-note-encypt').prop('hidden', true);
                                            $('#js-note-decrypt').prop('hidden', true);

                                            $('#EncryptedCheck1_id').change(function () {
                                                    $('#text_pass').prop('hidden', function (i, val) {
                                                        return !val;
                                                    })
                                                    $('#js-note-encypt').prop('hidden', function (i, val) {
                                                        return !val;
                                                    })
                                                    $('#js-note-decrypt').prop('hidden', function (i, val) {
                                                        return !val;
                                                    })
                                                }
                                            );
                                        })

                                    </script>


                                    <br>
                                    <textarea id="text_pass" class="rounded " placeholder="Password"
                                              aria-label="Password"
                                              style="resize: none; margin-bottom: 3px" hidden="hidden"></textarea>

                                    <input type="button" class="btn btn-secondary" id="js-note-encypt" name=""
                                           value=" Encrypt " hidden="hidden"/>
                                    <input type="button" class="btn btn-secondary" id="js-note-decrypt" name=""
                                           value=" Decrypt " hidden="hidden"/>


                                    <div class="form-group">

                                        <script>
                                            $(document).ready(function () {
                                                $('#deadline_date').prop('hidden', true);

                                                $('#DeadlineCheck_id').change(function () {
                                                        $('#deadline_date').prop('hidden', function (i, val) {
                                                            return !val;
                                                        })
                                                    }
                                                );
                                            })

                                        </script>

                                        <input type="date" name="deadline_date" id="deadline_date"
                                               class="form-control"
                                               aria-label="Deadline" style="margin-top: 8px" hidden="hidden">
                                    </div>

                                    <p class="submit">
                                        <input type="submit" name="Text" class="btn btn-primary"
                                               value="Submit"/>
                                    </p>


                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <script> var items = [];</script>
            <?php
            //код формирования строк заметок
            echo "<script>";
            for ($temp = 0;
                 $temp < $numrows;
                 $temp++) {
                if ($note_mass[$temp][3] != Null) {
                    $temp_date = str_replace('-', '/', $note_mass[$temp][3]);
                    echo "items.push(new Date('";

                    echo $temp_date . "').toISOString()); \n";
                }
            }
            echo "calendar_add(items); \n </script>";
            ?>


            <main role="main" class="col-xl-10 col-lg-9 col-md-8 col-sm-7 px-4">
                <div class="card-columns " id="all_notes_frame">

                    <!--Заглушка для неудачного поиска -->
                    <div class="card " id="wrong_search" style="display:none; ">
                        <div class="p-3 text-center text-dark  rounded "
                             id="note_frame">
                            Bad search! :(
                        </div>
                    </div>

                    <script>
                        function show_toggle(selector) {
                            $(selector).toggle('show');
                        }
                        function textAreaAdjust(o) {
                            o.style.height = "1px";
                            o.style.height = (25+o.scrollHeight)+"px";
                        }
                    </script>

                    <?php
                    //код формирования строк заметок
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


                        }
                        ?>
                        <div class="card" style="min-width: 250px">
                            <div class="p-3 text-center text-dark  rounded  <?php echo $border_color ?> "
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
                                              style="min-width: 200px height: auto; resize: vertical; display: none; " onclick="textAreaAdjust(this)"
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


                    <?php } ?>
                </div>

            </main>
        </div>
    </div>
<?php } ?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>


</body></html>
