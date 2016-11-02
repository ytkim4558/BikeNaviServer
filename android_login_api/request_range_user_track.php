<!-- 먼저 로그인 정보를 이용하여 유저의 No를 확인한뒤 해당 No에 해당되는 경로 테이블을 가져온다. -->

<?php 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$reponse = array("error" => FALSE);
$page = $_POST['page'];

// 페이징하기 위한 리스트뷰 첫번째 행
$start = 0;

// 페이징하기 위한 리스트뷰 아이템 개수
$limit = 3;

// 최대 갈 수 있는 페이지
$page_limit = $total/$limit;

// 페이지 숫자에 따라 시작 번호 계산
$start = ($page - 1) * $limit;

if (isset($_POST['email'])) {
	// receiving the post params
	$email = $_POST['email'];
	
	// get the user by email
	$user = $db->getUserByEmail($email);
} else if (isset($_POST['googleemail'])) {
	// receiving the post params
	$googleemail = $_POST['googleemail'];
	
	// get the user by email
	$user = $db->getGoogleUserByEmail($googleemail);
} else if (isset($_POST['kakaoid'])) {
	// receiving the post params
	$kakaoid = $_POST['kakaoid'];
	
	// get the user by kakao id
	$user = $db->getKakaoUserByID($kakaoid);
} else if (isset($_POST['facebookid'])) {
	// receiving the post params
	$facebookid = $_POST['facebookid'];
	
	// get the user by facebook id
	$user = $db->getFacebookUserByID($facebookid);
} else {
	// required post params is missing
	$response["error"] = TRUE;
	$response["error_msg"] = "필수 항목인 유저정보나 페이지가 누락되었습니다!";
	echo json_encode($response);
}

if(isset($user)) {
	if ($user != false) {
		$userNo = $user["USER_NO"];
		
		// 즐겨찾기 리스트 요청할 때 
		if(isset($_POST['bookmark'])) {
			// 페이징에 맞춘 즐겨찾기 경로를 가져오기 (유저번호, 시작 아이템번호, 아이템 개수)
			$response["error"] = FALSE;
			$response['bookmark'] = $db->getRangeUserBookMarkOfTrackUsingUserNo($userNo, $start, $limit);
			echo json_encode($response);
		} else if(isset($_POST['recent'])) {
			// 최근 경로 리스트 요청할 때
			$response["error"] = FALSE;
			$response['recent'] = $db->getRangeUserRecentTrackOfTrackUsingUserNo($userNo, $start, $limit);
			echo json_encode($response);
		}
	}
}