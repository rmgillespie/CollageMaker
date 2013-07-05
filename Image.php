<?php require_once 'SupportingFiles/PHPFunctions.php'; require_once "Classes/Facebook_Login.class.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Collage Maker</title>
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
<<div class='ViewSourceContainer'>
	<a style='color:#FFF; padding:5px;' href='https://github.com/rmgillespie/CollageMaker'>View source</a>
</div>
<?php
include 'SupportingFiles/Header.php';?>
	
<div id="FacebookAppContainer">
	<div class='FacebookAppContainerPadding'>
		<div style="margin: 0 auto; width:100%; text-align:center; padding:0px;">
<?php
/**************** Facebook ***************/
$FB = new Facebook_Login(); // Create new FB login class
$Logged_In = $FB->LogIn(); // Check if user is logged in
$Facebook = $FB->Facebook;
$User_profile = $FB->User_profile;
$User_ID = $FB->User_ID;
$Friends = $FB->Friends;
/************ End of Facebook ************/

extract($_GET);

$Output = '';
$Redirect = false;
$Type = '';
if (isset($_POST['Type'])) {
	$Type = $_POST['Type'];
}

if ($Logged_In) { // User is logged in
	if ($Type == 'Delete') { // User wants to delete an image
		$ImagePath = $_POST['ImagePath'];
		$User_ID = $_POST['UserID'];
		if (file_exists($ImagePath) && is_readable($ImagePath)) { // Image exists
			if (endsWith(dirname($ImagePath), $User_profile['id'])) { // User has permision to delete file
				if (unlink($ImagePath)) { // Success.
					$ThumbnailPath = 'imgs/' . $User_ID . '/Thumbnails/'.basename($ImagePath);
					if (file_exists($ThumbnailPath) && is_readable($ThumbnailPath)) {
						unlink($ThumbnailPath);
					}
					deleteCollageDetails($ImagePath);
					$Output = '<p style="color:green;">Deleted successfully.</p>
								<meta http-equiv="refresh" content="0;url=' . getenv("HomePage") . '"/>';
				} else { // Deletion failed
					$Output = '<p>An error occured when attempting to delete the file. Please try again.</p>'; $Redirect = true;
				}
			} else { // Not users photo to delete
				$Output = '<p>Permission denied. You can only delete your own collages.</p>'; $Redirect = true;
			}
		} else { // Image does not exist or is unreadable
			$Output = '<p>File cannot be found.</p>'; $Redirect = true;
		}
	} else {
		$Output = '<p>Invalid request.</p>'; $Redirect = true;
	}
} else { // User is not logged in
	if ($Type == 'Delete') { // User wants to delete an image
		$Output = '<p style="color:#990000">You must be logged in to delete a collage.</p>'; $Redirect = true;
	}
}

