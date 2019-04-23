<?
	$data = json_encode($_POST);
	file_put_contents("acco.txt", $data."\n", FILE_APPEND);
?>