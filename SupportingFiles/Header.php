<?php require_once 'SupportingFiles/PHPFunctions.php'; require_once "Classes/Facebook_Login.class.php";
/**************** Facebook ***************/
$FB = new Facebook_Login(); // Create new FB login class
$Logged_In = $FB->LogIn(); // Check if user is logged in
/************ End of Facebook ************/ ?>

<div style='height:20px; width:55%; min-width:400px; max-width:1000px; margin:0 auto;'> 
	<div style='float:left; font-size:14px; position:relative; top:0px; right:0px;'>Collages made (<span class='RedCounter'><?php echo TotalNumberOfCollages(); ?></span>)</div> <?php 
if ($Logged_In) { ?>
		<div  style='float:right; font-size:14px; position:relative; top:0px; right:0px;'>Logged in as <b><?php echo $FB->User_Name; ?></b>. (<?php echo '<a href="' . $FB->Logout_URL . '">Logout</a>'; ?>)</div> <?php 
} ?>
</div>

<div id='Header'>
	<!--<div style='position:relative; text-align:center; left:0px; right:0px; top:0px; bottom:0px;'>
		<p class='WhiteTextCenter' style="display:inline;">Collages made (<span class='RedCounter'><?php echo TotalNumberOfCollages(); ?></span>)</p>
	</div>!-->
	
	<div style="position:relative; bottom:0px; height:30px;">
		<div style='float:left; padding-top:5px; padding-left:9px;'>
			<g:plusone size='medium'></g:plusone>
		</div>

		<div style='float:right; padding-top:5px; padding-right:0px; width:85px;'>
			<a style="float:right;" href='https://twitter.com/share' class='twitter-share-button' data-count='horizontal'>Tweet</a>
			<script type='text/javascript' src='//platform.twitter.com/widgets.js'></script>
		</div>
	</div>
	
	<div style='position:relative; bottom:19px; text-align:center; color:#FFF;"'>
		<a title='Home' href='<?php echo getenv("HomePage"); ?>'>
			<img alt='Logo' width='170' height='50' src='Images/Logo.png'/>
		</a>
	</div>
	<!--<p style='position:relative; bottom:19px; text-align:center; color:#FFF; line-height:40px;"'>
		<a title='Home' href='<?php echo getenv("HomePage"); ?>' class='HeaderText'>Collage Maker</a>
	</p>!-->
</div>