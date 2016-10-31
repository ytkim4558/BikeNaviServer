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
	 * Storing new poi
	 * return poi details
	 */
	public function storePOI($poiLatLng, $poiName) {
		$stmt = $this->conn->prepare("INSERT INTO POI_TB(POI_NAME, POI_LAT_LNG, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ss", $poiName, $poiLatLng);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT LAST_USED_AT FROM POI_TB WHERE POI_LAT_LNG = ?");
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
	
	// POI 사용시각 업데이트
	public function updateLASTUSEDPOIWithPOI_LAT_LNG($poiLatLng) {
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
	
	/** USER POI TB
	 * 
	 */
	
	/**
	 * Storing new user_poi
	 * return user_poi details
	 */
	public function storeUSERPOI($userNo, $poiNo) {
		$stmt = $this->conn->prepare("INSERT INTO USER_POI_TB(USER_NO, POI_NO, CREATED_AT, UPDATED_AT, LAST_USED_AT) VALUES(?, ?, NOW(), NOW(), NOW())");
		$stmt->bind_param("ss", $userNo, $poiNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USER_POI_TB WHERE WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ss", $userNo, $poiNo);
			$stmt->execute();
			$user_poi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
	
			return $user_poi;
		} else {
			return false;
		}
	}
	
	/**
	 * Check user_poi is existed or not
	 */
	public function isUSER_POIExisted($userNo, $poiNo) {
		$stmt = $this->conn->prepare("SELECT USER_NO from USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
		$stmt->bind_param("ss", $userNo, $poiNo);
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
	
	// USER_POI 업데이트
	public function updateUSREPOILastUsedWithUSER_NOandPOI_NO($userNo, $poiNo) {
		$stmt = $this->conn->prepare("UPDATE USER_POI_TB SET LAST_USED_AT = NOW() WHERE USER_NO = ? AND POI_NO = ?");
		$stmt->bind_param("ss", $userNo, $poiNo);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT LAST_USED_AT FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?");
			$stmt->bind_param("ss", $userNo, $poiNo);
			if($stmt->execute()) {
				$userpoi = $stmt->get_result()->fetch_assoc();
				$stmt->close();
	
				return $userpoi;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * @param $poiLatLng : 장소
	 * @return poi 배열
	 */
	public function getUSERPOIByPoiLatLng($userNo, $poiNo) {
		$query = "SELECT * FROM USER_POI_TB WHERE USER_NO = ? AND POI_NO = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("ss", $userNo, $poiNo);
		if($stmt->execute()) {
			$userpoi = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			return $userpoi;
		} else {
			return NULL;
		}
	}
	
	
	/**
	 * Check user is existed or not
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
	 * Check user is existed or not
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
	 * Encrypting password
	 * @param password
	 * return salt and encrypted passwrd
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
	 * @param salt, password
	 * return hash string
	 */
	public function checkhashSSHA($salt, $password) {
		$hash = base64_encode(sha1($password . $salt, true) . $salt);
		return $hash;
	}
}

?>