if (($Type == 'Download') || ($Type == 'Download All')) { // User wants to download a collage
	if ($Type == 'Download') { // Only 1 collage
		if ((pathinfo($Url, PATHINFO_EXTENSION) == 'png') && file_exists($Url) && is_readable($Url) && !is_dir($Url))  {
		echo "<br>Tewst<br>";
			$ImageID = substr(basename($Url), 0, strlen(basename($Url)) - 4);
			$XMLFilePath = dirname($Url) . '/' . 'CollageDownloadCounter.xml';
			updateDownloadCounter($XMLFilePath, $ImageID);
			$File = @fopen($Url, "rb");
			@header("Cache-Url: no-cache, must-revalidate"); 
			@header("Pragma: no-cache");
			@header('Content-Disposition: attachment; filename=' . basename($Url));
			@header('Content-Type: application/octet-stream');
			@header('Content-Length: ' . filesize($Url));
			@header('Content-Transfer-Encoding: binary');
			
			ob_end_clean(); // Required for large files 
			
			if ($File) {
				@fpassthru($File); // stream the file and exit the script when complete
				exit;
			} else {
				$Output = '<p>Sorry, the collage you are requesting is unavailable.</p>'; $Redirect = true;
			}
		 } else {
			$Output = '<p>Sorry, the collage you are requesting does not exist.</p>'; $Redirect = true;
		 }
	} else { // All users collages
		$Directory = 'imgs/' . $ID . '/';
		$Distination = $Directory . '/' . 'CollageMaker.zip';
		if (is_dir($Directory)) { //True if user directory exists
			$Images = glob($Directory . "*.png");
			$NumberOfImages = count($Images);
			if ($NumberOfImages != 0) { //True if images were found
				$Zip_File = new ZipArchive();
				if ($Zip_File->open($Distination, true ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
					$Output = '<p>Error creating zip file of user images.</p>';
				} else { //True if zip was opened
					foreach ($Images as $Image) { //For each image in the users directory
						$Zip_File->addFile($Image, $Image);
					}
					$Zip_File->close();
					if (file_exists($Distination)) { //True if zip file exists
						if (file_exists($Distination) && is_readable($Distination)) {
							$File = @fopen($Distination, "rb");
							@header("Cache-Url: no-cache, must-revalidate"); 
							@header("Pragma: no-cache");
							@header('Content-Disposition: attachment; filename=' . basename($Distination));
							@header('Content-Type: application/octet-stream');
							@header('Content-Length: ' . filesize($Distination));
							@header('Content-Transfer-Encoding: binary');
							
							ob_end_clean(); // Required for large files 
							
							if ($File) {
								@fpassthru($File); // stream the file and exit the script when complete
								exit;
							} else {
								$Output = '<p>Sorry, the file you are requesting is unavailable.</p>';;
							}
						} else {
							$Output = '<p>Sorry, the file you are requesting is unavailable.</p>';;
						}
					} else { //True if zip files does not exist
						$Output = 'Error when creating zip file';
					}
				}
			} else { //True if no images found
				$Output = 'No images found.'; $Redirect = true;
			}
		} else {
			$Output = 'User directory does not exist.'; $Redirect = true;
		}
	}
}

if ($Redirect) {
	$Output .= '<script type="text/javascript">setTimeout("Redirect(\'' . getenv("HomePage") . '\')",4000);</script> Redirecting in <span id="Timer">4</span> seconds.'; ?>
	<script type="text/javascript">
		IntervalID = setInterval("Change_Timer_Value()", 1000);
	</script> <?php
}
echo $Output;


function deleteCollageDetails($Delete) {
	$ImageID = substr(basename($Delete), 0, strlen(basename($Delete)) - 4);
	$Directory = dirname($Delete) . '/';
	$ImageExists = false;
	$XMLFileName = 'CollageDownloadCounter.xml'; // Counter views and download coutner for users collages
	$XMLFilePath = $Directory . $XMLFileName;
	if (file_exists($XMLFilePath) && is_readable($XMLFilePath)) { //Download counter file exists
		$xmlDoc = new DOMDocument();
		$xmlDoc->formatOutput = true;
		$xmlDoc->preserveWhiteSpace = false;
		$xmlDoc->load($XMLFilePath);
		$CollageNodes = $xmlDoc->getElementsByTagName('Collage');
		$NumberOfCollagesFound = 0;
		
		foreach ($CollageNodes as $Collage) {
		$NumberOfCollagesFound = $NumberOfCollagesFound + 1;
			$CollageID = $Collage->getAttribute('id');
			if ($CollageID == $ImageID) {
				$ImageExists = true;
				$ImageNum = $NumberOfCollagesFound - 1;
				break;
			}
		}

		if ($ImageExists) { //True if an element for the image already exists in the xml file
			$doc = $xmlDoc->documentElement;
			$CollageElement = $xmlDoc->getElementsByTagName('Collage')->item($ImageNum);
			$OldElement = $doc->removeChild($CollageElement);
			$xmlDoc->save($XMLFilePath);
		}
	}
}

function updateDownloadCounter($XMLFilePath, $ImageID) {
	if (file_exists($XMLFilePath) && is_readable($XMLFilePath)) { //Download counter file already exists
		$xmlDoc = new DOMDocument();
		$xmlDoc->formatOutput = true;
		$xmlDoc->preserveWhiteSpace = false;
		$xmlDoc->load($XMLFilePath);
		$CollageNodes = $xmlDoc->getElementsByTagName('Collage');
		$NumberOfCollagesFound = 0;
		
		$ImageExists = false;
		foreach ($CollageNodes as $Collage) {
			$NumberOfCollagesFound = $NumberOfCollagesFound + 1;
			$CollageID = $Collage->getAttribute('id');
			if ($CollageID == $ImageID) {
				$ImageExists = true;
				$ImageNum = $NumberOfCollagesFound - 1;
				break;
			}
		}

		if ($ImageExists) { //True if an element for the image already exists in the xml file
			$CurrentElement = $xmlDoc->getElementsByTagName('Collage')->item($ImageNum);
			$CurrentValue = $CurrentElement->getAttribute('DownloadCounter');
			$NewValue = $CurrentValue + 1;
			$CurrentElement->setAttribute("DownloadCounter", $NewValue);
			$xmlDoc->save($XMLFilePath);
		} else { //True if an element does not exist for that image. Create element for that image
			$x = $xmlDoc->documentElement;
			$newNode = $xmlDoc->createElement("Collage");
			$IDAttribute = $xmlDoc->CreateAttribute("id");
			$newNode->setAttributeNode($IDAttribute);
			$newNode->setAttribute("id", $ImageID);

			$NewAttribute = $xmlDoc->CreateAttribute("DownloadCounter");
			$newNode->setAttributeNode($NewAttribute);
			$newNode->setAttribute("DownloadCounter", 1);

			$NewAttribute = $xmlDoc->CreateAttribute("Views");
			$newNode->setAttributeNode($NewAttribute);
			$newNode->setAttribute("Views", 0);

			$x->appendChild($newNode);
			$xmlDoc->save($XMLFilePath);
		}
	} else { //True if xml file does not exist
		$XMLOutput = '<?xml version="1.0"?>
					<root>
						<Collage id="' . $ImageID . '" DownloadCounter="1" Views="0"/>
					</root>';
		if ($handle = fopen($XMLFilePath, 'a')) {
			if (fwrite($handle, $XMLOutput) === FALSE) {
			} else {
				fclose($handle);
			}
		} 
	}
} ?>
		</div>
	</div>
</div>

<?php include 'SupportingFiles/Footer.php'; ?>
</body>
</html>


