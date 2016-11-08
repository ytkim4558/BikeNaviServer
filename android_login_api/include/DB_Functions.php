<?php 
/**
 * @author Kim YongTak
 * reference : @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial  
 */

class DB_Functions {
	private $conn;
	
	// constructor
	function __construct() {
		require_once 'DB_Connect.php';
		// connecting to database
		$db = new DB_Connect();
		$this->conn = $db->connect();
	}
	
	// destructor
	function __destruct() {
		
	}
	
	/**
	 * Storing new user
	 * return user details
	 */
	public function storeUser($email, $password) {
		$hash = $this->hashSSHA($password);
		$encrypted_password = $hash["encrypted"]; // encrypted password
		$salt = $hash["salt"]; // salt
		
		$stmt = $this->conn->prepare("INSERT INTO USERS(USER_EMAIL, USER_PW, SALT, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("sss", $email, $encrypted_password, $salt);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
		
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE USER_EMAIL = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			
			return $user;
		} else {
			return false;
		}
	}
	
	/**
	 * 구글이메일 사용
	 * Storing new user
	 * return user details
	 * 추후 storeUserWithGoogleEmailnAccessTokennRefreshToken($email, $accessToken, $refreshToken) 변경 해야한다.
	 */
	public function storeUserWithGoogleEmail($email) {
		$stmt = $this->conn->prepare("INSERT INTO USERS(GOOGLE_EMAIL, CREATED_AT) VALUES(?, NOW())");
		$stmt->bind_param("s", $email);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE GOOGLE_EMAIL = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return false;
		}
	}
	
	// 카카오톡 
	public function storeUserWithKakaoIDAndNickName($id, $nickname) {
		$stmt = $this->conn->prepare("INSERT INTO USERS(KAKAO_ID, KAKAO_NICK_NAME, CREATED_AT) VALUES(?, ?, NOW())");
		$stmt->bind_param("ss", $id, $nickname);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE KAKAO_ID = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return false;
		}
	}
	
	// 카카오톡 닉네임 가져옴.
	public function getUserNickNameWithKakaoID($id) {
		$stmt = $this->conn->prepare("SELECT KAKAO_NICK_NAME FROM USERS WHERE KAKAO_ID = ?");
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$stmt->bind_result($kakaoNick);
		$stmt->fetch();
		$stmt->close();

		return $kakaoNick;
	}
	
	// 카카오톡 아이디,비밀번호 다르게 변경되었는지 조사
	public function isUserChangedWithKakaoIDAndNickName($id, $nickname) {
		// 먼저 정보를 가져옴.
		$kakaoNick = $this->getUserNickNameWithKakaoID($id);
		error_log("kakaonick : " . $nickname);
		error_log("nick : " . $kakaoNick);
		if($nickname === $kakaoNick) {
			return false;
		} else {
			return true;
		}
	}
	
	// 카카오톡 업데이트
	public function updateUserWithKakaoIDAndNickName($id, $nickname) {
		$stmt = $this->conn->prepare("UPDATE USERS SET KAKAO_NICK_NAME = ?, UPDATED_AT = NOW() WHERE KAKAO_ID = ?");
		$stmt->bind_param("ss", $nickname, $id);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE KAKAO_ID = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return false;
		}
	}
	
	// 페이스북
	public function storeUserWithFacebookIDAndName($id, $name) {
		$stmt = $this->conn->prepare("INSERT INTO USERS(FACEBOOK_ID_NUM, FACEBOOK_USER_NAME, CREATED_AT) VALUES(?, ?, NOW())");
		$stmt->bind_param("ss", $id, $name);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE FACEBOOK_ID_NUM = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return false;
		}
	}
	
	/**
	 * 구글이메일 사용
	 * Storing new user
	 * return user details
	 */
	public function storeUserWithGoogleEmailnAccessTokennRefreshToken($email, $accessToken, $refreshToken) {
		
		error_log("accesstoken : " . $accessToken . "refreshToken : " . $refreshToken);
		$stmt = $this->conn->prepare("INSERT INTO USERS(CREATED_AT, GOOGLE_EMAIL, GOOGLE_ACCESS_TOKEN, GOOGLE_REFRESH_TOKEN) VALUES(NOW(), ?, ?, ?)");
		$stmt->bind_param("sss", $email, $accessToken, $refreshToken);
		
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE USER_EMAIL = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
				
			return $user;
		} else {
			return false;
		}
	}
	
	/**
	 * Get user by 구글 토큰
	 */
	public function getGoogleUserByEmail($email) {
		$query = "SELECT * FROM USERS WHERE GOOGLE_EMAIL = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $email);
		if($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
				
			return $user;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get user by 카카오id
	 */
	public function getKakaoUserByID($id) {
		$query = "SELECT * FROM USERS WHERE KAKAO_ID = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $id);
		if($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get user by 페북 이메일
	 */
	public function getFacebookUserByID($id) {
		$query = "SELECT * FROM USERS WHERE FACEBOOK_ID_NUM = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $id);
		if($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get user by email and password
	 */
	public function getUserByEmailAndPassword($email, $password) {
		$query = "SELECT * FROM USERS WHERE USER_EMAIL = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $email);
		if($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			
			// verifying user password
			$salt = $user['SALT'];
			$encrypted_password = $user['USER_PW'];
			$hash = $this->checkhashSSHA($salt, $password);
			// check for password equality
			if ($encrypted_password == $hash) {
				// user authentification details are correct
				return $user;
			}
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get user by email
	 */
	public function getUserByEmail($email) {
		$query = "SELECT * FROM USERS WHERE USER_EMAIL = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $email);
		if($stmt->execute()) {
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
				
			// user 반환
			return $user;
		} else {
			return NULL;
		}
	}
	
	
	
	/**
	 * Check user is existed or not
	 */
	public function isUserExisted($email) {
		$stmt = $this->conn->prepare("SELECT USER_EMAIL from USERS WHERE USER_EMAIL = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows() > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}
	
	/**
	 * Storing new track
	 * @param int $START_POI_NO 시작 장소 번호
	 * @param int $DEST_POI_NO 도착 장소 번호
	 * @param String $STOP_POI_NO_ARRAY	경유지 번호 리스트 
	 * @return track|boolean 트랙 정보나 false 리턴
	 */
	public function storeTrack($START_POI_NO, $DEST_POI_NO, $STOP_POI_NO_ARRAY) {
		$stmt = $this->conn->prepare("INSERT INTO TRACK_TB(START_POI_NO, DEST_POI_NO, STOP_POI_NO_ARRAY, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("iis", $START_POI_NO, $DEST_POI_NO, $STOP_POI_NO_ARRAY);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			if(isset($STOP_POI_NO_ARRAY)) {
				$stmt = $this->conn->prepare("SELECT * FROM TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ? AND STOP_POI_NO_ARRAY = ?");
				$stmt->bind_param("iis", $START_POI_NO, $DEST_POI_NO, $STOP_POI_NO_ARRAY);
			} else {
				$stmt = $this->conn->prepare("SELECT * FROM TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ?");
				$stmt->bind_param("ii", $START_POI_NO, $DEST_POI_NO);
			}
			$stmt->execute();
			$track = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $track;
		} else {
			return false;
		}
	}
	
	/**
	 * Storing new poi
	 * return poi details
	 */
	public function storePOI($poiLatLng, $poiName, $poiAddress) {
		$stmt = $this->conn->prepare("INSERT INTO POI_TB(POI_NAME, POI_LAT_LNG, POI_ADDRESS, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("sss", $poiName, $poiLatLng, $poiAddress);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM POI_TB WHERE POI_LAT_LNG = ?");
			$stmt->bind_param("s", $poiLatLng);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
				
			return $user;
		} else {
			return false;
		}
	}

    /**
     * @param $poiID : poi 숫자번호
     * @return array|bool : poi
     */
    public function getPOIUsingPOIID($poiID) {
        $stmt = NULL;
        $stmt = $this->conn->prepare("SELECT * from POI_TB WHERE POI_NO = ?");
        $stmt->bind_param("i", $poiID);
        $result = $stmt->execute();
        if($result) {
            $poi = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $poi;
        } else {
            return false;
        }
    }
	
	/**
	 * 경로 가져오기
	 * @param int $start_poi_no 시작 장소 번호
	 * @param int $dest_poi_no 도착 장소 번호
	 * @param String $stop_poi_no_array 경로 번호 리스트 json
	 * @return track|boolean track 정보 가져오거나 false 
	 */
	public function getTrack($start_poi_no, $dest_poi_no, $stop_poi_no_array) {
		$stmt = NULL;
		if (isset($stop_poi_no_array)) {
			$stmt = $this->conn->prepare("SELECT * from TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ? AND STOP_POI_NO_ARRAY = ?");
			$stmt->bind_param("iis", $start_poi_no, $dest_poi_no, $stop_poi_no_array);
		} else {
			$stmt = $this->conn->prepare("SELECT * from TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ?");
			$stmt->bind_param("ii", $start_poi_no, $dest_poi_no);
		}
		$result = $stmt->execute();
		if($result) {
			$track = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $track;
		} else {
			return false;
		}
	}
	
	/**
	 * Check track is existed or not
	 */
	public function isTrackExisted($start_poi_no, $dest_poi_no, $stop_poi_no_array) {
		$stmt = NULL;
		if (isset($stop_poi_no_array)) {
			$stmt = $this->conn->prepare("SELECT TRACK_NO from TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ? AND STOP_POI_NO_ARRAY = ?");
			$stmt->bind_param("iis", $start_poi_no, $dest_poi_no, $stop_poi_no_array);
		} else {
			$stmt = $this->conn->prepare("SELECT TRACK_NO from TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ?");
			$stmt->bind_param("ii", $start_poi_no, $dest_poi_no);
		}
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows() > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}
	
	/**
	 * Check poi is existed or not
	 */
	public function isPOIExisted($poiLatLng) {
		$stmt = $this->conn->prepare("SELECT POI_LAT_LNG from POI_TB WHERE POI_LAT_LNG = ?");
		$stmt->bind_param("s", $poiLatLng);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows() > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}
	
	// USER_POI 정보 삭제하는 함수
	/**
	 *
	 * @param int $userNo	: 유저 번호
	 * @param int $poiNo	: 장소 번호
	 * @return array|boolean
	 */
	public function deleteUSERPOIAtUserPOI($userNo, $poiNo) {
		$stmt = $this->conn->prepare("DELETE FROM USER_POI_TB WHERE POI_NO = ? AND USER_NO = ?");
		$stmt->bind_param("ii", $poiNo, $userNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful delete
		if($result) {
			return true;
		} else {
			return false;
		}
	}

    // USER_TRACK 정보 업데이트
    /**
     *
     * @param int $userNo 유저 번호
     * @param int $trackNo 장소 번호
     * @return array|boolean
     */
    public function updateLastUsedAtUserBookmarkTrack($userNo, $trackNo) {
        $stmt = $this->conn->prepare("UPDATE USER_BOOKMARK_TRACK_TB SET LAST_USED_AT = NOW() WHERE TRACK_NO = ? AND USER_NO = ?");
        $stmt->bind_param("ii", $trackNo, $userNo);
        $result = $stmt->execute();
        error_log(htmlspecialchars($stmt->error), 0);
        $stmt->close();

        // check for successful store
        if($result) {
            $stmt = $this->conn->prepare("SELECT * FROM USER_BOOKMARK_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
            $stmt->bind_param("ii", $userNo, $trackNo);
            if($stmt->execute()) {
                $user_bookmark_track = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                return $user_bookmark_track;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	// USER_TRACK 정보 업데이트
	/**
	 *
	 * @param int $userNo 유저 번호
	 * @param int $trackNo 장소 번호
	 * @return array|boolean
	 */
	public function updateLastUsedAtUserTrack($userNo, $trackNo) {
		$stmt = $this->conn->prepare("UPDATE USER_TRACK_TB SET LAST_USED_AT = NOW() WHERE TRACK_NO = ? AND USER_NO = ?");
		$stmt->bind_param("ii", $trackNo, $userNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
			$stmt->bind_param("ii", $userNo, $trackNo);
			if($stmt->execute()) {
				$user_track = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $user_track;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// USER_POI 정보 업데이트
	/**
	 *
	 * @param int $userNo	: 유저 번호
	 * @param int $poiNo	: 장소 번호
	 * @return array|boolean
	 */
	public function updateLastUsedAtUserPOI($userNo, $poiNo) {
		$stmt = $this->conn->prepare("UPDATE USER_POI_TB SET LAST_USED_AT = NOW() WHERE POI_NO = ? AND USER_NO = ?");
		$stmt->bind_param("ii", $poiNo, $userNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ii", $userNo, $poiNo);
			if($stmt->execute()) {
				$poi = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $poi;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// POI 정보 업데이트
	/**
	 *
	 * @param String $poiLatLng	: 장소 좌표
	 * @return array|boolean
	 */
	public function updateLastUsedAtPOI($poiLatLng) {	
		$stmt = $this->conn->prepare("UPDATE POI_TB SET LAST_USED_AT = NOW() WHERE POI_LAT_LNG = ?");
		$stmt->bind_param("s", $poiLatLng);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM POI_TB WHERE POI_LAT_LNG = ?");
			$stmt->bind_param("s", $poiLatLng);
			if($stmt->execute()) {
				$poi = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $poi;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// Track 정보 업데이트	(출발 장소 id, 도착장소 id, 경유지 id 리스트)
	/**
	 * 
	 * @param int $start_poi_id	: 출발장소 id
	 * @param int $dest_poi_id : 도착장소 id
	 * @param String $stop_id_list : 경유지 리스트 optional
	 * @return array|boolean
	 */
	public function updateLastUsedAtTrack($start_poi_id, $dest_poi_id, $stop_id_list) {
		if(isset($stop_id_list)) {
			$stmt = $this->conn->prepare("UPDATE TRACK_TB SET START_POI_NO = ?, DEST_POI_NO = ?, STOP_POI_NO_ARRAY = ?, LAST_USED_AT = NOW() ");
			$stmt->bind_param("iis", $start_poi_id, $dest_poi_id, $stop_id_list);
		} else {
			$stmt = $this->conn->prepare("UPDATE TRACK_TB SET START_POI_NO = ?, DEST_POI_NO = ?, LAST_USED_AT = NOW() ");
			$stmt->bind_param("ii", $start_poi_id, $dest_poi_id);
		}
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			if(isset($stop_id_list)) {
				$stmt = $this->conn->prepare("SELECT * FROM TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ? AND STOP_POI_NO_ARRAY = ? ");
				$stmt->bind_param("iis", $start_poi_id, $dest_poi_id, $stop_id_list);
			} else {
				$stmt = $this->conn->prepare("SELECT * FROM TRACK_TB WHERE START_POI_NO = ? AND DEST_POI_NO = ?");
				$stmt->bind_param("ii", $start_poi_id, $dest_poi_id);
			}
			if($stmt->execute()) {
				$track = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $track;
			}
		} else {
			return false;
		}
	}
	
	// POI 정보 업데이트
	public function updatePOINameWithPOI_LAT_LNG($poiName, $poiLatLng) {
		$stmt = $this->conn->prepare("UPDATE POI_TB SET POI_NAME = ?, UPDATED_AT = NOW() WHERE POI_LAT_LNG = ?");
		$stmt->bind_param("ss", $poiName, $poiLatLng);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM POI_TB WHERE POI_LAT_LNG = ?");
			$stmt->bind_param("s", $poiLatLng);
			if($stmt->execute()) {
				$poi = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $poi;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * @param $poiLatLng : 장소
	 * @return poi 배열
	 */
	public function getPOIByPoiLatLng($poiLatLng) {
		$query = "SELECT * FROM POI_TB WHERE POI_LAT_LNG = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $poiLatLng);
		if($stmt->execute()) {
			$poi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $poi;
		} else {
			return NULL;
		}
	}
	
	/**
	 * @param $userNo : 유저아이디
	 * @param $start : 시작번호
	 * @param $limit : 가져올 아이템 개수
	 * @return array : 북마크 경로 리스트
     */
	public function getRangeUserBookMarkOfTrackUsingUserNo($userNo, $start, $limit) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
		$query = "SELECT * FROM USER_BOOKMARK_TRACK_TB WHERE USER_NO = ? LIMIT ?, ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("iii", $userNo, $start, $limit);
	
		// arraydp 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
		$res = array();
	
		if($stmt->execute()) {
            $result = $stmt->get_result();
            if($result) {
                while($user_bookmark_track = $result->fetch_assoc()) {
                    // TRACK_TB : 경로 테이블
                    $query = "SELECT * FROM TRACK_TB WHERE TRACK_NO = ?";
                    $stmt2 = $this->conn->prepare($query);
                    $stmt2->bind_param("i", $user_bookmark_track['TRACK_NO']);
                    if($stmt2->execute()) {
                        if($result2 = $stmt2->get_result()) {
                            while($track_row = $result2->fetch_assoc()) {
                                $start_poi = $this->getPOIUsingPOIID($track_row["START_POI_NO"]);
                                $dest_poi = $this->getPOIUsingPOIID($track_row["DEST_POI_NO"]);
                                array_push($res, array(
                                        "start_poi"=>$this->save_poi($start_poi),
                                        "dest_poi"=>$this->save_poi($dest_poi),
                                        "stop_list"=>$track_row["STOP_POI_NO_ARRAY"],
                                        "created_at"=>$user_bookmark_track["CREATED_AT"],
                                        "updated_at"=>$user_bookmark_track["UPDATED_AT"],
                                        "last_used_at"=>$user_bookmark_track["LAST_USED_AT"],
                                        "bookmarked"=>$this->isUSER_BookMarkTRACKExisted($userNo,$track_row['TRACK_NO']))
                                );
                            }
                        }
                        $stmt2->close();
                    }
                }
            }

			$stmt->close();
			return $res;
		} else {
			return NULL;
		}
	}

    /**
     * @param $poi : poi 정보
     * @return array
     */
    private function save_poi($poi) {
        $response = array();
        if($poi) {
            $response["poiName"] = $poi["POI_NAME"];
            $response["poiAddress"] = $poi["POI_ADDRESS"];
            $response["poiLatLng"] = $poi["POI_LAT_LNG"];
            $response["created_at"] = $poi["CREATED_AT"];
            $response["updated_at"] = $poi["UPDATED_AT"];
            $response["last_used_at"] = $poi["LAST_USED_AT"];
            return $response;
        } else {
            return null;
        }
    }

    /**
     * @param $userNo : 유저아이디
     * @return string : 북마크 경로 리스트
     */
	public function getAllUserBookMarkOfTrackUsingUserNo($userNo) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블 
		$query = "SELECT * FROM USER_BOOKMARK_TRACK_TB WHERE USER_NO = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("i", $userNo);
		
		// arraydp 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
		$res = array();
		
		if($stmt->execute()) {
			while($track = $stmt->get_result()->fetch_assoc()) {
				// TRACK_TB : 경로 테이블
				$query = "SELECT * FROM TRACK_TB WHERE NO = ?";
				$stmt2 = $this->conn->prepare($query);
				$stmt2->bind_param("i", $track['TRACK_NO']);
				if($stmt2->execute()) {
					while($track_row = $stmt2->get_result()->fetch_assoc()) {
                        $start_poi = $this->getPOIUsingPOIID($track_row["START_POI_NO"]);
                        $dest_poi = $this->getPOIUsingPOIID($track_row["DEST_POI_NO"]);
						array_push($res, array(
								"bookmark_start_poi"=>$start_poi,
								"bookmark_dest_poi"=>$dest_poi,
								"bookmark_stop_list"=>$track_row["STOP_POI_NO_ARRAY"],
								"bookmark_created_at"=>$track_row["CREATED_AT"],
								"bookmark_updated_at"=>$track_row["UPDATED_AT"],
								"bookmark_last_used_at"=>$track_row["LAST_USED_AT"])
								);
					}
					$stmt2->close();
				}
				
			}
			$stmt->close();
			return json_encode($res);
		} else {
			return NULL;
		}
	}

    /**
     * @param $userNo : 유저아이디
     * @return int | null 유저가 검색한 장소리스트 개수
     */
    public function getCountOfUserTrackList($userNo) {
        // USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
        $query = "SELECT * FROM USER_TRACK_TB WHERE USER_NO = ?";
        $stmt = $this->conn->prepare($query);

        // 유저번호
        $stmt->bind_param("i", $userNo);

        /* 쿼리 실행 */
        if($stmt->execute()) {
            /* 결과 저장*/
            $stmt->store_result();

            $res = $stmt->num_rows;

            $stmt->close();
            return $res;
        } else {
            return NULL;
        }
    }
	
	/**
	 * @param $userNo : 유저아이디
	 * @return int | null 유저가 검색한 장소리스트 개수
	 */
	public function getCountOfUserPOIList($userNo) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
		$query = "SELECT * FROM USER_POI_TB WHERE USER_NO = ?";
		$stmt = $this->conn->prepare($query);
	
		// 유저번호
		$stmt->bind_param("i", $userNo);
	
		/* 쿼리 실행 */
		if($stmt->execute()) {
			/* 결과 저장*/
			$stmt->store_result();
			
			$res = $stmt->num_rows;
			
			$stmt->close();
			return $res;
		} else {
			return NULL;
		}
	}

    /**
     * @param $userNo : 유저번호
     * @param $start : 시작번호
     * @param $limit : 가져올 아이템 개수
     * @return array|null : 유저별 장소리스트에서 범위별
     */
    public function getRangeUserBookMarkPOIListUsingUserNo($userNo, $start, $limit) {
        // USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
        $query = "SELECT * FROM USER_BOOKMARK_POI_TB WHERE USER_NO = ? LIMIT ?, ?";
        error_log($query);
        error_log("query : ".$query);
        $stmt = $this->conn->prepare($query);

        // 유저번호, 리스트 보이는 부분 시작 위치, 리스트 보이는부분 끝 위치
        $stmt->bind_param("iii", $userNo, $start, $limit);

        // arraydp 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
        $res = array();

        if($stmt->execute()) {
            if($result = $stmt->get_result()) {
                while($user_poi_row = $result->fetch_assoc()) {
                    // POI_TB : 경로 테이블
                    $query = "SELECT * FROM POI_TB WHERE POI_NO = ?";
                    $stmt2 = $this->conn->prepare($query);
                    $stmt2->bind_param("i", $user_poi_row['POI_NO']);
                    if($stmt2->execute()) {
                        $poi = $stmt2->get_result()->fetch_assoc();
                        error_log(json_encode($poi));
                        array_push($res, array(
                                "poiName"=>$poi["POI_NAME"],
                                "poiAddress"=>$poi["POI_ADDRESS"],
                                "poiLatLng"=>$poi["POI_LAT_LNG"],
                                "created_at"=>$user_poi_row["CREATED_AT"],
                                "updated_at"=>$user_poi_row["UPDATED_AT"],
                                "last_used_at"=>$user_poi_row["LAST_USED_AT"])
                        );
                        $stmt2->close();
                    }
                }
            } else {
                error_log("get_result is nothing?");
            }
            $stmt->close();
            return $res;
        } else {
            return NULL;
        }
    }

    /**
     * @param $userNo : 유저번호
     * @param $start : 시작번호
     * @param $limit : 가져올 아이템 개수
     * @return array|null : 유저별 장소리스트에서 범위별
     */
	public function getRangeUserRecentPOIListUsingUserNo($userNo, $start, $limit) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
		$query = "SELECT * FROM USER_POI_TB WHERE USER_NO = ? ORDER BY LAST_USED_AT DESC LIMIT ?, ?";
		error_log("query : ".$query);
		$stmt = $this->conn->prepare($query);
	
		// 유저번호, 리스트 보이는 부분 시작 위치, 리스트 보이는부분 끝 위치
		$stmt->bind_param("iii", $userNo, $start, $limit);
	
		// arraydp 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
		$res = array();
	
		if($stmt->execute()) {
			if($result = $stmt->get_result()) {
				while($user_poi_row = $result->fetch_assoc()) {
					// POI_TB : 경로 테이블
					$query = "SELECT * FROM POI_TB WHERE POI_NO = ?";
					$stmt2 = $this->conn->prepare($query);
					$stmt2->bind_param("i", $user_poi_row['POI_NO']);
					if($stmt2->execute()) {
						$poi = $stmt2->get_result()->fetch_assoc();
//						error_log(json_encode($poi));
						array_push($res, array(
								"poiName"=>$poi["POI_NAME"],
								"poiAddress"=>$poi["POI_ADDRESS"],
								"poiLatLng"=>$poi["POI_LAT_LNG"],
								"created_at"=>$user_poi_row["CREATED_AT"],
								"updated_at"=>$user_poi_row["UPDATED_AT"],
								"last_used_at"=>$user_poi_row["LAST_USED_AT"])
								);
						$stmt2->close();
					}
				}
			} else {
				error_log("get_result is nothing?");
			}
			$stmt->close();
			return $res;
		} else {
			return NULL;
		}
	}
	
	/**
	 * @param $userNo : 유저아이디
	 * @param $start : 시작번호
	 * @param $limit : 끝번호
	 * @return string : 리스트 : 최근 경로 리스트
     */
	public function getRangeUserRecentTrackOfTrackUsingUserNo($userNo, $start, $limit) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
        error_log("start :".$start.", limit : ".$limit);
		$query = "SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? ORDER BY LAST_USED_AT DESC LIMIT ?, ?";
        error_log("query : ".$query);
		$stmt = $this->conn->prepare($query);

		// 유저번호, 리스트 보이는 부분 시작 위치, 리스트 보이는부분 끝 위치
		$stmt->bind_param("iii", $userNo, $start, $limit);
	
		// array 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
		$res = array();
	
		if($stmt->execute()) {
            if($result = $stmt->get_result()) {
                while ($user_track = $result->fetch_assoc()) {
                    // TRACK_TB : 경로 테이블
                    $query = "SELECT * FROM TRACK_TB WHERE TRACK_NO = ?";
                    $stmt2 = $this->conn->prepare($query);
                    $stmt2->bind_param("i", $user_track['TRACK_NO']);
                    error_log($user_track['TRACK_NO']);
                    if ($stmt2->execute()) {
                        $result2 = $stmt2->get_result();
                        if($result2) {
                            while ($track_row = $result2->fetch_assoc()) {
                                array_push($res, array(
                                        "start_poi" => $this->save_poi($this->getPOIUsingPOIID($track_row["START_POI_NO"])),
                                        "dest_poi" => $this->save_poi($this->getPOIUsingPOIID($track_row["DEST_POI_NO"])),
                                        "stop_list" => $track_row["STOP_POI_NO_ARRAY"],
                                        "created_at" => $user_track["CREATED_AT"],
                                        "updated_at" => $user_track["UPDATED_AT"],
                                        "last_used_at" => $user_track["LAST_USED_AT"],
                                        "bookmarked" => $this->isUSER_BookMarkTRACKExisted($userNo, $track_row['TRACK_NO']))
                                );
//                                error_log(json_encode($track_row));
                            }
                        }
                        $stmt2->close();
                    }
                }
            } else {
                error_log("get_result is nothing?");
                return NULL;
            }
			$stmt->close();
            error_log(json_encode($res));
			return json_encode($res);
		} else {
			return NULL;
		}
	}
	
	/**
	 * @param $userNo : 유저아이디
	 * @return string : 최근 경로 리스트
     */
	public function getAllUserRecentTrackOfTrackUsingUserNo($userNo) {
		// USER_BOOKMARK_TB : 유저가 즐겨찾기한 경로 테이블
		$query = "SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? ORDER BY LAST_USED_AT DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("i", $userNo);
	
		// arraydp 결과 삽입 (참고 : https://www.simplifiedcoding.net/android-feed-example-using-php-mysql-volley/)
		$res = array();
	
		if($stmt->execute()) {
			while($user_track = $stmt->get_result()->fetch_assoc()) {
				// TRACK_TB : 경로 테이블
				$query = "SELECT * FROM TRACK_TB WHERE NO = ?";
				$stmt2 = $this->conn->prepare($query);
				$stmt2->bind_param("i", $user_track['TRACK_NO']);
				if($stmt2->execute()) {
					while($track_row = $stmt2->get_result()->fetch_assoc()) {
						array_push($res, array(
                                "start_poi"=>$this->save_poi($this->getPOIUsingPOIID($track_row["START_POI_NO"])),
                                "dest_poi"=>$this->save_poi($this->getPOIUsingPOIID($track_row["DEST_POI_NO"])),
								"stop_list"=>$track_row["STOP_POI_NO_ARRAY"],
								"created_at"=>$user_track["CREATED_AT"],
								"updated_at"=>$user_track["UPDATED_AT"],
								"last_used_at"=>$user_track["LAST_USED_AT"])
								);
					}
					$stmt2->close();
				}
	
			}
			$stmt->close();
			return json_encode($res);
		} else {
			return NULL;
		}
	}
	
	/**
	 * Storing new user_bookmark_track
	 * return user_bookmark_track details
	 */
	public function storeBookmarkUSERTrack($userNo, $trackNo) {
		$stmt = $this->conn->prepare("INSERT INTO USER_BOOKMARK_TRACK_TB(USER_NO, TRACK_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ii", $userNo, $trackNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
			$stmt->bind_param("ii", $userNo, $trackNo);
			$stmt->execute();
			$user_track = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user_track;
		} else {
			return false;
		}
	}

    /**
     * Storing new user_bookmark track
     * @param $userNo : 유저 번호
     * @param $trackNo : 경로 번호
     * @return bool
     */
	public function deleteUSERBookmarkTrack($userNo, $trackNo) {
		$stmt = $this->conn->prepare("DELETE FROM USER_BOOKMARK_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
		$stmt->bind_param("ii", $userNo, $trackNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful delete
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Deleting new user_track
	 * return true,false
	 */
    /**
     * 유저 경로 삭제
     * @param $userNo  : 사용자 번호
     * @param $trackNo : 경로 번호
     * @return bool : 경로가 제대로 삭제되었는지 여부.
     */
	public function deleteUSERTrack($userNo, $trackNo) {
		$stmt = $this->conn->prepare("DELETE FROM USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
		$stmt->bind_param("ii", $userNo, $trackNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful delete
		if($result) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * 유저가 북마크한 경로를 저장하는 함수
     * @param $userNo  : 유저번호
     * @param $trackNo : 경로 번호
     * @return array|bool : 경로 저장되면 경로 리턴, 아니면 false
     */
    public function storeUSERBookmarkTrack($userNo, $trackNo) {
        $stmt = $this->conn->prepare("INSERT INTO USER_BOOKMARK_TRACK_TB(USER_NO, TRACK_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
        $stmt->bind_param("ii", $userNo, $trackNo);
        $result = $stmt->execute();
        error_log(htmlspecialchars($stmt->error), 0);
        $stmt->close();

        // check for successful store
        if($result) {
            $stmt = $this->conn->prepare("SELECT * FROM USER_BOOKMARK_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
            $stmt->bind_param("ii", $userNo, $trackNo);
            $stmt->execute();
            $user_bookmark_track = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user_bookmark_track;
        } else {
            return false;
        }
    }

    /**
     * 유저 에러 저장하는 함수
     * @param $userNo : 유저번호
     * @param $errorMessage : 에러 메시지
     * @return array|bool : 경로 저장되면 경로 리턴, 아니면 false
     */
    public function storeUSERErrorMessage($userNo, $errorMessage) {
        $stmt = $this->conn->prepare("INSERT INTO USER_CLIENT_ERROR_REPORT(USER_NO, MESSAGE, CREATED_AT) VALUES(?, ?, NOW() )");
        $stmt->bind_param("is", $userNo, $errorMessage);
        $result = $stmt->execute();
        error_log(htmlspecialchars($stmt->error), 0);
        $stmt->close();

        // check for successful store
        if($result) {
            $stmt = $this->conn->prepare("SELECT * FROM USER_CLIENT_ERROR_REPORT WHERE USER_NO = ? AND MESSAGE = ?");
            $stmt->bind_param("is", $userNo, $errorMessage);
            $stmt->execute();
            $user_error = $stmt->get_result()->fetch_assoc();
            error_log(json_encode($user_error));
            $stmt->close();

            return true;
        } else {
            return false;
        }
    }

    /**
     * 유저 경로 저장하는 함수
     * @param $userNo  : 유저번호
     * @param $trackNo : 경로 번호
     * @return array|bool : 경로 저장되면 경로 리턴, 아니면 false
     */
	public function storeUSERTrack($userNo, $trackNo) {
		$stmt = $this->conn->prepare("INSERT INTO USER_TRACK_TB(USER_NO, TRACK_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ii", $userNo, $trackNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
			$stmt->bind_param("ii", $userNo, $trackNo);
			$stmt->execute();
			$user_track = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user_track;
		} else {
			return false;
		}
	}

    /**
     * Storing new user_bookmark_poi
     * @param $userNo : 유저번호
     * @param $poiNo  : 장소 번호
     * @return array|bool : user_bookmark_poi details
     */
	public function storeUSERBookMarkPOI($userNo, $poiNo) {
		$stmt = $this->conn->prepare("INSERT INTO USER_BOOKMARK_POI_TB(USER_NO, POI_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ii", $userNo, $poiNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_BOOKMARK_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ii", $userNo, $poiNo);
			$stmt->execute();
			$user_poi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user_poi;
		} else {
			return false;
		}
	}

    /**
     * Storing new user_poi
     * return user_poi details
     * @param $userNo : 유저번호
     * @param $poiNo : 장소번호
     * @return array|bool
     */
	public function storeUSERPOI($userNo, $poiNo) {
		$stmt = $this->conn->prepare("INSERT INTO USER_POI_TB(USER_NO, POI_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ii", $userNo, $poiNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ii", $userNo, $poiNo);
			$stmt->execute();
			$user_poi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user_poi;
		} else {
			return false;
		}
	}

    /**
     * Check user_track is existed or not
     * @param $userNo : 유저 번호
     * @param $trackNo : 경로 번호
     * @return bool
     */
	public function isUSER_TRACKExisted($userNo, $trackNo) {
		$stmt = $this->conn->prepare("SELECT USER_NO from USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
		$stmt->bind_param("ii", $userNo, $trackNo);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}

    /**
     * Check user_bookmark track is existed or not
     * @param $userNo : 유저 번호
     * @param $trackNo : 경로 번호
     * @return bool
     */
    public function isUSER_BookMarkTRACKExisted($userNo, $trackNo) {
        $stmt = $this->conn->prepare("SELECT USER_NO from USER_BOOKMARK_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?");
        $stmt->bind_param("ii", $userNo, $trackNo);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // user existed
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Check user_poi is existed or not
     * @param $userNo : 유저번호
     * @param $poiNo : 장소번호
     * @return bool
     */
	public function updateUSER_POIExisted($userNo, $poiNo) {
		$stmt = $this->conn->prepare("SELECT USER_NO from USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
		$stmt->bind_param("ii", $userNo, $poiNo);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}

    /**
     * Check user_poi is existed or not
     * @param $userNo : 유저번호
     * @param $poiNo : 장소번호
     * @return bool
     */
	public function isUSER_POIExisted($userNo, $poiNo) {
		$stmt = $this->conn->prepare("SELECT USER_NO from USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
		$stmt->bind_param("ii", $userNo, $poiNo);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}
	
	// USER_POI 업데이트
	public function updateUSREPOILastUsedWithUSER_NOandPOI_NO($userNo, $poiNo) {
		$stmt = $this->conn->prepare("UPDATE USER_POI_TB SET LAST_USED_AT = NOW() WHERE USER_NO = ? AND POI_NO = ?");
		$stmt->bind_param("ii", $userNo, $poiNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT LAST_USED_AT FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ii", $userNo, $poiNo);
			if($stmt->execute()) {
				$userPoi = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $userPoi;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @param int $userNo 유저번호
	 * @param int $trackNo 경로번호
	 * @return array
     */
	public function getUSERTrackByUserNoAndTrackNo($userNo, $trackNo) {
		$query = "SELECT * FROM USER_TRACK_TB WHERE USER_NO = ? AND TRACK_NO = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("ii", $userNo, $trackNo);
		if($stmt->execute()) {
			$userTrack = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $userTrack;
		} else {
			return NULL;
		}
	}

    /**
     * @param $userNo : 유저 번호
     * @param $poiNo : 장소 번호
     * @return array
     */
	public function getUSERPOIUsingUserNoAndPOINo($userNo, $poiNo) {
		$query = "SELECT * FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("ii", $userNo, $poiNo);
		if($stmt->execute()) {
			$userPoi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $userPoi;
		} else {
			return NULL;
		}
	}

    /**
     * Check user is existed or not
     * @param $EMAIL : 구글이메일
     * @return bool
     */
	public function isUserExistedWithGoogle($EMAIL) {
		$stmt = $this->conn->prepare("SELECT GOOGLE_EMAIL from USERS WHERE GOOGLE_EMAIL = ?");
		if ($stmt == FALSE) {
			error_log($this->conn->error);
			return false;
		}
		$stmt->bind_param("s", $EMAIL);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}

    /**
     * Check user is existed or not
     * @param $ID : 페북아이디(숫자)
     * @return bool
     */
	public function isUserExistedWithFacebookID($ID) {
		$stmt = $this->conn->prepare("SELECT FACEBOOK_ID_NUM from USERS WHERE FACEBOOK_ID_NUM = ?");
		if ($stmt == FALSE) {
			error_log($this->conn->error);
			return false;
		}
		$stmt->bind_param("s", $ID);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}
	
	/**
	 * Check user is existed or not
	 */
	public function isUserExistedWithKakao($id) {
		$stmt = $this->conn->prepare("SELECT KAKAO_ID from USERS WHERE KAKAO_ID = ?");
		if ($stmt == FALSE) {
			error_log($this->conn->error);
			return false;
		}
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// user existed
			$stmt->close();
			return true;
		} else {
			// user not existed
			$stmt->close();
			return false;
		}
	}

    /**
     * Encrypting password
     * @param password
     * @return array salt and encrypted passwrd
     */
	public function hashSSHA($password) {
		$salt = sha1(rand());
		$salt = substr($salt, 0, 10);
		$encrypted = base64_encode(sha1($password . $salt, true) . $salt);
		$hash = array("salt" => $salt, "encrypted" => $encrypted);
		return $hash;
	}

    /**
     * Decrypting password
     * @param $salt
     * @param $password
     * @return string : hash
     */
	public function checkhashSSHA($salt, $password) {
		$hash = base64_encode(sha1($password . $salt, true) . $salt);
		return $hash;
	}
}