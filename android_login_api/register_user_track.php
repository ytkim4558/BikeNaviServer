<?php 

require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("register_user_track_page 입니다~");

// json response array
$response = array("error" => FALSE);

// 자체 회원가입
if (isset($_POST['START_POI_NO'])  && isset($_POST['DEST_POI_NO']) && 
		isset($_POST['CREATED_AT']) 
		&& isset($_POST['UPDATED_AT']) && isset($_POST['LAST_USED_AT'])) {
	// receiving the post params
	$startPOINo = $_POST['START_POI_NO'];
	$destPOINo = $_POST['DEST_POI_NO'];
	$stopPOINoArray = $_POST['STOP_POI_NO_ARRAY'];
	$createdAt = $_POST['CREATED_AT'];
	$updatedAt = $_POST['UPDATED_AT'];
	$lastUsedAt = $_POST['LAST_USED_AT'];
	
	// check if track is already existed with the same start_poi_no, dest_poi_no, stop_poi_no(optional)
	if ($db->isTrackExisted($startPOINo, $destPOINo, $stopPOINoArray, $createdAt, $updatedAt, $lastUsedAt)) {
		// track already existed, 그러니 사용한 시각을 업데이트 하고 받아오자
		$track = $db->updateTrack($startPOINo, $destPOINo, $stopPOINoArray, $createdAt, $updatedAt, $lastUsedAt);
		$response["error"] = FALSE;
		$response["track"]["id"] = $track["TRACK_NO"];
		$response["track"]["start_poi_no"] = $track["START_POI_NO"];
		$response["track"]["dest_poi_no"] = $track["DEST_POI_NO"];
		$response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
		$response["track"]["created_at"] = $track["CREATED_AT"];
		$response["track"]["updated_at"] = $track["UPDATED_AT"];
		$response["track"]["last_used_at"] = $track["LAST_USED_AT"];
		echo json_encode($response);
	} else {
		// create a new POI
		$track = $db->storeTrack($startPOINo, $destPOINo, $stopPOINoArray, $createdAt, $updatedAt, $lastUsedAt);
		if ($track) {
			// poi stored successfully
			$response["error"] = FALSE;
			$response["track"]["id"] = $track["TRACK_NO"];
			$response["track"]["start_poi_no"] = $track["START_POI_NO"];
			$response["track"]["dest_poi_no"] = $track["DEST_POI_NO"];
			$response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
			$response["track"]["created_at"] = $track["CREATED_AT"];
			$response["track"]["updated_at"] = $track["UPDATED_AT"];
			$response["track"]["last_used_at"] = $track["LAST_USED_AT"];
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