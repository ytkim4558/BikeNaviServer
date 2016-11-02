<?php 
$file = 'logfile.log';
// 현재 존재하는 컨텐츠를 열기
$current = file_get_contents($file);
// 뒤에 붙이기
if(isset($_POST['hi'])) {
	$current .= $_POST['hi'];
} else {
	$current .= "안녕?";
}
// error_log("이메일 인증입니다.", 1, "ytkim4558@naver.com", "From: webmaster@example.com");
// 파일 쓰기
file_put_contents($file, $current);
echo $current;
?>