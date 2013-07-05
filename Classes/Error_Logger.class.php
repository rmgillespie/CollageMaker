<?php
class Error_Logger {

    # Default paths used to log error uri's if undefined in the constructor
    const FATAL_ERROR_DIR_CONST = './Currency_Converter_Fatal_Errors.log';
    const WARNING_ERROR_DIR_CONST = './DCurrency_Converter_Warning_Errors.log';
    const NOTICE_ERROR_DIR_CONST = './Currency_Converter_Notice_Errors.log';
    const OTHER_ERROR_DIR_CONST = './Currency_Converter_Other_Errors.log';

    private $Fatal_Error_Dir;
    private $Warning_Error_Dir;
    private $Notice_Error_Dir;
    private $Other_Error_Dir;

    function __construct($Fatal_Path = self::FATAL_ERROR_DIR_CONST, $Warning_Path = self::WARNING_ERROR_DIR_CONST, $Notice_Path = self::NOTICE_ERROR_DIR_CONST, $Other_Path = self::OTHER_ERROR_DIR_CONST) {
		$this->Fatal_Error_Dir = $Fatal_Path;
		$this->Warning_Error_Dir = $Warning_Path;
		$this->Notice_Error_Dir = $Notice_Path;
		$this->Other_Error_Dir =$Other_Path;
    } //End of constructor


    public function Error_Handler($Error_Num, $Error_String, $Error_File, $Error_Line) {
		$Log_Message = "/" . str_repeat('*', 54) . "\\\r\n";
		$Log_Message .= " Date:		" . date("F d Y H:i:s") . "\r\n";
		$Log_Message .= " Location:	" . $Error_File . " (" . $Error_Line . ")" . "\r\n";
		$Log_Message .= " Error String:	" . wordwrap($Error_String, 40, "\r\n\t\t", 1). "\r\n";
		$Log_Message .= "/" . str_repeat('*', 54) . "\\\r\n\r\n";

		switch ($Error_Num) {
			case E_USER_ERROR:
				error_log($Log_Message, 1, $_SERVER['SERVER_ADMIN'], "Subject: " . $_SERVER["DOCUMENT_ROOT"] . "\nFrom: " . $_SERVER['SERVER_NAME'] . "\n"); //Sends an email to admin
				error_log($Log_Message, 3, $this->Fatal_Error_Dir); //Log the error in the fatal error log file
				Error(3100); //Prints out an error message and exit
			case E_USER_WARNING:
				error_log($Log_Message, 3, $this->Warning_Error_Dir); //Log the error in the warning error log file
				break;
			case E_USER_NOTICE:
				error_log($Log_Message, 3, $this->Notice_Error_Dir); //Log the error in the notice error log file
				break;
			default:
				error_log($Log_Message, 3, $this->Other_Error_Dir); //Log the error in the other error log file
		} //End of switch
    } //End of Error function

} //End of class file
?>