<?php
echo '안녕';
$ch = curl_init("http://item2.gmarket.co.kr/Item/DetailView/Item.aspx?goodscode=824231995");
$fp = fopen("example_homepage.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($fp);