<?php 
// 장소를 추가하거나 갱신처리하는 페이지
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("reguest_add_or_update_poi_page 입니다~");

// json response array
$response = array("error" => FALSE);

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
	$response["error"] = TRUE;
	$response["error_msg"] = "필수 항목인 유저정보나 페이지가 누락되었습니다!";
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
				$user_poi = $db->updateLastUsedAtUserPOI($userNo, $poiNo);
				if($user_poi) {
					$response = save_poi($response, $poi);
					echo json_encode($response);
				} else {
					$response["error"] = TRUE;
					$response["error_msg"] = "유저 - 장소가 갱신되지 않았습니다.";
					echo json_encode($response);
				}
			} else {
				$user_poi = $db->storeUSERPOI($userNo, $poiNo);
				if($user_poi) {
					$response = save_poi($response, $poi);
					echo json_encode($response);
				} else {
					$response["error"] = TRUE;
					$response["error_msg"] = "유저 - 장소가 저장되지 않았습니다.";
					echo json_encode($response);
				}
			}
		} else {
			// create a new POI
			$poi = $db->storePOI($poiLatLng, $poiName, $poiAddress);
			$poiNo = $poi["POI_NO"];
			if ($poi) {
				// poi stored successfully
				$user_poi = $db->storeUSERPOI($userNo, $poiNo);
				if($user_poi) {
					$response = save_poi($response, $poi);
					echo json_encode($response);
				} else {
					$response["error"] = TRUE;
					$response["error_msg"] = "유저 - 장소가 저장되지 않았습니다.";
					echo json_encode($response);
				}
			} else {
				// user failed to store
				$response["error"] = TRUE;
				$response["error_msg"] = "장소가 저장되지 않았습니다.";
				echo json_encode($response);
			}
		}
	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "경로 정보가 없습니다!";
		echo json_encode($response);
	}
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "경로를 등록하거나 갱신하는데 필수정보가 없습니다.";
	echo json_encode($response);
}

/**
 * 
 * @param POI $poi	poi 정보
 * @return array|boolean	반환할 response
 */
function save_poi($response, $poi) {
	if($poi) {
		$response["error"] = FALSE;
		$response["poi"]["poiName"] = $poi["POI_NAME"];
		$response["poi"]["poiAddress"] = $poi["POI_ADDRESS"];
		$response["poi"]["poiLatLng"] = $poi["POI_LAT_LNG"];
		$response["poi"]["created_at"] = $poi["CREATED_AT"];
		$response["poi"]["updated_at"] = $poi["UPDATED_AT"];
		$response["poi"]["last_used_at"] = $poi["LAST_USED_AT"];
		return $response;
	} else {
		return false;
	}
}