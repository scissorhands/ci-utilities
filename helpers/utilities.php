<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function dump( $var, $text = false )
{
	if( $text ){
		echo "<pre>";
			print_r($var);
		echo "</pre>";
		exit();
	} else{
		header('Content-Type: application/json');
		exit( json_encode( $var ) );
	}
}

function dump_r( $var, $text = false )
{
	if( !$text ){
		echo "<div class='hiddenDebug' style='display: none'>";
			print_r($var);
		echo "</div>";

	} else{
		echo "<div>";
		echo json_encode( $var );
		echo "</div>";
	}
}

function generate_uri_hash()
{
	return md5(rand().microtime());
}

function escapeString($string) {
    $dict = array(
        "I'm"      => "I am",
        "thier"    => "their",
    );
    return strtolower(
        preg_replace(
          array( '#[\\s-]+#', '#[^A-Za-z0-9\. -]+#' ),
          array( '-', '' ),
          cleanString(
              str_replace(
                  array_keys($dict),
                  array_values($dict),
                  urldecode($string)
              )
          )
        )
    );
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
