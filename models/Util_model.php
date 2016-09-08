<?php
namespace ciutil\models;
defined('BASEPATH') OR exit('No direct script access allowed');

class Util_model extends \CI_Model {

	public function insert_update( $table, $data, $where_keys = array() )
	{
		$id = $this->insert_single( $table, $data );

		foreach ($where_keys as $key => $value) {
			$this->db->where($key, $value );
		}
		$this->db->update($table, $data );
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
}

/* End of file Util_model.php */
/* Location: ./application/models/Util_model.php */