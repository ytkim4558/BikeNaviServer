<?php
//먼저 로그인 정보를 이용하여 유저의 No를 확인한뒤 해당 No에 해당되는 경로 테이블을 가져온다.
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

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

        // counting the total item available in the database
        $total = $db->getCountOfUserTrackList($userNo);
        error_log("total : ".$total);

        $page = $_POST['page'];

        // 페이징하기 위한 리스트뷰 첫번째 행
        $start = 0;

        // 페이징하기 위한 리스트뷰 아이템 개수
        $limit = 7;

        // 최대 갈 수 있는 페이지
        $page_limit = ceil($total/$limit);

        // 만약 페이지 숫자가 우리가 보지못하는 것보다 더 작은 경우
        if($page <= $page_limit) {
            // 페이지 숫자에 따라 시작 번호 계산
            $start = ($page - 1) * $limit;

            // 즐겨찾기 리스트 요청할 때
            if(isset($_POST['bookmark'])) {
                // 페이징에 맞춘 즐겨찾기 경로를 가져오기 (유저번호, 시작 아이템번호, 아이템 개수)
                if($response['track'] = $db->getRangeUserBookMarkOfTrackUsingUserNo($userNo, $start, $limit)) {
                    $response["error"] = FALSE;
                } else {
                    $response["error"] = TRUE;
                }
                echo json_encode($response);
            } else if(isset($_POST['recent'])) {
                // 최근 경로 리스트 요청할 때

                if($response['track'] = $db->getRangeUserRecentTrackOfTrackUsingUserNo($userNo, $start, $limit)) {
                    $response["error"] = FALSE;
                } else {
                    $response["error"] = TRUE;
                }
                echo json_encode($response);
            }
        } else {
            $response["error"] = TRUE;
            $response["error_msg"] = "개수가 초과되었습니다!";
        }
	}
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "유저정보가 없습니다.!";
    echo json_encode($response);
}