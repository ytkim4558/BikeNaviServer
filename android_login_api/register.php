<?php 

require_once 'include/DB_Functions.php';
require_once '../vendor/autoload.php';
$db = new DB_Functions();
error_log("hihi");
// 구글 유저 확인 .참고 : https://developers.google.com/api-client-library/php/guide/aaa_idtoken
// 재참고 : https://github.com/google/google-api-php-client/blob/master/UPGRADING.md
function getUserEmailFromToken($idToken, $client) {
	$userData = $client->verifyIdToken($idToken);
	if ($userData) {
		//return $data['payload']['sub']; // user ID
		return $userData['email']; // user email
	}
	return false;
}

// json response array
$response = array("error" => FALSE);

// 자체 회원가입
if (isset($_POST['email']) && isset($_POST['password'])) {
	// receiving the post params
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	// check if user is already existed with the same email
	if ($db->isUserExisted($email)) {
		// user already existed
		$response["error"] = TRUE;
		$response["error_msg"] = "이미 " . $email . "로 가입한 회원이 있습니다. " ;
		echo json_encode($response);
	} else {
		// create a new user
		$user = $db->storeUser($email, $password);
		if ($user) {
			// user stored successfully
			$response["error"] = FALSE;
			$response["user"]["email"] = $user["USER_EMAIL"];
			$response["user"]["created_at"] = $user["CREATED_AT"];
			$response["user"]["updated_at"] = $user["UPDATED_AT"];
			echo json_encode($response);
		} else {
			// user failed to store
			$response["error"] = TRUE;
			$response["error_msg"] = "회원가입하는동안 알 수 없는 오류가 발생했습니다!";
			echo json_encode($response);
		}
	}
	
} else if (isset($_POST['google_authcode']) && isset($_POST['idToken'])) {	// 구글 로그인
	
	
	
	// 구글 로그인
	// 참고 : https://developers.google.com/api-client-library/php/auth/web-app
	// https://developers.google.com/identity/sign-in/android/offline-access
	// (Receive authCode via HTTPS POST)
		
	// Set path to the Web application client_secret_*.json file you downloaded from the
	// Google Developers Console: https://console.developers.google.com/apis/credentials?project=_
	// You can also find your Web application client ID and client secret from the
	// console and specify them directly when you create the GoogleAuthorizationCodeTokenRequest
	// object.
	
	/************************************************
	 * NOTICE:
	 * The redirect URI is to the current page, e.g:
	 * http://localhost:8080/idtoken.php
	 * 참고 : https://github.com/google/google-api-php-client/blob/master/examples/idtoken.php
	 ************************************************/
	//$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
 	//error_log("redirect_uri xx : " . $redirect_uri, 0);
	
	$client = new Google_Client();
	$client->setAuthConfig('client_secret.json');
	$client->setApplicationName("My Application");
// 	$client->setDeveloperKey("AIzaSyAlLEqHBvm2ZUyWZfl_lVQCH_msZ7a6SI8");
// 	$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
	//$client->setRedirectUri($redirect_uri);
	$client->setScopes('email');
	$client->setAccessType("offline");
	$idToken = $_POST['idToken'];
	error_log("test1 : ");
	
	// 참고 : https://developers.google.com/api-client-library/php/guide/aaa_idtoken
	// 구글서버에다 인증 받은 회원인지 확인
	if (getUserEmailFromToken($idToken, $client) != false) {
		// 인증 받은 경우라면
		error_log("test2 : ");
		// 	After the web server receives the authorization code, it can exchange the authorization code for an access token.
		// 	To exchange an authorization code for an access token, use the authenticate method:
		//$client->authenticate($_POST['google_authcode']);
		$token = $client->fetchAccessTokenWithAuthCode($_POST['google_authcode']);
  		//$client->setAccessToken($token);
// 		error_log("error_log : ");
// 		error_log(print_r($token, true));
// 		error_log("authCode : ".$_POST['google_authcode'], 0);
		$access_token = $client->getAccessToken();
		//error_log("accesstoken : ".json_encode($access_token), 0);
		$refresh_token = $client->getRefreshToken();
		//error_log("refresh_token : ".$refresh_token, 0);
		//error_log("refresh_token array : ".json_encode($refresh_token), 0);
		
		$email = getUserEmailFromToken($_POST['idToken'], $client);
		
		if ($db->isUserExistedWithGoogle($email)) {
			// user already existed 
			// 로그인 시도
			$user = $db->getGoogleUserByEmail($email);
			$response["error"] = FALSE;
			$response["user"]["googleemail"] = $user["GOOGLE_EMAIL"];
			$response["user"]["created_at"] = $user["CREATED_AT"];
			$response["user"]["updated_at"] = $user["UPDATED_AT"];
			echo json_encode($response);
		} else {
			// create a new user
			$user = false;
			// 나중에 활용가능? : 주석 처리 액세스 토큰과 리프레시 토큰을 활용할 때 구현하도록 한다. 현재로서는 유저 이메일만 가져오면 됨.
// 나중에 활용가능? 			if(isset($access_token) && isset($refresh_token) ) {
// 나중에 활용가능?				$user = $db->storeUserWithGoogleEmailnAccessTokennRefreshToken($email, json_encode($access_token), json_encode($refresh_token));
			$user = $db->storeUserWithGoogleEmail($email);
// 나중에 활용가능?			} else {
// 나중에 활용가능?				$user = false;
// 나중에 활용가능?				$response["error_msg"] = "accesstoken : " . json_encode($access_token) . ", refreshToken : " . json_encode($refresh_token);
// 나중에 활용가능?				$response["error_msg"] = "refreshToken : " . json_encode($refresh_token);
// 나중에 활용가능?			}
			if ($user) {
				// user stored successfully
				$response["error"] = FALSE;
				$response["user"]["googleemail"] = $user["GOOGLE_EMAIL"];
				$response["user"]["created_at"] = $user["CREATED_AT"];
				$response["user"]["updated_at"] = $user["UPDATED_AT"];
				echo json_encode($response);
			} else {
				// user failed to store
				$response["error"] = TRUE;
				$response["error_msg"] .= "회원가입하는동안 알 수 없는 오류가 발생했습니다!";
				echo json_encode($response);
			}
		}
	} else {
		// 구글 서버에서 인증 받지 못하면 에러 메시지 발송
		$response["error"] = TRUE;
		$response["error_msg"] = "인증되지 않은 어플입니다.";
		echo json_encode($response);
	}
} else if(isset($_POST['kakaoNickName']) && isset($_POST['kakaoID'])) {
	$kakaoID = $_POST['kakaoID'];
	$kakatotalknickname = $_POST['kakaoNickName'];
	error_log("kakaoID : " . $kakaoID);
	error_log("kakaonick : " . $kakatotalknickname);
	if($db->isUserExistedWithKakao($kakaoID)) {
		// 이미 유저가 있는 경우 
		// 로그인 시도
		$user = $db->getKakaoUserByID($kakaoID);
		$response["error"] = FALSE;
		$response["user"]["kakaonickname"] = $user["KAKAO_NICK_NAME"];
		$response["user"]["created_at"] = $user["CREATED_AT"];
		$response["user"]["updated_at"] = $user["UPDATED_AT"];
		echo json_encode($response);
	} else {
		$user = false;
		$user = $db->storeUserWithKakaoIDAndNickName($kakaoID, $kakatotalknickname);
		if ($user) {
			// user stored successfully
			$response["error"] = FALSE;
			$response["user"]["kakaonickname"] = $user["KAKAO_NICK_NAME"];
			$response["user"]["created_at"] = $user["CREATED_AT"];
			$response["user"]["updated_at"] = $user["UPDATED_AT"];
			echo json_encode($response);
		} else {
			// user failed to store
			$response["error"] = TRUE;
			$response["error_msg"] .= "회원가입하는동안 알 수 없는 오류가 발생했습니다!";
			echo json_encode($response);
		}
	}
} else if(isset($_POST['facebookEmail'])) {
	$email = $_POST['facebookEmail'];
	if($db->isUserExistedWithFacebook($email)) {
		// 이미 유저가 있는 경우 
		// 로그인 시도
		$user = $db->getFacebookUserByEmail($email);
		$response["error"] = FALSE;
		$response["user"]["facebookemail"] = $user["FACEBOOK_EMAIL"];
		$response["user"]["created_at"] = $user["CREATED_AT"];
		$response["user"]["updated_at"] = $user["UPDATED_AT"];
		echo json_encode($response);
	} else {
		$user = false;
		$user = $db->storeUserWithFacebookEmail($email);
		if ($user) {
			// user stored successfully
			$response["error"] = FALSE;
			$response["user"]["facebookemail"] = $user["FACEBOOK_EMAIL"];
			$response["user"]["created_at"] = $user["CREATED_AT"];
			$response["user"]["updated_at"] = $user["UPDATED_AT"];
			echo json_encode($response);
		} else {
			// user failed to store
			$response["error"] = TRUE;
			$response["error_msg"] .= "회원가입하는동안 알 수 없는 오류가 발생했습니다!";
			echo json_encode($response);
		}
	}
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "필수 항목인 이메일이나 비밀번호가 누락되었습니다!";
	echo json_encode($response);
}
?>