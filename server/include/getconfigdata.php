<?php 

function gd($file_name) {
	$file = file_get_contents(__DIR__ . "/../" . $file_name);
	$json = json_decode($file, true);
	return $json;
}

function get_data_from_config($parameter = NULL) {
	$json = gd("config.json");
	if (is_null($parameter))
		return $json;
	return $json[$parameter];
}

function get_data_from_form_config() {
	$form_config = get_data_from_config("form_config_file");
	$json = gd("$form_config");
	if (is_null($parameter))
		return $json;
	return $json[$parameter];
}

 ?>