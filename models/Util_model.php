<?php
namespace ciutil\models;
defined('BASEPATH') OR exit('No direct script access allowed');

class Util_model extends \CI_Model {

	public function insert_update( $table, $data, $where_keys = array() )
	{
		$id = $this->generic_insert( $table, $data );

		foreach ($where_keys as $key => $value) {
			$this->db->where($key, $value );
		}
		$this->db->update($table, $data );
	}

	public function insert_on_duplicate_update( $table, $data, $update_fields = [] )
	{
		$update_str = [];
		foreach ($update_fields as $field) {
			$val = $this->db->escape($data[$field]);
			$update_str[] = "{$field} = {$val}";
		}
		$sql = $this->db->insert_string($table, $data) . "\n ON DUPLICATE KEY UPDATE ".implode(', ', $update_str);
		$this->db->query($sql);
	}

	public function insert_update_batch($table, $items, $where_keys = array() )
	{
		foreach ($items as $item) {
			$where = array();
			foreach ($where_keys as $where_key ) {
				$where[$where_key] = $item[$where_key];
			}
			$this->insert_update( $table, $item, $where );
		}
	}

	public function generic_update( $table, $where_keys, $data )
	{
		foreach ($where_keys as $key => $value) {
			$this->db->where($key, $value );
		}
		$this->db->update($table, $data );
	}

	public function generic_insert( $table, $data )
	{
		$string = $this->db->insert_string($table, $data);
		$query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $string);
		$this->db->query($query);
		return $this->db->insert_id();
	}

	public function insert_single( $table, $data )
	{
		return $this->generic_insert( $table, $data );
	}

	public function generic_delete( $table, $where_clause = array() )
	{
		$this->db->where($where_clause)->delete($table);
	}

	public function generic_insert_batch( $table, $data )
	{
		$this->db->insert_batch($table, $data);
	}

	public function generic_delete_in( $table, $where_field, $where_values )
	{
		$this->db->where_in($where_field, $where_values)->delete($table);
	}

	public function empty_table( $table )
	{
		$this->db->truncate($table);
	}
	
	public function get( $table, $where_clause, $all_rows = false )
	{
		$query = $this->db->where($where_clause)->get($table);
		return $query->result()? ( $all_rows? $query->result() : $query->row() ) : null;
	}

	public function table_exists( $table_name )
	{
		return $this->db->from("INFORMATION_SCHEMA.tables")
		->where('TABLE_NAME', $table_name)
		->where('TABLE_SCHEMA', $this->db->database)->get()->result();
	}
}

/* End of file Util_model.php */
/* Location: ./application/models/Util_model.php */