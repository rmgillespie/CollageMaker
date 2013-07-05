<?php
require_once("SupportingFiles/facebook-php-sdk-master/src/facebook.php");

class Facebook_Login {

private $Facebook;
private $Logout_URL;
private $Login_URL;
private $User_profile;
private $User_Name;
private $User_ID;
private $Friends;

// Add methods that return album/photo data etc

function __construct() { // constructor
	// Create new Facebook session
	$Config = array();
	$Config['appId'] = getenv("FACEBOOK_APP_ID");
	$Config['secret'] = getenv("FACEBOOK_SECRET");
	$this->Facebook = new Facebook($Config);
	
	$Access_token = $this->Facebook->getAccessToken();
    $this->Facebook->setAccessToken($Access_token);
	
	$this->Logout_URL = $this->Facebook->getLogoutUrl(array('next' => getenv("LogoutPage")));
	$this->Login_URL = $this->Facebook->getLoginUrl(array(
		'scope' => 'user_photos, friends_photos, user_photo_video_tags, friends_photo_video_tags',
		'redirect_uri' => getenv("HomePage"),
		'next' => getenv("HomePage")
		));
		
}
    
public function __get($Name) { // Getter
    return $this->$Name;
}

function LogIn() { // Must be called before getter
    if($this->Facebook->getUser()) { // Attempt to get user info
		try {
			$this->User_profile = $this->Facebook->api('/me','GET');
			$this->Friends = $this->Facebook->api('/me/friends');
			$this->User_ID = $this->User_profile['id'];
			$this->User_Name  = $this->User_profile['name'];
			return true;
		} catch(FacebookApiException $e) {
			// echo 'Error type: ' . $e->getType() . '<br/> Error message: ' . $e->getMessage() . '<br/>';
			return false;
		}
    } else {
		return false;
	}
}

}
?>