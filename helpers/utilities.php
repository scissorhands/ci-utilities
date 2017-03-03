<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function dump( $var, $text = false )
{
	if( $text ){
		echo "<pre>";
			print_r($var);
		echo "</pre>";
		exit;
	} else{
		header('Content-Type: application/json');
		echo json_encode($var);
        exit;
	}
}

function inspect( $var )
{
    var_dump($var);
    exit;
}

function generate_uri_hash()
{
	return md5(rand().microtime());
}

function time_format(
    $input_seconds = 0,
    $format_style = 'hrs_mins_secs',
    $lang_time = [
        'sec'=>'seg.',
        'min'=>'min.',
        'hr'=>'hrs.',
        'year'=>'años'
    ]){
    $days = "";$hours = "";$minutes = "";$seconds = "";
    $days=floor($input_seconds / 86400);
    $remainder=floor($input_seconds % 86400);
    $hours=floor($remainder / 3600);
    $remainder=floor($remainder % 3600);
    $minutes=floor($remainder / 60);
    $seconds=floor($remainder % 60);

    if ($days > 0) $days = "$days";
    if ($hours > 0) $hours = "$hours";
    if ($minutes > 0) $minutes = "$minutes";
    if ($seconds > 0) $seconds = "$seconds";

    if ($hours < 10) $hours = "0$hours";
    if ($minutes < 10) $minutes = "0$minutes";
    if ($seconds < 10) $seconds = "0$seconds";
    switch($format_style) {
        case 'days_hrs':
            if($input_seconds>(365*24*60*60)) {
                return number_format( ($days / 365), 2, '.', ',' ) . " " . $lang_time['year'];
            } else {
                return ($days*24)+$hours . ':' . $minutes . ':' . $seconds;
            }
            break;

        case 'mins_secs':
            return $minutes . ':' . $seconds;
            break;

        case 'hrs_mins_secs':
            return $hours . ':' . $minutes . ':' . $seconds;
            break;

        case 'long_mins_secs':
            return $minutes . ' ' . $lang_time['min'] . ', ' . $seconds . ' '
            . $lang_time['sec'];
            break;

        case 'long_hrs_mins_secs':
            return $hours . ' ' . $lang_time['hr'] . ', ' . $minutes . ' '
            . $lang_time['min'] . ', ' . $seconds . ' ' . $lang_time['sec'];
            break;
    }
    return $days.$hours;
}

function get_dates_query( $start, $end, $field_name = 'custom_date' ){
    $query = "SELECT '{$start}' AS {$field_name} \n";
    $current = $start;
    while( $current < $end){
        $current = date('Y-m-d', strtotime( $current.' +1 day') );
        $query .= "UNION SELECT '". $current ."' \n";
    }
    return $query;
}

function cleanString($text) {
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', 
        '/[’‘‹›‚]/u'    =>   ' ', 
        '/[“”«»„]/u'    =>   ' ', 
        '/ /'           =>   ' ', 
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function parse_obj_array_to_ids_array($obj_array, $id_name = "id")
{
    $arr = array();
    foreach ($obj_array as $row) {
        $arr[] = $row->$id_name;
    }
    return $arr;
}

function get_date_diff( $date1="2015-01-01", $date2="2015-01-15" )
{
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval;
}

function valid_future_date( $date )
{
    $diff = get_date_diff(date("Y-m-d"), $date );
    if( (int)$diff->format("%R%a") < 0){
        return false;
    }
    return true;
}

function match_date($date) {
    if( preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date) ){
        return true;
    }
    return false;
}

function valid_daterange( $date_from, $date_to ){
    if( get_date_diff( $date_from, $date_to )->format("%R%a") < 0 ){
        return false;
    } else {
        return true;
    }
}

function get_dsn( $host, $user, $pass, $database )
{
    return 'mysqli://'.$user.':'.$pass.'@'.$host.'/'.$database;
}

function obj_array_to_indexed_array($obj_array, $index_field, $value_field, $value_type = '')
{
    $arr = array();
    foreach ($obj_array as $row) {
		switch ($value_type) {
			case 'bool':
    			$arr[$row->$index_field] = (bool) $row->$value_field;
				break;
			case 'int':
    			$arr[$row->$index_field] = (int) $row->$value_field;
				break;
			case 'float':
    			$arr[$row->$index_field] = (float) $row->$value_field;
				break;
			case 'double':
    			$arr[$row->$index_field] = (double) $row->$value_field;
				break;
			default:
    			$arr[$row->$index_field] = $row->$value_field;
				break;
		}
    }
    return $arr;
}

function echolor( $text = '', $color = 'white'){
    $color_code = [
        'black'=> '30',
        'blue'=> '34',
        'green'=> '32',
        'cyan'=> '36',
        'red'=> '31',
        'purple'=> '35',
        'brown'=> '33',
        'light_gray'=> '1;37', 
        'dark_gray'=> '1;30',
        'light_blue'=> '1;34',
        'light_green'=> '1;32',
        'light_cyan'=> '1;36',
        'light_red'=> '1;31',
        'light_purple'=> '35',
        'yellow'=> '33',
        'white'=> '37'
    ];
    echo isset($color_code[$color])? "\033[".$color_code[$color]."m$text\033[0m \n" : $text."\n";
}

function form_errors(){
    return validation_errors('<div class="alert alert-danger">', '</div>');
}

function api_response($data = null, $success = true, $response_code = 200 )
{
    http_response_code($response_code);
    header('access-control-allow-origin: *');
    if( $success ){
        dump( [
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        dump( [
            'status' => 'error',
            'message' => $data
        ]);
    }
}

function api_allowed_options( $options = 'GET,POST' ){
    http_response_code(204);
    header('access-control-allow-headers:authorization');
    header('access-control-allow-methods:'.$options);
    header('access-control-allow-origin:*');
    exit();
}

function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_numeric($key) ){
            $key = 'item'.$key;
        }
        if( is_array($value) ) {
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

function base64ToImage($base64_string, $output_file) {
    $file = fopen($output_file, "wb");
    fwrite($file, base64_decode( $base64_string ));
    fclose($file);
    return $output_file;
}

function is_cli_request()
{
    return php_sapi_name() == 'cli' ? true : false;
}

function cron_response($var)
{
    if(is_cli_request()){
        print_r( $var );
        exit;
    } else {
        dump( $var, false );
    }
}