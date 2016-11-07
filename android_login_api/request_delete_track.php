<?php 
// 장소를 추가하거나 갱신처리하는 페이지
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("reguest_delete_track_page 입니다~");

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
    if (isset($_POST['START_POI_LAT_LNG']) && isset($_POST['DEST_POI_LAT_LNG'])) {
        // START_POI 정보를 가져옴
        $startPOILatLng = $_POST['START_POI_LAT_LNG'];
        $startPOI = $db->getPOIByPoiLatLng($startPOILatLng);
        $startPOINo = $startPOI['POI_NO'];

        // DEST_POI 정보를 가져옴
        $destPOILatLng = $_POST['DEST_POI_LAT_LNG'];
        $destPOI = $db->getPOIByPoiLatLng($destPOILatLng);
        $destPOINo = $destPOI['POI_NO'];

        if (isset($startPOINo) && isset($destPOINo)) {
            // receiving the post params
            // int로 변환 http://stackoverflow.com/questions/5052932/how-to-get-int-instead-string-from-form
            $stopPOIArray = null;
            if (isset($_POST['STOP_POI_ARRAY'])) {
                $stopPOIArray = $_POST['STOP_POI_ARRAY'];
            }

            // check if track is already existed with the same start_poi_no, dest_poi_no, stop_poi_no(optional)
            if ($db->isTrackExisted($startPOINo, $destPOINo, $stopPOIArray)) {
                $track = $db->getTrack($startPOINo, $destPOINo, $stopPOIArray);
                if ($track) {
                    $trackNo = $track['TRACK_NO'];
                    // track already existed
                    // start_poi_no, dest_poi_no, stop_poi_no(optional)를 가진 user_track table이 있는지 확인
                    if ($db->isUSER_TRACKExisted($userNo, $trackNo)) {
                        // 기존에 userTrack 정보가 있다면 삭제할것
                        if (isset($_POST['recent'])) {
                            $db->deleteUSERTrack($userNo, $trackNo);
                        } else if ($_POST['bookmark']) {
                            $db->deleteUSERBookmarkTrack($userNo, $trackNo);
                        } else {
                            $response["error"] = TRUE;
                            $response["error_msg"] = "recent, bookmark 누락.!";
                            echo json_encode($response);
                        }
                    } else {
                        // 기존에 userTrack 정보가 없다면 추가할 것
                        if ($_POST['recent']) {
                            error_log("store 시도");
                            $db->storeUSERTrack($userNo, $trackNo);
                            error_log("store 되야됨");
                            // track stored successfully
                            $response["error"] = FALSE;
                            $start_poi = $db->getPOIUsingPOIID($track["START_POI_NO"]);
                            $response["track"]["start_poi"] = save_poi($response, $start_poi);
                            $dest_poi = $db->getPOIUsingPOIID($track["DEST_POI_NO"]);
                            $response["track"]["dest_poi"] = save_poi($response, $dest_poi);
                            $response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
                            $response["track"]["created_at"] = $track["CREATED_AT"];
                            $response["track"]["updated_at"] = $track["UPDATED_AT"];
                            $response["track"]["last_used_at"] = $track["LAST_USED_AT"];
                            echo json_encode($response);
                        } else if ($_POST['bookmark']) {
                            $db->deleteUSERBookmarkTrack($userNo, $trackNo);
                        } else {
                            $response["error"] = TRUE;
                            $response["error_msg"] = "recent, bookmark 누락.!";
                            echo json_encode($response);
                        }
                    }
                } else {
                    $response["error"] = TRUE;
                    $response["error_msg"] = "경로를 가져오는 동안 오류가 생겼습니다.!";
                    echo json_encode($response);
                }
            } else {
                // create a new Track
                $track = $db->storeTrack($startPOINo, $destPOINo, $stopPOIArray);
                if ($track) {
                    // track stored successfully
                    $response["error"] = FALSE;
                    $start_poi = $db->getPOIUsingPOIID($track["START_POI_NO"]);
                    $response["track"]["start_poi"] = save_poi($response, $start_poi);
                    $dest_poi = $db->getPOIUsingPOIID($track["DEST_POI_NO"]);
                    $response["track"]["dest_poi"] = save_poi($response, $dest_poi);
                    $response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
                    $response["track"]["created_at"] = $track["CREATED_AT"];
                    $response["track"]["updated_at"] = $track["UPDATED_AT"];
                    $response["track"]["last_used_at"] = $track["LAST_USED_AT"];
                    echo json_encode($response);
                } else {
                    // user failed to store
                    $response["error"] = TRUE;
                    $response["error_msg"] = "경로를 등록하는 동안 알 수 없는 오류가 발생했습니다!";
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
        $response["error_msg"] = "유저 정보가 없습니다!";
        echo json_encode($response);
    }
}
