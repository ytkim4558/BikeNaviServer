<?php 
// 파일 로그 남기기
$myfile = fopen('newfile.txt', 'a') or die("Unable to open file!");
$txt = "John Doe\n";
fwrite($myfile, $txt);
$txt = "Jane Doe\n";
fwrite($myfile, $txt);
fclose($myfile);
?>
