<?php require_once("includes/connection.php"); ?>
<?php include("includes/header.php"); ?>


<?php

if (isset($_POST["register"])) {


	if (!empty($_POST['full_name']) && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])) {
	    $input_text = trim($_POST['full_name']);
        $input_text = htmlspecialchars($input_text);
        $input_text = mysqli_real_escape_string ($con,$input_text);
        $full_name=$input_text;
	    //$full_name = $_POST['full_name'];

	    $input_text2 = trim($_POST['email']);
        $input_text2 = htmlspecialchars($input_text2);
        $input_text2 = mysqli_real_escape_string ($con,$input_text2);
        $email=$input_text2;
		//$email = $_POST['email'];

		$input_text3 = trim($_POST['username']);
        $input_text3 = htmlspecialchars($input_text3);
        $input_text3 = mysqli_real_escape_string ($con,$input_text3);
        $username=$input_text3;
		//$username = $_POST['username'];

		$input_text4 = trim($_POST['password']);
        $input_text4 = htmlspecialchars($input_text4);
        $input_text4 = mysqli_real_escape_string ($con,$input_text4);
        $password=$input_text4;
		//$password = $_POST['password'];

		// Given password


         // Validate password strength
         $uppercase = preg_match('@[A-Z]@', $password);
         $lowercase = preg_match('@[a-z]@', $password);
         $number    = preg_match('@[0-9]@', $password);
         $specialChars = preg_match('@[^\w]@', $password);

         if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
             $message='Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
         }else{
             //echo 'Strong password.';
             $hash_password=password_hash($password,PASSWORD_DEFAULT);
             if($hash_password){
                 $n1 = $con; #mysqli_connect(DB_SERVER,DB_USER, DB_PASS) or die(mysqli_error());
	             #mysqli_select_db($con,DB_NAME) or die("Cannot select DB");
                    $sql = "SELECT * FROM usertbl WHERE email='" . $email . "'";
	                $query = mysqli_query($n1, $sql) or trigger_error(mysqli_error($n1) . " in " . $sql);
	                $numrows = mysqli_num_rows($query);
                    if($numrows == 0){
                        $sql = "SELECT * FROM usertbl WHERE username='" . $username . "'";
	                    $query = mysqli_query($n1, $sql) or trigger_error(mysqli_error($n1) . " in " . $sql);
	                    $numrows = mysqli_num_rows($query);
                        if ($numrows == 0) {

                                         $sql = "INSERT INTO usertbl (full_name, email, username,password) VALUES('$full_name','$email', '$username', '$hash_password')";
                                         $result = mysqli_query($n1, $sql);
                                         if ($result) {
                                             $message = "Account Successfully Created";
                                             if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='OK'>$message</div>";
                                                }
                                         } else {
                                             $message = "Failed to insert data information!";
                                             if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='Err'>$message</div>";
                                                }
                                         }
                                     } else {
                                         $message = "That username already exists! Please try another one!";
                                         if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='Err'>$message</div>";
                                                }
                                     }
                    } else{
                        $message = "That email already exists! Please try another one!";
                        if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='Err'>$message</div>";
                                                }
                    }

             } else {
                 $message = "Hash err";
                 if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='Err'>$message</div>";
                                                }
             }

         }
	} else {
		$message = "All fields are required!";
		                                             if (isset($_POST["mobile"]))
                                                {
                                                    echo "<div id='Err'>$message</div>";
                                                }
	}
}
?>


<?php if (!empty($message)) {
	echo "<p class=\"error\">" . "MESSAGE: " . $message . "</p>";
} ?>
</script>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(53745645, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/53745645" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
<div class="container mregister">
	<div id="login">
		<h1>REGISTER</h1>
		<form name="registerform" id="registerform" action="register.php" method="post">
			<p>
				<label for="user_login">Full Name<br />
					<input type="text" name="full_name" id="full_name" class="input" size="32" value="" /></label>
			</p>


			<p>
				<label for="user_pass">Email<br />
					<input type="email" name="email" id="email" class="input" value="" size="32" /></label>
			</p>

			<p>
				<label for="user_pass">Username<br />
					<input type="text" name="username" id="username" class="input" value="" size="32" /></label>
			</p>

			<p>
				<label for="user_pass">Password<br />
					<input type="password" name="password" id="password" class="input" value="" size="32" /></label>
			</p>


			<p class="submit">
				<input type="submit" name="register" id="register" class="button" value="Register" />
			</p>

			<p class="regtext">Already have an account? <a href="login.php">Login Here</a>!</p>
		</form>

	</div>
</div>



<?php include("includes/footer.php"); ?>