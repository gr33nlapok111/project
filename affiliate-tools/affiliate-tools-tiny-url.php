<?php
function fetchTinyUrl($url,$limit = 5) {
	$ch = curl_init();
	$timeout = 1;
	curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url='.$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	if($data != ''){
		return ''.$data.'';
	}else{
		if ($limit < 1){
			return 'requestTIMEOUT';
		}else{
			return fetchTinyUrl($url,($limit-1));
		}
	}
}
?>