<?php
require_once './android_login_api/include/DB_Config.php';
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
    $response["error"] = false;
}

$userNo = -1;
if(isset($user)) {
    $userNo = $user['USER_NO'];
}

if (isset($_POST['MESSAGE'])) {
    // receiving the post params
    // int로 변환 http://stackoverflow.com/questions/5052932/how-to-get-int-instead-string-from-form
    $message = $_POST['MESSAGE'];
    // check if poi is already existed with the same poiLatLNg
    $result = $db->storeUSERErrorMessage($userNo, $message);
    if($result) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "유저 - 에러가 저장되지 않았습니다.";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "메시지 정보가 없습니다!";
    echo json_encode($response);
}
