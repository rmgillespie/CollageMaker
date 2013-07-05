<?php require_once 'PHPFunctions.php'; require_once("facebook-php-sdk-master/src/facebook.php");

// Destroy the Facebook session and delete the cookie
$Config = array();
$Config['appId'] = getenv("FACEBOOK_APP_ID");
$Config['secret'] = getenv("FACEBOOK_SECRET");
$Facebook = new Facebook($Config);

$Facebook -> destroySession();
setcookie(getenv("FACEBOOK_APP_ID"),'',time()-10);

// Redirect to home page
echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=' . getenv("HomePage") . '">';
?>