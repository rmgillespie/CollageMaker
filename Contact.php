<?php require_once 'SupportingFiles/PHPFunctions.php'; require_once "Classes/Facebook_Login.class.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Collage Maker</title>
		<?php include 'SupportingFiles/RequiredSources.html'; ?>
		<script>
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=165398066942056";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
		
		<script type="text/javascript">
			function Form_Validation() {
				Feedback_Message = "";
				Name_Regex = /^([a-zA-Z\s]+)$/;
				Email_Regex = /^([\w]+)(.[\w]+)*@([\w]+)(.[\w]{2,3}){1,2}$/;
				Name_Input_Box = document.getElementById('Name_Input_Field');
				Email_Input_Box = document.getElementById('Email_Input_Field');
				Message_Input_Box = document.getElementById('Message_Input_Field');
				if ((Name_Regex.test(document.forms.Contact_Form.Name.value) == true) ) { // If the name input in the form is empty do this
					Name_Input_Box.style.border = "1px solid #000";
					if (Email_Regex.test(document.forms.Contact_Form.Email.value) == true) {
						Email_Input_Box.style.border = "1px solid #000";
						if (document.forms.Contact_Form.Message.value != "") {
							Message_Input_Box.style.border = "1px solid #000";
							valid = true;
						} else { // Message is empty
							Message_Input_Box.style.border = "2px solid #AB0000";
							Feedback_Message += '<p class="Feeback_Message">*Please enter a message.</p>';
						}
					} else { // Email is invlaid
						Email_Input_Box.style.border = "2px solid #AB0000";
						Feedback_Message += '<p class="Feeback_Message">*Your Email appears to be incorrect.</p>';
					}
				} else { // Name is invalid
					Name_Input_Box.style.border = "2px solid #AB0000";
					Feedback_Message += '<p class="Feeback_Message">*Your name is invalid.</p>';
				}
				
				if (Email_Regex.test(document.forms.Contact_Form.Email.value) == true) {
					Email_Input_Box.style.border = "1px solid #000";
				}
				
				if (document.forms.Contact_Form.Message.value != "") {
					Message_Input_Box.style.border = "1px solid #000";
				}
				
				document.getElementById('Validation_Feedback').innerHTML = Feedback_Message;
				if (valid) {
					document.Contact_Form.submit();
				}
				return Valid;
			}
		</script>
	</head>
<body>
<div id="fb-root"></div>
<div class='ViewSourceContainer'>
	<a style='color:#FFF; padding:5px;' href='https://github.com/rmgillespie/CollageMaker'>View source</a>
</div>
<?php
include 'SupportingFiles/Header.php';	

/**************** Facebook ***************/
$FB = new Facebook_Login(); // Create new FB login class
$Logged_In = $FB->LogIn(); // Check if user is logged in
$User_ID = $FB->User_ID;
$Friends = $FB->Friends;
/************ End of Facebook ************/

if ($Logged_In) { // User is logged in ?>
	<div id='FacebookAppContainer'>
		<div class='FacebookAppContainerPadding'>
			<hr width="95%"/>
			<form name="Contact_Form" id="Contact_Form" method="POST" action="SendMail.php"><!--  START of the contact form -->
				<div style="float:left">
					<p align="left">Name:</p>
						<input id="Name_Input_Field" name="Name" type="text" maxlength="40" size="20" style="width:140px; height:16px;"/><br/><br/>
				</div>

				<div style="float:right">
					<p align="left">Your E-mail Address:</p>
						<input id="Email_Input_Field" name="Email" type="text" maxlength="50" size="30" style="width:190px; height:16px;"/><br/><br/>
				</div>

				<br style="clear:both;"/>
				<p>Message:</p>
				<textarea id="Message_Input_Field" name="Message" rows="13%" cols="100%" style="width:99%;"></textarea><br/>

				<div id="Validation_Feedback"></div>
				<input class='Submit_Button' style='height:24px; width:100%; margin-top:5px;' type="button" value="Submit" onclick="Form_Validation()"/>
			</form>

			<div id='NoScriptContainer'>
				<noscript><p class='HeaderText'>Javascript is required for this app to work. Find out how to enable it </p><a href='http://enable-javascript.com/' style='display:inline;'>here</a><p style='display:inline; font-size:12px;'>.</p></noscript>
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
} ?>

<?php include 'SupportingFiles/Footer.php'; ?>
</body>
</html>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


