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
include 'SupportingFiles/Header.php'; ?>
	
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

extract($_GET); // Extracts URL into Variables
$Output = '';
$Redirect = false;
if (isset($View)) { // User wants to view an image
	if ((pathinfo($View, PATHINFO_EXTENSION) == 'png') && file_exists($View) && is_readable($View) && !is_dir($View)) {
		$ImageID = substr(basename($View), 0, strlen(basename($View)) - 4);
		$Directory = dirname($View) . '/';
		$ImageExists = false;
		$XMLFileName = 'CollageDownloadCounter.xml';
		$XMLFilePath = $Directory . $XMLFileName;
		$Views_HTML = '';
		if (file_exists($XMLFilePath) && is_readable($XMLFilePath)) { // Download counter file exists
			$xmlDoc = new DOMDocument();
			$xmlDoc->formatOutput = true; $xmlDoc->preserveWhiteSpace = false;
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
				$CurrentElement = $xmlDoc->getElementsByTagName('Collage')->item($ImageNum);
				$CurrentValue = $CurrentElement->getAttribute('Views');
				$NewValue = $CurrentValue + 1;
				$CurrentElement->setAttribute("Views", $NewValue);
				$xmlDoc->save($XMLFilePath);
				$Output .= '<p style="float:left; font-size:10px;"><b>Views: </b>' . $NewValue . '</p>';
			} else { // New Collage 
				$x = $xmlDoc->documentElement;
				$newNode = $xmlDoc->createElement("Collage");
				$IDAttribute = $xmlDoc->CreateAttribute("id");
				$newNode->setAttributeNode($IDAttribute);
				$newNode->setAttribute("id", $ImageID);

				$NewAttribute = $xmlDoc->CreateAttribute("Views");
				$newNode->setAttributeNode($NewAttribute);
				$newNode->setAttribute("Views", 1);

				$NewAttribute = $xmlDoc->CreateAttribute("DownloadCounter");
				$newNode->setAttributeNode($NewAttribute);
				$newNode->setAttribute("DownloadCounter", 0);

				$x->appendChild($newNode);
				$xmlDoc->save($XMLFilePath);
				$Output .= '<p style="float:left; font-size:10px;"><b>Views: </b>1</p>';
			}
		} else { // True if xml file does not exist
			$XMLOutput = '<?xml version="1.0"?>
						<root>
							<Collage id="' . $ImageID . '" DownloadCounter="0" Views="1"/>
						</root>';
			if ($handle = fopen($XMLFilePath, 'a')) {
				if (fwrite($handle, $XMLOutput) === TRUE) {
					fclose($handle);
					$Views_HTML .= '<p style="float:left; font-size:10px;"><b>Views: </b> 1</p>';
				}
			}
		}

		$Output .= "<div style='width:100%; height:20px; '>
						<p style='float:right; font-size:10px;'><b>Date created: </b>" . date("F d Y H:i:s", filemtime($View)) . "</p>" . $Views_HTML . "
					</div>
				<a href='" . $View . "'><img width='440px' height='440px' src='" . $View . "'/></a>
				<form style='margin: 0 auto; width:480px;' action='Image.php?Url=' . $View . '' method='POST'>
					<input style='width:480px;' type=submit name='Type' value='Download'>
				</form>";
	} else { // True if the file does not exist or is unreadable redirect here
		$Output = 'File unreadable or does not exist.'; $Redirect = true;
	}
}


if ($Redirect) {
	$Output .= '<script type="text/javascript">setTimeout("Redirect(\'' . getenv("HomePage") . '\')",4000);</script> Redirecting in <span id="Timer">4</span> seconds.';
	?>
	<script type="text/javascript">
		IntervalID = setInterval("Change_Timer_Value()", 1000);
	</script>
	<?php
}
	
echo $Output; ?>
		</div>
	</div>
</div>

<?php include 'SupportingFiles/Footer.php'; ?>
</body>
</html>


