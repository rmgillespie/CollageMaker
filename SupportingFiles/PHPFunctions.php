<?php
putenv("FACEBOOK_APP_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
putenv("FACEBOOK_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
putenv("HomePage=http://www.ryangillespie.co.uk/CollageMaker/index.php");
putenv("LogoutPage=http://www.ryangillespie.co.uk/CollageMaker/SupportingFiles/Logout.php");
putenv("CreateCollagePage=http://www.ryangillespie.co.uk/CollageMaker/CreateCollage.php");

function TotalNumberOfCollages() {
	$xmlDoc = new DOMDocument();
	$xmlDoc->load("SupportingFiles/CollageCounter.xml");
	return $xmlDoc->getElementsByTagName('CollageCounter')->item(0)->getAttribute('value');
}


function getDirectoryList($directory) {
	$results = array(); // create an array to hold directory list
	$handler = opendir($directory); // create a handler for the directory
	while ($file = readdir($handler)) { // open directory and walk through the filenames
		if ($file != "." && $file != "..") { // if file isn't this directory or its parent, add it to the results
			$Uri = $directory . "/".$file;
			$File_Size = filesize($Uri);
			if ($File_Size > 2600) {
				$results[] = $file;
			} 
		}
	}
	closedir($handler);
	return $results;
}
  
  
/* Generic PHP functions */
function endsWith($haystack, $needle) {
    $length = strlen($needle);
    $start = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function sortByOrder($a, $b) {
	return $a['downloadcount'] < $b['downloadcount'];
}

function GetRandomInteger($MinValue, $MaxValue) {
    return rand($MinValue, $MaxValue);
}
/* End of generic PHP functions */


/* Image processing PHP functions */
function imageToFile($Image, $Directory, $Full_Path) {
	if (is_dir($Directory)) { // Directory exists
		if (imagepng($Image, $Full_Path)) { // Attempts to save image to filepath
			return true;
		}
	} else { // Directory doesn't exist
		if(mkdir($Directory, 0777)){ // Atempt to create directory
			if (imagepng($Image, $Full_Path)) { // Attempts to save image to filepath
				return true;
			}
		}
	}
	return false;
}

function grayscale($Image, $V = 0) {
    for ($x = 0; $x < imagesx($Image); $x++) {
        for ($y = 0; $y < imagesy($Image); $y++) { // For every pixel
            $RGB = imageColorsForIndex($Image, ImageColorAt($Image, $x, $y)); // Pixel color

            $Gray = ($RGB["red"] + $RGB["green"] + $RGB["blue"]) / 3; // Grey
            if ($V != 0) {
                $Sum = $RGB["red"] + $RGB["green"] + $RGB["blue"];
                if ($Sum == 0) {
                    $RelR = $RelG = $RelB = 0;
                } else {
                    $RelR = $RGB["red"] / $Sum;
                    $RelG = $RGB["green"] / $Sum;
                    $RelB = $RGB["blue"] / $Sum;
                }
                $Gray = ($RGB["red"] * $RelR + $RGB["green"] * $RelG +
                        $RGB["blue"] * $RelB) * $V + $Gray * (1 - $V);
            }
            imagesetpixel($Image, $x, $y, imagecolorallocate($Image, $Gray, $Gray, $Gray));
        }
	}
    return $Image;
}
/* End of image processing PHP functions */


function MostDownloaded($UserID, $Friends) {
	$CollageDownloadsArray = array();
	$Output = '';
	$Directory = 'imgs/' . $UserID . '/';
	$XMLFileName = 'CollageDownloadCounter.xml';
	$XMLFilePath = $Directory . $XMLFileName;
	if (file_exists($XMLFilePath) && is_readable($XMLFilePath)) { //True if the download counter file already exists
		$xmlDoc = new DOMDocument();
		$xmlDoc->formatOutput = true;
		$xmlDoc->preserveWhiteSpace = false;
		$xmlDoc->load($XMLFilePath);
		$CollageNodes = $xmlDoc->getElementsByTagName('Collage');
		foreach ($CollageNodes as $Collage) {
			$CollageID = $Collage->getAttribute('id');
			$CollageDownloadCount = $Collage->getAttribute('DownloadCounter');
			$CollageDownloadsArray[] = array('profileid' => $UserID, 'imageid' => $CollageID, 'downloadcount' => $CollageDownloadCount);
		}
	}
	
	for ($i = 0; $i < count($Friends['data']); $i++) {
		$ID = $Friends['data'][$i]['id'];
		$Directory = 'imgs/' . $ID . '/';
		$XMLFilePath = $Directory . $XMLFileName;
		if (file_exists($XMLFilePath) && is_readable($XMLFilePath)) { //True if the download counter file already exists
			$xmlDoc = new DOMDocument();
			$xmlDoc->formatOutput = true;
			$xmlDoc->preserveWhiteSpace = false;
			$xmlDoc->load($XMLFilePath);
			$CollageNodes = $xmlDoc->getElementsByTagName('Collage');
			
			foreach ($CollageNodes as $Collage) {
				$CollageID = $Collage->getAttribute('id');
				$CollageDownloadCount = $Collage->getAttribute('DownloadCounter');
				$CollageDownloadsArray[] = array('profileid' => $ID, 'imageid' => $CollageID, 'downloadcount' => $CollageDownloadCount);
			}
		}
	}

	if ((count($CollageDownloadsArray)) != 0) {
		usort($CollageDownloadsArray, 'sortByOrder');
		$Output .= "<div style='width:100%; height:100px; overflow:hidden; white-space:nowrap;'>";
		if ((count($CollageDownloadsArray)-1) < 5) {
			for ($i=0; $i<count($CollageDownloadsArray);$i++) {		
				$ThumbnailSource = 'imgs/' . $CollageDownloadsArray[$i]['profileid'] . '/Thumbnails/' . $CollageDownloadsArray[$i]['imageid'] . '.png';
				$ImgSrc = 'imgs/' . $CollageDownloadsArray[$i]['profileid'] . '/' . $CollageDownloadsArray[$i]['imageid'] . '.png';
				$Output .= '
				<div style="width:100px; height:100px; display:inline-block;">
					<a title="View collage" alt="Collage photo - ' . $ImgSrc . '" href="View.php?View=' . $ImgSrc . '">
						<img style="padding:4px 2px 4px 2px;" width="96px" height="92px" src="' . $ThumbnailSource . '"/>
					</a>
				</div>';
			}
		} else {
			for ($i=1;$i<=4;$i++) {
				$ThumbnailSource = 'imgs/' . $CollageDownloadsArray[$i]['profileid'] . '/Thumbnails/' . $CollageDownloadsArray[$i]['imageid'] . '.png';
				$ImgSrc = 'imgs/' . $CollageDownloadsArray[$i]['profileid'] . '/' . $CollageDownloadsArray[$i]['imageid'] . '.png';
				$Output .= '
				<div style="width:100px; height:100px; display:inline-block;">
					<a title="View collage" alt="Collage photo - ' . $ImgSrc . '" href="View.php?View=' . $ImgSrc . '">
						<img tyle="padding:4px 2px 4px 2px;" width="96px" height="92px" src="' . $ThumbnailSource . '"/>
					</a>
				</div>';
			}
		}
		$Output .= "</div>";
   }

   if ($UserID == null) {
		$Output .= "<p style='line-height:104px; text-align:center; font-size:10px;'>Please login to enable this feature.</p>";
	} else if ((count($CollageDownloadsArray))==0) {
		$Output .= "<p style='line-height:104px; text-align:center; font-size:10px;'>Noone has downloaded any collages.</p>";
	}
	return $Output;
}


function CreateImageEmail($UserName, $ImgPath) {
    $Boundary = "--" . md5(uniqid(time())); // Unique boundry string - random hash
    $Headers = "MIME-Version: 1.0\r\n";
	$Headers .= "From: admin@ryangillespie.co.uk\r\n";
    $Headers .= "Content-Type: multipart/mixed; boundary=\"" . $Boundary . "\"\n";
    
    $Multipart = "--$Boundary\n";
    $Multipart .= "Content-Type: text/html; charset=utf-8\n";
    $Multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
    $Multipart .= "Thankyou $UserName, <br><br>
	Please donate <a href='https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ryanmichaelgillespie%40hotmail%2ecom&lc=GB&item_name=Ryan%20Gillespie&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted'>here</a>.<br><br>
	Kind Regards,<br>Ryan Gillespie"; // Message body/html goes here
    $Multipart .= "\n\n";

    $FileAttatchment = "../CollageMaker/" . $ImgPath; // Relative file path to image
    $Message_Part = "Content-Type: image/png; file_name=\"" . basename($ImgPath) . "\"\n";
    $Message_Part .= "Content-ID: <" . md5("<img src=\"" . $ImgPath . "\">") . ">\n";
    $Message_Part .= "Content-Transfer-Encoding: base64\n";
    $Message_Part .= "Content-Disposition: inline; filename =\"" . basename($ImgPath) . "\"\n\n";
    $Message_Part .= chunk_split(base64_encode(file_get_contents($FileAttatchment))) . "\n";
    $Multipart .= "--" . $Boundary . "\n" . $Message_Part . "\n";
    $Multipart .= "--" . $Boundary . "--\n";
	
    return array('multipart' => $Multipart, 'headers' => $Headers);
}

?>
