<?php
    // 참고 사이트 : http://www.jacobward.co.uk/web-scraping-with-php-curl-part-1/
    // 기본 cURL 함수를 정의
//    header('Content-type: text/html; charset=UTF-8');
    function curl($url) {
        $ch = curl_init();  // cURL 초기화
        curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
        curl_close($ch);    // Closing cURL
        return $data;   // Returning the data from the function
    }

    echo '안녕';
    $gmarket_page = curl("http://item2.gmarket.co.kr/Item/DetailView/Item.aspx?goodscode=824231995");
    $gmarket_page_t = iconv("EUC-KR", "UTF-8", $gmarket_page);
    echo $gmarket_page_t;