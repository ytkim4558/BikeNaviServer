<?php 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$reponse = array("error" => FALSE);

if (isset($_POST['email']) && isset($_POST['password'])) {
	// receiving the post params
	$facebookName = $_POST['email'];
	$password = $_POST['password'];
	
	// get the user by email and password
	$user = $db->getUserByEmailAndPassword($facebookName, $password);
	
	if ($user != false) {
		// user is found
		$response["error"] = FALSE;
		$response["user"]["email"] = $user["USER_EMAIL"];
		$response["user"]["created_at"] = $user["CREATED_AT"];
        $response["user"]["updated_at"] = $user["UPDATED_AT"];
        $response["user"]["last_used_at"] = $user["LAST_USED_AT"];
        echo json_encode($response);
	} else {
		// user is not found with the credentials (자격)
		$response["error"] = TRUE;
		$response["error_msg"] = "이메일이나 비밀번호가 잘못되었습니다. 다시 입력하세요.";
		echo json_encode($response);
	}
} else {
	// required post params is missing
	$response["error"] = TRUE;
	$response["error_msg"] = "필수 항목인 이메일이나 비밀번호가 누락되었습니다!";
	echo json_encode($response);
}
?>