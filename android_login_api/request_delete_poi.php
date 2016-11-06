<?php 
// 장소를 추가하거나 갱신처리하는 페이지
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("reguest_add_or_update_poi_page 입니다~");

// json response array
$response = array("delete" => FALSE);

// 유저정보 타입 확인
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
	$response["delete"] = TRUE;
	$response["delete_msg"] = "필수 항목인 유저정보나 페이지가 누락되었습니다!";
	echo json_encode($response);
}

if(isset($user)) {
	$userNo = $user['USER_NO'];
	if (isset($_POST['POI_NAME'])  && isset($_POST['POI_ADDRESS']) && isset($_POST['POI_LAT_LNG'])) {
		// receiving the post params
		// int로 변환 http://stackoverflow.com/questions/5052932/how-to-get-int-instead-string-from-form
		$poiName = $_POST['POI_NAME'];
		$poiAddress = $_POST['POI_ADDRESS'];
		$poiLatLng = $_POST['POI_LAT_LNG'];
		$poi = false;
		// check if poi is already existed with the same poiLatLNg
		if($db->isPOIExisted($poiLatLng)) {
			$poi = $db->updateLastUsedAtPOI($poiLatLng);
			$poiNo = $poi["POI_NO"];
			// track already existed
			// poiNo userNo를 가진 user_poi table이 있는지 확인
			if ($db->isUSER_POIExisted($userNo, $poiNo)) {
				$user_poi = $db->deleteUSERPOIAtUserPOI($userNo, $poiNo);
				if($user_poi) {
					$response["delete"] = TRUE;
					$response["delete_msg"] = "유저 - 장소가 삭제되었습니다..";
					echo json_encode($response);
				} else {
					$response["delete"] = FALSE;
					$response["delete_msg"] = "유저 - 장소가 삭제되지 않았습니다.";
					echo json_encode($response);
				}
			} else {
				$response["delete"] = FALSE;
				$response["delete_msg"] = "유저 - 장소가  존재하지 않았습니다.";
				echo json_encode($response);
			}
		} else {
			$response["delete"] = FALSE;
			$response["delete_msg"] = "장소가 존재하지 않습니다.";
			echo json_encode($response);
		}
	} else {
		$response["delete"] = FALSE;
		$response["delete_msg"] = "필수정보인 장소 인수가 올바른지 확인해보세요!";
		echo json_encode($response);
	}
} else {
	$response["delete"] = FALSE;
	$response["delete_msg"] = "경로를 등록하거나 갱신하는데 필수정보가 없습니다.";
	echo json_encode($response);
}
?>