<?php require_once 'SupportingFiles/PHPFunctions.php'; require_once "Classes/Facebook_Login.class.php";
if (!ini_get('safe_mode')) {
    set_time_limit(3500);
} ?>
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
include 'Header.php'; ?>

<div id="FacebookAppContainer">
	<div class='FacebookAppContainerPadding'>
		<div style="margin: 0 auto; width:100%; text-align:center;"> 
	<?php
	/**************** Facebook ***************/
	$FB = new Facebook_Login(); // Create new FB login class
	$Logged_In = $FB->LogIn(); // Check if user is logged in
	$Facebook = $FB->Facebook;
	$User_ID = $FB->User_ID;
	/************ End of Facebook ************/
	
	$Redirect_HTML = '<script type="text/javascript">setTimeout("Redirect(' . getenv("HomePage") . ')",4000);</script><p class="Center">Redirecting in <span id="Timer">4</span> seconds.</p>';
	if ($Logged_In) { // User is logged in
		if (isset($_POST['selectedfriends'])) {
			/* Get details from form */
			$Send_Email_Bool = $_POST['sendemail']; $User_Email = $_POST['email']; $Canvas_Width = $_POST['width'];
			$Canvas_Height = $_POST['height']; $No_Of_Rows = $_POST['rows']; $Border_Color = $_POST['bordercolor']; 
			$Border_Size = $_POST['bordersize']; $Only_Tagged_Photos =  $_POST['onlytaggedphotos']; $Black_Or_White_Bool = $_POST['blackandwhite'];
			$User_Array = explode(",", $_POST['selectedfriends']);

			if (count($User_Array) != 0) { // Ensure at least 1 user was selected	
				$Min_Image_Width =  $Canvas_Width / 100 * 10; // Image constraints based on canvas size and no of rows 10-50% of collage
				$Max_Image_Width =  $Canvas_Width / 100 * 50;
				$Max_Image_Height =  round($Canvas_Height / $No_Of_Rows);		

				$Friends = array();
				$Max_Width_Of_Useable_Photos = 0;
				$Total_No_Of_Useable_Photos = 0;
				for ($i = 0; $i < count($User_Array); $i++) { // For every selected user
					$User_Photos[] = array(); // Stores list of usable photos for user
					$ID = $User_Array[$i];					
					if (($ID == $User_ID) && ($Only_Tagged_Photos == 'true')) {
						$Photos = $Facebook->api('/me/photos?limit=100');
						foreach ($Photos['data'] as $Photo) { // For every photo in the album
							$PhotoID = $Photo['id']; $Width = $Photo['width']; $Height = $Photo['height']; $Source = $Photo['source']; // Get photo details

							$Tagged = false;
							if (isset($Photo['tags'])) {
								for ($x = 0; $x < count($Photo['tags']['data']); $x++) { // For every tag
									if (isset($Photo['tags']['data'][$x]['id'])) {
										if ($Photo['tags']['data'][$x]['id'] == $ID) { // If tag is user
											$Tagged = true;
											break;
										}
									}
								}
							}
					
							$Aspect_Ratio = $Width / $Height;
							$Resized_Height = $Max_Image_Height;
							$Resized_Width = round($Max_Image_Height * $Aspect_Ratio);
							if (($Resized_Width > $Min_Image_Width) && ($Resized_Width < $Max_Image_Width) && ($Tagged)) { // Ensures photo will fit in collage
								$Max_Width_Of_Useable_Photos = $Max_Width_Of_Useable_Photos + $Resized_Width;
								$User_Photos[] = array('id' => $PhotoID, 'width' => $Width, 'height' => $Height, 'AspectRatio' => $Aspect_Ratio, 'ResizedWidth' => $Resized_Width, 'ResizedHeight' => $Resized_Height, 'url' => $Source);
							}
						}
					} else { // Not the user
						$Albums = $Facebook->api('/' . $ID . '/albums'); //Gets the friends photo albums
					/*	echo "<br>No of albums found: " . count($Albums['data']) . "<br>";
						print_r($Albums);*/
						for ($d = 0; $d < count($Albums['data']); $d++) { //For every album user has count number of photos
						
						
							$Photos = $Facebook->api('/' . $Albums['data'][$d]['id'] . '/photos?limit=100');	
							/*echo "<br>No of photos: ".count($Photos['data']) . "<br>";
							if ($Albums['data'][$d]['name'] == 'EuroTrip 2012') {
								echo "<br>Name: " . $Albums['data'][$d]['name'] . " No of photos: ".count($Photos['data']) . "<br>";
							}*/
							
							foreach ($Photos['data'] as $Photo) { // For every photo in the album
								$PhotoID = $Photo['id']; $Width = $Photo['width']; $Height = $Photo['height']; $Source = $Photo['source']; // Get photo details

								if ($Only_Tagged_Photos == "true") { // Check photo has tagged user
									$Tagged = false;
									if (isset($Photo['tags'])) {
										for ($x = 0; $x < count($Photo['tags']['data']); $x++) { // For every tag
											if (isset($Photo['tags']['data'][$x]['id'])) {
												if ($Photo['tags']['data'][$x]['id'] == $ID) { // If tag is user
													$Tagged = true;
													break;
												}
											}
										}
									}
								} else {
									$Tagged = true;
								}

								$Aspect_Ratio = $Width / $Height;
								$Resized_Height = $Max_Image_Height;
								$Resized_Width = round($Max_Image_Height * $Aspect_Ratio);
								if (($Resized_Width > $Min_Image_Width) && ($Resized_Width < $Max_Image_Width) && ($Tagged)) { // Ensures photo will fit in collage
									$Max_Width_Of_Useable_Photos = $Max_Width_Of_Useable_Photos + $Resized_Width;
									$User_Photos[] = array('id' => $PhotoID, 'width' => $Width, 'height' => $Height, 'AspectRatio' => $Aspect_Ratio, 'ResizedWidth' => $Resized_Width, 'ResizedHeight' => $Resized_Height, 'url' => $Source);
								}
							}
						}
					}

					if (count($User_Photos) != 0) { // If photos were found
						$Total_No_Of_Useable_Photos = $Total_No_Of_Useable_Photos + count($User_Photos);
						$Friends[] = array('id' => $ID, 'usablephotos' => count($User_Photos), 'photos' => $User_Photos);
					}
				}
				
				if (($Max_Width_Of_Useable_Photos / $No_Of_Rows) > $Canvas_Width) { // Ensures enough photos are available to create collage
					$Canvas = imagecreatetruecolor($Canvas_Width, $Canvas_Height); // Create canvas
					$Fixed_Image = imagecreatetruecolor($Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size); // Blank image with border
					$Final_Image = imagecreatetruecolor($Canvas_Width, $Canvas_Height); // The final image

					/* Colors */
					$White = imagecolorallocate($Final_Image, 255, 255, 255); $Grey = imagecolorallocate($Final_Image, 128, 128, 128); $Black = imagecolorallocate($Final_Image, 0, 0, 0);
					$Red = imagecolorallocate($Final_Image, 255, 0, 0); $Blue = imagecolorallocate($Final_Image, 0, 0, 255); $Purple = imagecolorallocate($Final_Image, 186, 85, 211);
					$Pink = imagecolorallocate($Final_Image, 255, 181, 197); $Yellow = imagecolorallocate($Final_Image, 255, 255, 0); $Green = imagecolorallocate($Final_Image, 0, 255, 0);
					$Blue = imagecolorallocate($Final_Image, 0, 0, 255); $Orange = ImageColorAllocate($Final_Image, 255, 200, 0);
					
					switch ($Border_Color) {
						case "Red":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Red);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Red);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Red);
							break;
						case "Black":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Black);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Black);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Black);
							break;
						case "Grey":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Grey);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Grey);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Grey);
							break;
						case "White":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $White);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $White);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $White);
							break;
						case "Blue":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Blue);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Blue);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Blue);
							break;
						case "Purple":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Purple);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Purple);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Purple);
							break;
						case "Pink":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Pink);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Pink);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Pink);
							break;
						case "Yellow":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Yellow);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Yellow);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Yellow);
							break;
						case "Green":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Green);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Green);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Green);
							break;
						case "Blue":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Blue);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Blue);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Blue);
							break;
						case "Orange":
							imagefilledrectangle($Canvas, 0, 0, $Canvas_Width, $Canvas_Height, $Orange);
							imagefilledrectangle($Fixed_Image, 0, 0, $Canvas_Width - $Border_Size, $Canvas_Height - $Border_Size, $Orange);
							imagefilledrectangle($Final_Image, 0, 0, $Canvas_Width, $Canvas_Height, $Orange);
							break;
					}
					
					$Photos_To_Add_To_Row[] = array(); // Stores the photos to be added to this row
					$Height_Used = 0;
					$Temp_Row_Height = 0;
					for ($Current_Row = 0; $Current_Row < $No_Of_Rows; $Current_Row++) { // For every row
						unset($Photos_To_Add_To_Row); // Clear array for each row

						$Total_Border_Size = ($Border_Size * ($No_Of_Rows - 1));
						$Row_Height = floor(($Canvas_Height - $Total_Border_Size) / $No_Of_Rows); // Based on total border width
						
						$Offset = 0;
						$Width_Left = $Canvas_Width - $Border_Size;
						$Row_Is_Filled = false;
						while (!$Row_Is_Filled) { // While the row has not been filled
							$Random_Friend_No = GetRandomInteger(0, count($Friends) - 1); 	// Get a random friend
							$ID = $Friends[$Random_Friend_No]['id']; 						// Get the ID of that friend						
							$Random_Photo_No = GetRandomInteger(1, $Friends[$Random_Friend_No]['usablephotos']- 1);
							$Random_Photo = $Friends[$Random_Friend_No]['photos'][$Random_Photo_No]; // Select a random photo
							$Image = imagecreatefromjpeg($Friends[$Random_Friend_No]['photos'][$Random_Photo_No]['url']); // Get photo from FB
							
							$Resized_Width = $Random_Photo['ResizedWidth'] - $Border_Size; // Calculate resized width based on border
							$Resized_Height = $Row_Height; // For human readability
							$Resized_Image = imagecreatetruecolor($Resized_Width, $Resized_Height);

							$Temp_Width = $Width_Left - $Resized_Width;
							imagecopyresampled($Resized_Image, $Image, 0, 0, 0, 0, $Resized_Width, $Row_Height, $Random_Photo['width'], $Random_Photo['height']); // Resize photo
							
							if ($Temp_Width <= $Min_Image_Width) {
								$Current_Distance = $Canvas_Width - $Offset;

								if ($Temp_Width < $Current_Distance) {
									unset($Friends[$Random_Friend_No]['photos'][$Random_Photo_No]); // Removes photo from friend array
									$Friends[$Random_Friend_No]['usablephotos'] = $Friends[$Random_Friend_No]['usablephotos'] - 1; // Update photo counter
									$Friends[$Random_Friend_No]['photos'] = array_values($Friends[$Random_Friend_No]['photos']); // Update array
									if ($Friends[$Random_Friend_No]['usablephotos'] == 0) { // If there are no photos left for that friend
										 unset($Friends[$Random_Friend_No]); // Remove friend from array
										$Friends = array_values($Friends); // Update array
									}
									
									$Photos_To_Add_To_Row[] = array('canvas' => $Canvas, 'resizedimage' => $Resized_Image, 'offset' => $Offset, 'temprowheight' => $Temp_Row_Height, 'border' => $Border_Size, 'i' => $Current_Row, 'resizedwidth' => $Resized_Width, 'resizedheight' => $Row_Height);

									if ($Temp_Width < 0) { // True if the images extend pass the canvas width
										$V = abs($Temp_Width) / ($Offset / 100);
										$Temp_Total_Width = 0;
										for ($a = 0; $a < count($Photos_To_Add_To_Row) - 1; $a++) { // Resize each photo to fit
											$PhotoObj = $Photos_To_Add_To_Row[$a];
											$NewWidth = $PhotoObj['resizedwidth'] - (($PhotoObj['resizedwidth'] / 100) * $V);
											$NewImage = imagecreatetruecolor($NewWidth, $PhotoObj['resizedheight']);
											imagecopyresampled($NewImage, $PhotoObj['resizedimage'], 0, 0, 0, 0, $NewWidth, $PhotoObj['resizedheight'], $PhotoObj['resizedwidth'], $PhotoObj['resizedheight']);
											$Photos_To_Add_To_Row[$a]['resizedimage'] = $NewImage;
											$Photos_To_Add_To_Row[$a]['offset'] = $Temp_Total_Width;
											$Photos_To_Add_To_Row[$a]['resizedwidth'] = $NewWidth;
											$Temp_Total_Width = $Temp_Total_Width + $NewWidth + $Border_Size;
											if ($a == count($Photos_To_Add_To_Row) - 2) {
												$Photos_To_Add_To_Row[($a + 1)]['offset'] = $Temp_Total_Width;
											}
										}
										
										for ($a = 0; $a < count($Photos_To_Add_To_Row); $a++) {
											$PhotoObj = $Photos_To_Add_To_Row[$a];
											imagecopy($PhotoObj['canvas'], $PhotoObj['resizedimage'], $PhotoObj['offset'], ($PhotoObj['temprowheight'] + ($PhotoObj['border'] * $PhotoObj['i'])), 0, 0, $PhotoObj['resizedwidth'], $PhotoObj['resizedheight']);
										}
										
										if ($Current_Row == $No_Of_Rows - 1) {
											$Height_Used = ($Row_Height * $No_Of_Rows) + $Total_Border_Size;
										}
									} else { //True if the images cut short before the end of the canvas width
										$V = abs($Temp_Width) / ($Offset / 100);
										$Temp_Total_Width = 0;
										for ($a = 0; $a < count($Photos_To_Add_To_Row); $a++) {
											$PhotoObj = $Photos_To_Add_To_Row[$a];
											$NewWidth = ceil((($PhotoObj['resizedwidth'] / 100) * $V)) + $PhotoObj['resizedwidth'];
											$NewImage = imagecreatetruecolor($NewWidth, $PhotoObj['resizedheight']);
											imagecopyresampled($NewImage, $PhotoObj['resizedimage'], 0, 0, 0, 0, $NewWidth, $PhotoObj['resizedheight'], $PhotoObj['resizedwidth'], $PhotoObj['resizedheight']);
											$Photos_To_Add_To_Row[$a]['resizedimage'] = $NewImage;
											$Photos_To_Add_To_Row[$a]['offset'] = $Temp_Total_Width;
											$Photos_To_Add_To_Row[$a]['resizedwidth'] = $NewWidth;
											$Temp_Total_Width = $Temp_Total_Width + $NewWidth + $Border_Size;
										}
										
										for ($a = 0; $a < count($Photos_To_Add_To_Row); $a++) {
											$PhotoObj = $Photos_To_Add_To_Row[$a];
											imagecopy($PhotoObj['canvas'], $PhotoObj['resizedimage'], $PhotoObj['offset'], ($PhotoObj['temprowheight'] + ($PhotoObj['border'] * $PhotoObj['i'])), 0, 0, $PhotoObj['resizedwidth'], $PhotoObj['resizedheight']);
										}
										
										if ($Current_Row == $No_Of_Rows - 1) {
											$Height_Used = ($Row_Height * $No_Of_Rows) + $Total_Border_Size;
										}
									}
								}
							
								$Row_Is_Filled = true;
							} else {
								unset($Friends[$Random_Friend_No]['photos'][$Random_Photo_No]); // Remove friend from array
								$Friends[$Random_Friend_No]['usablephotos'] = $Friends[$Random_Friend_No]['usablephotos'] - 1;
								$Friends[$Random_Friend_No]['photos'] = array_values($Friends[$Random_Friend_No]['photos']);
								if ($Friends[$Random_Friend_No]['usablephotos'] == 0) {
									unset($Friends[$Random_Friend_No]);
									$Friends = array_values($Friends);										
								}
								$Photos_To_Add_To_Row[] = array('canvas' => $Canvas, 'resizedimage' => $Resized_Image, 'offset' => $Offset, 'temprowheight' => $Temp_Row_Height, 'border' => $Border_Size, 'i' => $Current_Row, 'resizedwidth' => $Resized_Width, 'resizedheight' => $Resized_Height);
								$Offset = $Offset + $Resized_Width + $Border_Size;
								$Width_Left = $Width_Left - $Random_Photo['ResizedWidth'];
							}
						}
						$Temp_Row_Height = $Row_Height * ($Current_Row + 1);
					}

					imagecopyresampled($Fixed_Image, $Canvas, 0, 0, 0, 0, $Canvas_Width, $Canvas_Height, $Canvas_Width - $Border_Size, $Height_Used);
					imagecopyresampled($Final_Image, $Fixed_Image, $Border_Size, $Border_Size, 0, 0, $Canvas_Width - ($Border_Size * 2), $Canvas_Height - ($Border_Size * 2), $Canvas_Width - $Border_Size, $Height_Used);

					if ($Black_Or_White_Bool == "true") {
						grayscale($Final_Image);
					}

					$Img_ID = time(); // Unique image ID
					$Image_Directory = "imgs/" . $User_ID;
					$Image_File_Path = $Image_Directory . "/" . $Img_ID . '.png';
					$Created_Successfully = imageToFile($Final_Image, $Image_Directory, $Image_File_Path);
					
					// Create thumnail image
					$T = imagecreatefrompng($Image_File_Path);
					$Thumbnail_Image = imagecreatetruecolor(200, 200);
					imagecopyresized($Thumbnail_Image, $T, 0, 0, 0, 0, imagesx($Thumbnail_Image), imagesy($Thumbnail_Image), imagesx($T), imagesy($T));

					$Thumbnail_Directory = "imgs/" . $User_ID . "/Thumbnails";
					$Thumbnail_File_Path = $Thumbnail_Directory .  "/" . $Img_ID . '.png';			
					if ($Created_Successfully &&(imageToFile($Final_Image, $Image_Directory, $Image_File_Path)) && (imageToFile($Thumbnail_Image, $Thumbnail_Directory, $Thumbnail_File_Path))) { // True if collage was succesfully saved/created
						if ($Send_Email_Bool == 'true') { // True if the user requested an email to be sent containing the photo collage
							$Email_Content = CreateImageEmail($Image_File_Path);
							if (!mail($User_Email, 'My photo collage', $Email_Content['multipart'], $Email_Content['headers'])) {
									echo '<p class="Center">An error occurred when sending an email to '.$User_Email.'</p>' . $Redirect_HTML;
							}
						}			

						if (file_exists($Image_File_Path) && is_readable($Image_File_Path)) {
							$xmlDoc = new DOMDocument();
							$xmlDoc->load("SupportingFiles/CollageCounter.xml");
							$CounterElement = $xmlDoc->getElementsByTagName('CollageCounter')->item(0);
							$TotalNumberOfCollagesMade = $CounterElement->getAttribute('value');
							$CounterElement->setAttribute("value", ($TotalNumberOfCollagesMade + 1));
							$xmlDoc->save('SupportingFiles/CollageCounter.xml');

							echo "<a href='" . $Image_File_Path . "'><img width='440px' height='440px' src='" . $Image_File_Path . "'/></a>
							<form style='margin: 0 auto; width:480px;' action='Image.php?Url=" . $Image_File_Path . "' method='POST'>
							<input style='width:480px;' type=submit name='Type' value='Download'></form>";
						} else {
							echo '<p class="Center">Image could not be found/opened.</p>' . $Redirect_HTML;;
						}
					} else { //True if the directory or file could not be created
						echo '<p class="Center">Sorry an error occured. please try again.</p>' . $Redirect_HTML;
					}	
				} else { // Not enough photos to complete collage
					echo '<p class="Center">Not enough photos to complete collage.<br/> Your friends privacy settings maybe preventing access to their photos. Please select more friends.</p>' . $Redirect_HTML;
				}
			} else {
				echo '<p class="Center">No friends or users selected!</p> ' . $Redirect_HTML;
			}
		} else {
			echo '<p class="Center">No friends or users selected!</p>' . $Redirect_HTML;
		}
	} else { // User is logged out
		echo '<p class="Center">Sorry your session has expired. Please log in again.</p>' . $Redirect_HTML;
	} ?>
		</div>
	</div>
</div>

<?php include 'Footer.php'; ?>
</body>
</html>