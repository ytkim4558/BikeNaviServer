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
		
		$stmt = $this->conn->prepare("INSERT INTO USERS(USER_EMAIL, USER_PW, SALT, CREATED_AT) VALUES(?, ?, ?, NOW())");
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
	public function storeUserWithKakaoNickName($email) {
		$stmt = $this->conn->prepare("INSERT INTO USERS(KAKATOK_EMAIL, CREATED_AT) VALUES(?, NOW())");
		$stmt->bind_param("s", $email);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE KAKATOK_EMAIL = ?");
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
	public function storeUserWithFacebookEmail($email) {
		$stmt = $this->conn->prepare("INSERT INTO USERS(FACEBOOK_EMAIL, CREATED_AT) VALUES(?, NOW())");
		$stmt->bind_param("s", $email);
		$result = $stmt->execute();
		error_log(htmlspecialchars($stmt->error), 0);
		$stmt->close();
	
		// check for successful store
		if($result) {
			$stmt = $this->conn->prepare("SELECT * FROM USERS WHERE FACEBOOK_EMAIL = ?");
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
	 */
	public function storeUserWithGoogleEmailnAccessTokennRefreshToken($email, $accessToken, $refreshToken) {
		error_log("accesstoken : " . $accessToken . "refreshToken : " . $refreshToken);
		$stmt = $this->conn->prepare("INSERT INTO USERS(CREATED_AT, GOOGLE_EMAIL, GOOGLE_ACCESS_TOKEN, GOOGLE_REFRESH_TOKEN) VALUES(?, NOW(), ?, ? ,?)");
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
	 * Get user by 카카오이메일
	 */
	public function getKakaoUserByEmail($email) {
		$query = "SELECT * FROM USERS WHERE KAKATOK_EMAIL = ?";
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
	 * Get user by 페북 이메일
	 */
	public function getFacebookUserByEmail($email) {
		$query = "SELECT * FROM USERS WHERE FACEBOOK_EMAIL = ?";
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
	public function isUserExistedWithFacebook($EMAIL) {
		$stmt = $this->conn->prepare("SELECT FACEBOOK_EMAIL from USERS WHERE FACEBOOK_EMAIL = ?");
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
	public function isUserExistedWithKakao($EMAIL) {
		$stmt = $this->conn->prepare("SELECT KAKATOK_EMAIL from USERS WHERE KAKATOK_EMAIL = ?");
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