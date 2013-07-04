<?php
# Assembles the appropriate error message depending on the error code parameter
# If error message data does not exist or the code cannot be found a default error message is returned
function Error($Error_Code) {
    if (isset($GLOBALS['Error_Message_Data'])) { //True if error message data exists
		# Attempt to find the rquested error code
		$Errors = $GLOBALS['Error_Message_Data']->xpath('error[@code="' . $Error_Code . '"]'); 

		if (count($Errors) == 1) { //True if the error code exists in the config file
			$Error_Message = $Errors[0]['message']; //Gets the message for the requested error code
		} else {
			#Default error code and message if the requested error could not be found
			$Error_Code = "0000";
			$Error_Message = 'An unknown error occurred';
		} //End of if else

    } else { //True if error message data does not exist
        $Error_Code = "0000";
        $Error_Message = 'An unknown error occurred';
    } //End of if else statement

    # Prints out the error message
    Print_Out_Error_Message($Error_Code, $Error_Message);
} //End of Error_Message function


# Prints out the error message
function Print_Out_Error_Message($Error_Code, $Error_Message) {
    $Dom = new DOMDocument;
    $Dom->preserveWhiteSpace = false;
	$Dom->formatOutput = true;
    $Dom->loadXML("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
     <conv>
       <error code='" . $Error_Code . "'>" . $Error_Message . "</error>
     </conv>");
    echo $Dom->saveXml();
	exit;
} //End of Print_Out_Error_Message function
?>