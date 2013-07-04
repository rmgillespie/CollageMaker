<?php require_once 'SupportingFiles/PHPFunctions.php'; require_once "Classes/Facebook_Login.class.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Create Collage</title>
		<?php include 'SupportingFiles/RequiredSources.html'; ?>
    </head>
		<script>
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=165398066942056";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
<body>
<div id="fb-root"></div>
<?php
include 'Header.php';
/**************** Facebook ***************/
$FB = new Facebook_Login(); // Create new FB login class
$Logged_In = $FB->LogIn(); // Check if user is logged in
/************ End of Facebook ************/

if ($Logged_In) { // User is logged in ?>
	<div id="FacebookAppContainer">
		<div class='FacebookAppContainerPadding'> <?php
			if (isset($_REQUEST['Name']) && isset($_REQUEST['Email']) && isset($_REQUEST['Message'])) {
				$Name = $_REQUEST['Name'];
				$Email = preg_replace('/[\0\n\r\|\!\/\<\>\^\$\%\*\&]+/','',$_REQUEST['Email']);
				$Message = $_REQUEST['Message'];
				
				if (mail('admin@ryangillespie.co.uk', 'Feedback Form - ryangillespie.co.uk (Collage Maker)', $Message, "From: $Email\n")) {
					echo '<p style="text-align:center">Thankyou for your feedback.</p>
					<p style="text-align:center">You will be redirected automatically within a few seconds.</p>
					<script type="text/javascript">setTimeout("Redirect(\'' . getenv("HomePage") . '\')",3000);</script>';
				} else {
					echo '<p style="text-align:center;">1An error occurred when sending an email to '.$User_Email.'</p>' . $Redirect_HTML;
				}
			} else {
				echo '<p style="text-align:center;">2An error occurred when sending an email to '.$User_Email.'</p>' . $Redirect_HTML;
			} ?>
			<div id='NoScriptContainer'>
				<noscript><p style='display:inline; font-size:12px;'>Javascript is required for this app to work. Find out how to enable it </p><a href='http://enable-javascript.com/' style='display:inline;'>here</a><p style='display:inline; font-size:12px;'>.</p></noscript>
			</div> 
		</div>
	</div> <?php
} else { // User is not logged in ?>
	<div id="FacebookAppContainer">
		<div class='FacebookAppContainerPadding'>
			<div style="margin:0 auto; height:21px; width:100%; margin-top:50px; margin-bottom:50px;">
				<div style="margin:0 auto; width:100%; text-align:center;">
					<a href="<?php echo $FB->Login_URL; ?>">Login to Facebook to continue.</a>
				</div>
			</div>
			<div id='NoScriptContainer'>
				<noscript><p class='HeaderText'>Javascript is required for this app to work. Find out how to enable it </p><a href='http://enable-javascript.com/' style='display:inline;'>here</a><p style='display:inline; font-size:12px;'>.</p></noscript>
			</div>
		</div>
	</div> <?php
}

include 'Footer.php'; ?>
</body>
</html>