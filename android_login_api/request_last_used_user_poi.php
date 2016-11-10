<?php
// 장소를 추가하거나 갱신처리하는 페이지
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
error_log("request_last_used_user_poi 페이지 입니다~");

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
    // check if poi is already existed with the same poiLatLNg
    if($poi = $db->getLastUserPOI($userNo)) {
        $response = save_poi($response, $poi);
        echo json_encode($response);
    }
    else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "장소가 저장되지 않았습니다.";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "유저 정보가 없습니다..";
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
        $response["poiName"] = $poi["POI_NAME"];
        $response["poiAddress"] = $poi["POI_ADDRESS"];
        $response["poiLatLng"] = $poi["POI_LAT_LNG"];
        $response["created_at"] = $poi["CREATED_AT"];
        $response["updated_at"] = $poi["UPDATED_AT"];
        $response["last_used_at"] = $poi["LAST_USED_AT"];
        $response["bookmarked"] = $poi["bookmarked"];
        return $response;
    } else {
        return false;
    }
}