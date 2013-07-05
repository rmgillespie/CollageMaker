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
	<script type="text/javascript">	
		constructFriendIDArray(<?php echo $User_ID;?>);
	</script>

	<div id='FacebookAppContainer'>
		<div class='FacebookAppContainerPadding'>
			<div id='FriendSelector'>
				<p style='text-align:center; text-decoration:underline;'><b>Select friends:</b></p>
				<input style="-moz-border-radius:5px; -moz-border-radius:5px; border-radius:5px;
	border-radius:5px; border:1px solid #000; width:100%; color:#3B7DC1;" name='search' value='' id='id_search' placeholder='Search'/>
				<div style='height:200px; width:100%; overflow:auto; border:1px solid #000;'>
					<table id='FacebookFriends' class='tablesorter' style="width:100%;" cellspacing="0" cellpadding="3" rules="rows">
						<thead><tr>
							<th class='sort-alpha' style="text-align:center; height:15px; width:198px; background-color:#FFF; cursor:pointer;" align="center">
								<p href="#" style='font-size:13px; color:#000;'> Name</p>
							</th>
						</tr></thead>
						<tbody>
							<?php
							for ($i = 0; $i < count($Friends['data']); $i++) {
								$Friend_ID = $Friends['data'][$i]['id'];
								echo "<tr id='" . $Friend_ID . "' class='NotSelected' style='cursor:pointer;'>
									<td id='" . $Friend_ID . "Cell' class='FriendCellNotSelected' onclick='GetRowID(this)' align='center' style='padding:0; margin:0; height:15px;'>
										<font size='2'>" . $Friends['data'][$i]['name'] . "</font>
									</td>
								</tr>";
							} ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div id='AdvancedOptions'>
				<p style='text-align:center; text-decoration:underline;' class='MenuHeader'><b>Options:</b></p>
				<input id='usercheckbox' type='checkbox' value='<?php echo $User_ID;?>' onChange='MyPhotosCheckBoxModifed(<?php echo $User_ID;?>)' checked /><p style='display:inline; font-size:12px;'> Include my photos</p>
				<input id='onlytaggedphotos' type='checkbox' checked /><p style='display:inline; font-size:12px;'> Tagged photos only</p>
				<input id='blackandwhite' type='checkbox'/><p style='display:inline; font-size:12px;'> Black and white</p><br/>
				
				
				<p style='display:inline; font-size:12px;'><b>Border color: </b></p>
				<select style="color:#3B7DC1; border:1px solid #000;" id='bordercolor'>
					<option value='Black' selected='selected'>Black</option><option value='Blue'>Blue</option><option value='Green'>Green</option><option value='Grey'>Grey</option><option value='Orange'>Orange</option><option value='Pink'>Pink</option><option value='Purple'>Purple</option><option value='Red'>Red</option><option value='White'>White</option><option value='Yellow'>Yellow</option>
				</select>
				<p style='display:inline; font-size:12px;'><b>Number of rows: </b></p><input class="BoxFormat" id='rows' style='display:inline;' type='text' size='1' onChange='Validate(this)' value='5'/><p style='display:inline; font-size:10px;'> Min: 1 Max: 10</p><br/>
				
				<div style="display:none;">
					<p style='display:inline; font-size:12px;'><b>Width: </b></p><input class="BoxFormat" id='bordersize' style='display:inline;' type='text' size='1' onChange='Validate(this)' value='5'/><p style='display:inline; font-size:10px;'> Min: 0px Max: 10px</p><br/>
				</div>
				
				<p class="MenuHeader"><b>Size:</b></p><hr class="MenuBreaker"/>
				
				<input type='radio' name='collagesize' onChange='radioSizeChanged(this)' value='800' /><p style='display:inline; font-size:10px;'> Small</p>
				<input type='radio' name='collagesize' onChange='radioSizeChanged(this)' value='1280' checked/><p style='display:inline; font-size:10px;'>  Medium</p>
				<input type='radio' name='collagesize' onChange='radioSizeChanged(this)' value='1980'/><p style='display:inline; font-size:10px;'>  Large</p>
				<div id='CustomSizeOptions'>
					<p style='display:inline; font-size:12px;'><b>Height: </b></p><input id='height' class="BoxFormat" style='display:inline;' type='text' size='3' onChange='Validate(this)' value='1280'/><p style='display:inline; font-size:10px;'> Min: 800px Max: 1980px</p><br/>
					<p style='display:inline; font-size:12px;'><b>Width: </b></p><input id='width' class="BoxFormat" style='display:inline;' type='text' size='4' onChange='Validate(this)' value='1280'/><p style='display:inline; font-size:10px;'> Min: 800px Max: 1980px</p><br/>
				</div>
				
				<p class="MenuHeader"><b>Other:</b></p><hr class="MenuBreaker"/>

				<input id='sendemail' type='checkbox' onChange='SendEmailChanged()'/><p style='display:inline; font-size:12px;'> Email me the image</p><br/>
				<div id='EmailField'><p style='display:inline; font-size:12px;'><b>Email: </b></p><input class="BoxFormat" id='email' style='display:inline;' type='text' size='25' onKeyPress='Validate(this)' onChange='Validate(this)' value='' placeholder='Enter your email'/></div>

				<div id='Output' style='color:#FF0000;'></div>
			</div>
			
			<input id='submit' type='submit' name='button' style='height:24px; width:100%; margin-top:5px;' value='Create collage!' onClick='Done("<?php echo getenv("CreateCollagePage"); ?>")'/>
			<div id='NoScriptContainer'>
				<noscript><p style='display:inline; font-size:12px;'>Javascript is required for this app to work. Find out how to enable it </p><a href='http://enable-javascript.com/' style='display:inline;'>here</a><p style='display:inline; font-size:12px;'>.</p></noscript>
			</div>
		</div>
	</div>

	<div class='Section'>
		<div class='SectionHeader'>
			<p class='HeaderText'><b>Most downloaded</b></p>
		</div>
		<?php echo MostDownloaded($User_ID, $Friends); ?>
	</div>

	<?php
	$Friend_Str = '';
	$No_Of_Friends_Images = 0;
	for ($i = 0; $i < count($Friends['data']); $i++) { // For every friend of the user
		$Directory = 'imgs/' . $Friends['data'][$i]['id'] . '/';
		if (is_dir($Directory)) { // True if thumbnail directory exists
			$Images = glob($Directory . "*.png"); // All thumbnails to array
			$No_Of_Friends_Images += count($Images);
			//$Friend_Str .= '<p font-size:12px;><b>' . $Friends['data'][$i]['name'] . ':</b></p>';
			foreach ($Images as $Image) {
				$Img_Src_Array = explode("/", $Image);
				$Thumbnail_Src = 'imgs/' . $Img_Src_Array[1] . '/Thumbnails/' . $Img_Src_Array[2];
				$Friend_Str .=  "<div class='showhim' style='width:100px; height:100px; display:inline-block;'>
					<a title='View collage' alt='Collage photo - " . $Image . "' href='View.php?View=" . $Image . "'>
					<img style='padding:4px 2px 4px 2px;' width='96px' height='92px' src='" . $Thumbnail_Src . "'/></a>
					
					<form class='showme' style='margin-left:2px; margin-right:2px; width:96px; height:20px; position:relative; bottom:101px;' action='Image.php?Url=" . $Image . "' method='POST'>
						<input type='hidden' name='Url' value='" . $Image . "'/>
						<input style='width:96px; height:20px;' type='submit' name='Type' value='Download'/>
					</form>
				</div>";
			}
		}
	}

	$User_Str = '';
	//$Download_All_HTML = '';
	$No_Of_User_Images = 0;
	$Directory = "imgs/" . $User_ID . '/';
	if (is_dir($Directory)) { // True if user directory exists
		$Images = glob($Directory . "*.png"); // All thumbnails to array
		$No_Of_User_Images = count($Images);	
		/*if ($No_Of_User_Images > 2) { // If at least two image exists, include download all button
			$Download_All_HTML = "<form style='width:100px;' action='Image.php?Url=ALL&ID=" . $User_ID . "' method='POST'>
				<input style='width:100px;' type=submit name='Type' value='Download All'/>
			</form>";
		}*/
		//<p style='font-size:10px;'>" . date ("F d Y H:i:s", filemtime($Image)) . "</p>
		foreach ($Images as $Image) {
			$Img_Src_Array = explode("/", $Image);
			$Thumbnail_Src = 'imgs/' . $Img_Src_Array[1] . '/Thumbnails/' . $Img_Src_Array[2];
			$User_Str .=  "<div class='showhim' style='width:100px; height:100px; display:inline-block;'>
					<a title='View collage' alt='Collage image - " . $Image . "' href='View.php?View=" . $Image . "'>
						<img style='padding:4px 2px 4px 2px;' width='96px' height='92px' src='" .  $Thumbnail_Src . "'/>
					</a>
					
					<form class='showme' style='margin-left:2px; margin-right:2px; width:96px; height:20px; position:relative; bottom:27px;' action='Image.php' method='POST'>
						<input type='hidden' name='ImagePath' value='" . $Image . "'/>
						<input type='hidden' name='UserID' value='" . $User_ID . "'/>
						<input style='width:96px; height:20px;' type='submit' name='Type' value='Delete'/>
					</form>

					<form class='showme' style='margin-left:2px; margin-right:2px; width:96px; height:20px; position:relative; bottom:121px;' action='Image.php?Url=" . $Image . "' method='POST'>
						<input type='hidden' name='Url' value='" . $Image . "'/>
						<input style='width:96px; height:20px;' type='submit' name='Type' value='Download'/>
					</form>
				</div>";
		}
	} ?>

	<div class='Section'>
		<div class='SectionHeader'>
			<p class='HeaderText'><b>Your collages (<font class='RedCounter'><b><?php echo $No_Of_User_Images; ?></b></font>)</p>
		</div>
		<?php if ($No_Of_User_Images > 0) { 
			echo "<div style='width:100%; height:117px; overflow:auto; overflow-x:auto; overflow-y:hidden; white-space:nowrap;'>" . $User_Str . "</div>";
		} ?>
	</div>

	<div class='Section'>
		<div class='SectionHeader'>
			<p class='HeaderText'><b>Collages your friends have made  (<font class='RedCounter'><b><?php echo $No_Of_Friends_Images; ?></b></font>)</p>
		</div>
		<?php if ($No_Of_Friends_Images > 0) { 
			echo "<div style='width:100%; height:117px; overflow:auto; overflow-x:auto; overflow-y:hidden; white-space:nowrap;'>" . $Friend_Str . "</div>";
		} ?>
	</div>

	<?php	
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
	</div>
		
	<div class='Section'>
		<div class='SectionHeader'>
			<p class='HeaderText'><b>Your collages</b></p>
		</div>
		<p style='line-height:30px;	text-align:center; font-size:11px;'>Please login to enable this feature.</p>
	</div>
	
	
	<div class='Section'>
		<div class='SectionHeader'>
			<p class='HeaderText'><b>Collages your friends have made</b></p>
		</div>
		<p style='line-height:30px;	text-align:center; font-size:11px;'>Please login to enable this feature.</p>
	</div>
	<?php
} ?>

<?php include 'SupportingFiles/Footer.php'; ?>

<div id="Spinner" class="LoadSpinnerContainer"  style="display:none;">
	<div class="bar">
		<i class="sphere"></i>
		<div class="LoadingText">
			<p style="text-align: center; font-family:'Carrois Gothic', sans-serif; font-size:1em; color:#FFF; text-shadow:0 .05em rgba(255,255,255,0.7);">Please Wait.</p>
		</div>
	</div>
</div>
<div id="Overlay" style="display: none;"></div>
</body>
</html>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


