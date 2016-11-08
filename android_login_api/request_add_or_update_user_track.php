<?php 

require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("reguest_add_or_update_or_user_track_page 입니다~");

// json response array
$response = array("error" => FALSE);

if(isset($_POST['recent']) or isset($_POST['bookmark'])) {

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
        // START_POI 정보를 가져옴
        $startPOILatLng = $_POST['START_POI_LAT_LNG'];
        $startPOI = $db->getPOIByPoiLatLng($startPOILatLng);
        $startPOINo = $startPOI['POI_NO'];

        // DEST_POI 정보를 가져옴
        $destPOILatLng = $_POST['DEST_POI_LAT_LNG'];
        $destPOI = $db->getPOIByPoiLatLng($destPOILatLng);
        $destPOINo = $destPOI['POI_NO'];

        if (isset($startPOINo)  && isset($destPOINo)) {
            // receiving the post params
            // int로 변환 http://stackoverflow.com/questions/5052932/how-to-get-int-instead-string-from-form
            $stopPOIArray = null;
            if (isset($_POST['STOP_POI_ARRAY'])) {
                $stopPOIArray = $_POST['STOP_POI_ARRAY'];
            }

            // check if track is already existed with the same start_poi_no, dest_poi_no, stop_poi_no(optional)
            if ($db->isTrackExisted($startPOINo, $destPOINo, $stopPOIArray)) {
                // 기존에 경로가 있다면 진행.

                $track = $db->getTrack($startPOINo, $destPOINo, $stopPOIArray);
                if( $track ) {
                    error_log("his");
                    error_log(json_encode($track));
                    error_log("hi");
                    $trackNo = $track['TRACK_NO'];
                    // track already existed
                    // start_poi_no, dest_poi_no, stop_poi_no(optional)를 가진 user_bookmark_track table이 있는지 확인
                    $user_bookmark = null;

                    // start_poi_no, dest_poi_no, stop_poi_no(optional)를 가진 user_track table이 있는지 확인
                    if ($db->isUSER_TRACKExisted($userNo, $trackNo)) {
                        // 기존에 userTrack 정보가 있다면 업데이트 할것.
                        error_log("업데이트 왔니?");
                        $db->updateLastUsedAtUserTrack($userNo, $trackNo);
                        error_log("업데이트 했니?");
                        $response["error"] = FALSE;
                        error_log(json_encode($response));
                        if(isset($_POST['recent'])) {
                            echo json_encode($response);
                        }
                    } else {
                        // 기존에 userTrack 정보가 없다면 추가할 것
                        error_log("store 시도");
                        $user_track = $db->storeUSERTrack($userNo, $trackNo);
                        error_log("store 되야됨");
                        // track stored successfully
                        $response["error"] = FALSE;
                        $start_poi = $db->getPOIUsingPOIID($track["START_POI_NO"]);
                        $response["track"]["start_poi"] = save_poi($response, $start_poi);
                        $dest_poi = $db->getPOIUsingPOIID($track["DEST_POI_NO"]);
                        $response["track"]["dest_poi"] = save_poi($response, $dest_poi);
                        $response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
                        $response["track"]["created_at"] = $user_track["CREATED_AT"];
                        $response["track"]["updated_at"] = $user_track["UPDATED_AT"];
                        $response["track"]["last_used_at"] = $user_track["LAST_USED_AT"];
                        if(isset($_POST['recent'])) {
                            echo json_encode($response);
                        }
                    }

                    if(isset($_POST['bookmark'])) {
                        if ($db->isUSER_BookMarkTRACKExisted($userNo, $trackNo)) {
                            // 유저가 북마크한 기록이 있는 경우 업데이트, 아닌 경우는 추가.
                            error_log("북마크 업데이트니?");
                            $user_bookmark = $db->updateLastUsedAtUserBookmarkTrack($userNo, $trackNo);
                            $response["error"] = FALSE;
                            error_log(json_encode($response));
                            echo json_encode($response);
                        } else {
                            error_log("북마크 저장하니?");
                            $user_bookmark = $db->storeBookmarkUSERTrack($userNo, $trackNo);
                            $response["error"] = FALSE;
                            error_log(json_encode($response));
                            echo json_encode($response);
                        }
                    } else {
                        $response["error"] = TRUE;
                        $response["error_msg"] = "recent와 post가 없네요 뉴뉴 !";
                        echo json_encode($response);
                    }
                } else {
                    $response["error"] = TRUE;
                    $response["error_msg"] = "경로를 가져오는 동안 오류가 생겼습니다.!";
                    echo json_encode($response);
                }
            } else {
                // create a new Track
                $track = $db->storeTrack($startPOINo, $destPOINo, $stopPOIArray);
                $trackNo = $track["TRACK_NO"];
                $user_track = $db->storeUSERTrack($userNo, $track["TRACK_NO"]);
                if(isset($_POST['bookmark'])) {
                    $db->storeBookmarkUSERTrack($userNo, $trackNo);
                }
                if ($track) {
                    // track stored successfully
                    $response["error"] = FALSE;
                    $start_poi = $db->getPOIUsingPOIID($track["START_POI_NO"]);
                    $response["track"]["start_poi"] = save_poi($response, $start_poi);
                    $dest_poi = $db->getPOIUsingPOIID($track["DEST_POI_NO"]);
                    $response["track"]["dest_poi"] = save_poi($response, $dest_poi);
                    $response["track"]["stop_poi_no_array"] = $track["STOP_POI_NO_ARRAY"];
                    $response["track"]["created_at"] = $user_track["CREATED_AT"];
                    $response["track"]["updated_at"] = $user_track["UPDATED_AT"];
                    $response["track"]["last_used_at"] = $user_track["LAST_USED_AT"];
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
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "recent나 bookmark가 없네요.!";
    echo json_encode($response);
}

/**
 * @param $response : 저장할 배열
 * @param $poi : poi 정보
 * @return bool
 */
function save_poi($response, $poi) {
    if($poi) {
        $response["poiName"] = $poi["POI_NAME"];
        $response["poiAddress"] = $poi["POI_ADDRESS"];
        $response["poiLatLng"] = $poi["POI_LAT_LNG"];
        $response["created_at"] = $poi["CREATED_AT"];
        $response["updated_at"] = $poi["UPDATED_AT"];
        $response["last_used_at"] = $poi["LAST_USED_AT"];
        return $response;
    } else {
        return false;
    }
}
