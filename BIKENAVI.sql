-- MySQL dump 10.13  Distrib 5.7.16, for Linux (x86_64)
--
-- Host: localhost    Database: BIKENAVI
-- ------------------------------------------------------
-- Server version	5.7.16-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `POI_TB`
--

DROP TABLE IF EXISTS `POI_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `POI_TB` (
  `POI_NO` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '장소번호',
  `POI_NAME` varchar(100) NOT NULL COMMENT '장소 이름',
  `POI_ADDRESS` varchar(100) DEFAULT NULL COMMENT '장소 주소',
  `POI_LAT_LNG` varchar(100) NOT NULL COMMENT '장소 좌표',
  `CREATED_AT` datetime NOT NULL COMMENT '생성한 시각',
  `UPDATED_AT` datetime NOT NULL COMMENT '사용한 시각',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막에 사용한 시각',
  PRIMARY KEY (`POI_NO`),
  KEY `POI_LAT_LNG` (`POI_LAT_LNG`,`LAST_USED_AT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='장소 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `POI_TB`
--

LOCK TABLES `POI_TB` WRITE;
/*!40000 ALTER TABLE `POI_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `POI_TB` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TRACK_TB`
--

DROP TABLE IF EXISTS `TRACK_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TRACK_TB` (
  `TRACK_NO` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '트랙 번호',
  `START_POI_NO` tinyint(20) NOT NULL COMMENT '출발지 번호 ',
  `DEST_POI_NO` bigint(20) NOT NULL COMMENT '도착지 번호',
  `STOP_POI_NO_ARRAY` text COMMENT '경유지 번호 리스트',
  `CREATED_AT` datetime NOT NULL COMMENT '생성 시각',
  `UPDATED_AT` datetime NOT NULL COMMENT '정보 수정한 시각',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막에 사용한 시각',
  PRIMARY KEY (`TRACK_NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='경로 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TRACK_TB`
--

LOCK TABLES `TRACK_TB` WRITE;
/*!40000 ALTER TABLE `TRACK_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `TRACK_TB` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USERS` (
  `USER_NO` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '회원 넘버',
  `USER_EMAIL` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '자체회원 이메일',
  `GOOGLE_EMAIL` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '구글로 가입한 사람 이메일',
  `KAKAO_ID` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '카카오톡id, long타입 변수이나 string으로 변환해서 넘겨옴',
  `KAKAO_NICK_NAME` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '카카오톡으로 가입한 사람 닉네임 ',
  `FACEBOOK_ID_NUM` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '페이스북 아이디 번호(숫자)',
  `FACEBOOK_USER_NAME` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '페이스북으로 가입한 사람 이름 ',
  `USER_PW` varchar(80) CHARACTER SET utf8 DEFAULT NULL COMMENT '자체회원가입 비밀번호',
  `SALT` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'base64암호화에 보안을 강화하기위해 첨부',
  `CREATED_AT` datetime NOT NULL COMMENT '생성 시각 ',
  `UPDATED_AT` datetime DEFAULT NULL COMMENT '정보 수정 시각',
  `LAST_USED_AT` datetime DEFAULT NULL COMMENT '마지막으로 사용한 시각',
  `GOOGLE_ACCESS_TOKEN` text CHARACTER SET utf8 COMMENT '구글 액세스 토큰',
  `GOOGLE_REFRESH_TOKEN` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '구글 refresh 토큰 ',
  PRIMARY KEY (`USER_NO`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COMMENT='유저정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USERS`
--

LOCK TABLES `USERS` WRITE;
/*!40000 ALTER TABLE `USERS` DISABLE KEYS */;
INSERT INTO `USERS` VALUES (3,'nova@nova.com',NULL,NULL,NULL,NULL,NULL,'O/YmEJ2r2zlWvhJOgcRH2nsnWPIwN2FkNzBjMmYy','07ad70c2f2','2016-09-29 00:00:00',NULL,NULL,NULL,NULL),(5,'ytkim4558@naver.com',NULL,NULL,NULL,NULL,NULL,'ziHhRmTFK1Xx6zuYWnpFI3sYzIBhMjM3MTFkYTVk','a23711da5d','2016-09-30 17:44:46',NULL,NULL,NULL,NULL),(9,'nova5@nova.com',NULL,NULL,NULL,NULL,NULL,'q46wVdogmyeq2eGO6WYePtfX8Z45MDAyMDVkMjRm','900205d24f','2016-10-02 13:14:49',NULL,NULL,NULL,NULL),(10,'nova6@nova.com',NULL,NULL,NULL,NULL,NULL,'eLNbbbjCFzS8tUJInGXTs0cDrMQyY2Q5MGNkYTJi','2cd90cda2b','2016-10-02 13:18:45',NULL,NULL,NULL,NULL),(14,'nova9@nova.com',NULL,NULL,NULL,NULL,NULL,'I4DUd+JLLkgaRSCnmhTgWgYQgF84OGJlNDQ1YTJh','88be445a2a','2016-10-02 14:45:28',NULL,NULL,NULL,NULL),(16,'nova11@nova.com',NULL,NULL,NULL,NULL,NULL,'8QrxSFC+A3wmRuGduKjYM3qCuRk5NDI0Y2I1Y2Nj','9424cb5ccc','2016-10-06 16:44:38',NULL,NULL,NULL,NULL),(22,'nova43@nova.com',NULL,NULL,NULL,NULL,NULL,'IH4eLIucvJFONvw4T+9qb1SLnjQwZmMzZTMyMGMz','0fc3e320c3','2016-10-08 13:04:11',NULL,NULL,NULL,NULL),(23,'nova71@naver.com',NULL,NULL,NULL,NULL,NULL,'Wi+U5/VQqjCW5Ce/yQc2xNkM8WxjZWY2YzQ3MjNj','cef6c4723c','2016-10-08 17:37:46',NULL,NULL,NULL,NULL),(24,'nova71@nova.com',NULL,NULL,NULL,NULL,NULL,'t2AkYgXAXm2hHf5QxB3YYriz5mg1OWU0NGFkMmYx','59e44ad2f1','2016-10-09 15:52:54',NULL,NULL,NULL,NULL),(58,NULL,NULL,'292514480','용',NULL,NULL,NULL,NULL,'2016-10-13 21:56:30','2016-10-23 15:15:15',NULL,NULL,NULL),(61,NULL,'ytkim4558@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-13 23:10:09',NULL,NULL,'{\"access_token\":\"ya29.Ci97A3uiYbDU0Jh6puCOzeTKW2Jyv5JZUJcN5UYf_lbfiSQovo82NWYWENphmCauWw\",\"token_type\":\"Bearer\",\"expires_in\":3600,\"refresh_token\":\"1\\/ex7sjIrQVIO4NMVQgNp-rSFgOgufLVlhNZXvvzXzU-E\",\"id_token\":\"eyJhbGciOiJSUzI1NiIsImtpZCI6Ijc2ZWM0OWRkMjllMjc5ZjdjNDI1ODMzMTMwOTYzOTdiYTMwM2U2MzgifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhdF9oYXNoIjoienFQbURQdTdsWS1yd29Cck9ILUdnUSIsImF1ZCI6IjM1MDgxNzcyNzQ1Ni1ua2ZnbDZta2p2czZsbWxoZmw3c21sZWdmdGh1cDQzMy5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjExMzcyODQzNzgwODI4NjI5MzU4MiIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJhenAiOiIzNTA4MTc3Mjc0NTYtbmtmZ2w2bWtqdnM2bG1saGZsN3NtbGVnZnRodXA0MzMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJlbWFpbCI6Inl0a2ltNDU1OEBnbWFpbC5jb20iLCJpYXQiOjE0NzYzNjc4MDksImV4cCI6MTQ3NjM3MTQwOSwibmFtZSI6IllvbmdUYWsgS2ltIiwicGljdHVyZSI6Imh0dHBzOi8vbGg2Lmdvb2dsZXVzZXJjb250ZW50LmNvbS8tNVpJM0EyeFBwMXMvQUFBQUFBQUFBQUkvQUFBQUFBQUFDdjgvWEVkOGw4MmJlbzQvczk2LWMvcGhvdG8uanBnIiwiZ2l2ZW5fbmFtZSI6IllvbmdUYWsiLCJmYW1pbHlfbmFtZSI6IktpbSIsImxvY2FsZSI6ImtvIn0.Do2Y2tK0cAteSoOHvuVDiBfMpYQSUd9oGArwx3yrxaUGWW8PgmOUX0tmnNFeocdGtkdDG1ChLHbSFZyNIbd5S49LsOIYPsM451o_JKfnjPtyGCN2CmSzeYSnYpziW7qdbIlNoNrM_EyGIhJHIcTaUlHY6uJxRNI7C7b5gAl4wDvYmROkuvEVu1wJVKw3GTAMNVEoDwWoVy0NpQR7xDVGMzf6HlCJcbpe1lVd5i1S3miEsikOH-l1jkn66GHIIRrFb24xNVHbUc5Zu_YxaRPpMMkAruKmrKXDuj_s8sgc0a6ws4kZ-7KSSMo2y0XYIi__Ed6JKD1Gtk73Fjxnx6TcIg\",\"created\":1476367809}','\"1\\/ex7sjIrQVIO4NMVQgNp-rSFgOgufLVlhNZXvvzXzU-E\"'),(66,NULL,NULL,NULL,NULL,'1601113260183676','김용탁',NULL,NULL,'2016-10-14 17:17:26',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `USERS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USER_BOOKMARK_POI_TB`
--

DROP TABLE IF EXISTS `USER_BOOKMARK_POI_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USER_BOOKMARK_POI_TB` (
  `NO` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '번호 ',
  `USER_NO` bigint(20) NOT NULL COMMENT '유저번호 ',
  `POI_NO` bigint(20) NOT NULL COMMENT '장소번호 ',
  `CREATED_AT` datetime NOT NULL COMMENT '생성 시각 ',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막으로 사용한 시각 ',
  PRIMARY KEY (`NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USER_BOOKMARK_POI_TB`
--

LOCK TABLES `USER_BOOKMARK_POI_TB` WRITE;
/*!40000 ALTER TABLE `USER_BOOKMARK_POI_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `USER_BOOKMARK_POI_TB` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USER_BOOKMARK_TRACK_TB`
--

DROP TABLE IF EXISTS `USER_BOOKMARK_TRACK_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USER_BOOKMARK_TRACK_TB` (
  `NO` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '번호 ',
  `USER_NO` bigint(20) NOT NULL COMMENT '유저번호 ',
  `TRACK_NO` bigint(20) NOT NULL COMMENT '경로 번호 ',
  `CREATED_AT` datetime NOT NULL COMMENT '생성 시각 ',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막으로 사용한 시각 ',
  PRIMARY KEY (`NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USER_BOOKMARK_TRACK_TB`
--

LOCK TABLES `USER_BOOKMARK_TRACK_TB` WRITE;
/*!40000 ALTER TABLE `USER_BOOKMARK_TRACK_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `USER_BOOKMARK_TRACK_TB` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USER_POI_TB`
--

DROP TABLE IF EXISTS `USER_POI_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USER_POI_TB` (
  `NO` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '번호',
  `USER_NO` bigint(20) NOT NULL COMMENT '유저번호',
  `POI_NO` bigint(20) NOT NULL COMMENT '장소 번호',
  `CREATED_AT` datetime NOT NULL COMMENT '생성한 시각 ',
  `UPDATED_AT` datetime NOT NULL COMMENT '정보를 수정한 시각',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막으로 사용한 시각',
  PRIMARY KEY (`NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='유저가 검색한 장소 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USER_POI_TB`
--

LOCK TABLES `USER_POI_TB` WRITE;
/*!40000 ALTER TABLE `USER_POI_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `USER_POI_TB` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USER_TRACK_TB`
--

DROP TABLE IF EXISTS `USER_TRACK_TB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USER_TRACK_TB` (
  `NO` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '번호',
  `USER_NO` bigint(20) unsigned NOT NULL COMMENT '유저 번호',
  `TRACK_NO` bigint(20) unsigned NOT NULL COMMENT '경로 번호',
  `CREATED_AT` datetime NOT NULL COMMENT '생성한 시각',
  `UPDATED_AT` datetime NOT NULL COMMENT '정보를 수정한 시각',
  `LAST_USED_AT` datetime NOT NULL COMMENT '마지막으로 사용한 시각',
  PRIMARY KEY (`NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='유저가 검색한 경로 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USER_TRACK_TB`
--

LOCK TABLES `USER_TRACK_TB` WRITE;
/*!40000 ALTER TABLE `USER_TRACK_TB` DISABLE KEYS */;
/*!40000 ALTER TABLE `USER_TRACK_TB` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-31 15:54:29
