<?php 

require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("register_user_poi_page 입니다~");

// json response array
$response = array("error" => FALSE);

// 자체 회원가입
if (isset($_POST['POI_NAME'])  && isset($_POST['POI_ADDRESS']) && isset($_POST['POI_LAT_LNG']) && isset($_POST['CREATED_AT']) && isset($_POST[''])) {
	// receiving the post params
	$pioName = $_POST['POI_NAME'];
	$poiLatLng = $_POST['POI_LAT_LNG'];
	
	// check if poi is already existed with the same poiLatLng
	if ($db->isPOIExisted($poiLatLng)) {
		// poi already existed, 그러니 사용한 시각을 업데이트 하고 받아오자
		$poi = $db->updatePOIWithPOI_LAT_LNG($poiLatLng);
		$response["error"] = FALSE;
		$response["poi"]["id"] = $poi["POI_NO"];
		$response["poi"]["name"] = $poi["POI_NAME"];
		$response["poi"]["poi_address"] = $poi["POI_ADDRESS"];
		$response["poi"]["poi_lat_lng"] = $poi["POI_LAT_LNG"];
		$response["poi"]["created_at"] = $poi["CREATED_AT"];
		$response["poi"]["updated_at"] = $poi["UPDATED_AT"];
		$response["poi"]["last_used_at"] = $poi["LAST_USED_AT"];
		echo json_encode($response);
	} else {
		// create a new POI
		$poi = $db->storePOI($facebookName, $password);
		if ($poi) {
			// poi stored successfully
			$response["error"] = FALSE;
			$response["poi"]["id"] = $poi["POI_NO"];
			$response["poi"]["name"] = $poi["POI_NAME"];
			$response["poi"]["poi_address"] = $poi["CREATED_AT"];
			$response["poi"]["poi_lat_lng"] = $poi["UPDATED_AT"];
			$response["poi"]["created_at"] = $poi["CREATED_AT"];
			$response["poi"]["updated_at"] = $poi["UPDATED_AT"];
			$response["poi"]["last_used_at"] = $poi["LAST_USED_AT"];
			echo json_encode($response);
		} else {
			// user failed to store
			$response["error"] = TRUE;
			$response["error_msg"] = "장소를 등록하는 동안 알 수 없는 오류가 발생했습니다!";
			echo json_encode($response);
		}
	}
	
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "필수 항목인 장소 이름, 주소 또는 좌표가 누락되었습니다!";
	echo json_encode($response);
}
?>