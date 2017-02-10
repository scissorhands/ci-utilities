<?php
namespace ciutil\controllers;
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends \CI_Controller
{
	private $migrationList;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('migration');
		$this->migrationList = $this->migration->find_migrations();
	}

	public function index()
	{
		$this->status();
	}

	public function run_latest( $redirect = null )
	{
		if ($this->migration->latest() === FALSE)
		{
			show_error($this->migration->error_string());
		} else {
			$list = $this->migrationList;
			$latest = end( (array_keys( $list )) );
			$data = array(
				"version" => date("Y-m-d H:i:s", strtotime($latest)),
				"file" => $list[$latest]
			);
			if( $redirect ){
				redirect('/');
			}
			exit( json_encode( $data ) );
		}
	}

	public function status()
	{
		$current = $this->get_current();
		$keys = array_keys( $this->migrationList );
		$pos = array_search($current, $keys);
		echo json_encode( 
			array(
				"current_version"=> isset($this->migrationList[$current])? [$current=>$this->migrationList[$current]] : [],
				"pending" => array_slice($this->migrationList, $pos+1)
			)
		);
	}

	private function get_current()
	{
		$row = $this->db->select('version')->get("migrations")->row();
		return $row ? $row->version : '0';
	}

	public function run_next()
	{
		$current = $this->get_current();
		$keys = array_keys( $this->migrationList );
		$pos = array_search($current, $keys);
		$next = isset($keys[$pos+1])? $keys[$pos+1] : null;
		if($next){
			$this->run_by_version( $next );
		} else {
			echo json_encode( array("notice" => "migrations are up to date")  );
		}
	}

	public function run_previous()
	{
		$current = $this->get_current();
		$keys = array_keys( $this->migrationList );
		$pos = array_search($current, $keys);
		$this->run_by_version( $keys[$pos-1] );
	}

	public function get_all_versions()
	{
		echo json_encode( $this->migrationList, false );
	}

	public function run_last()
	{
		$keys = array_keys($this->migrationList);
		$last = $keys[count($keys)-2];
		$this->run_by_version( $last );
	}

	public function run_by_version( $version = null )
	{
		if( $version === null ){ exit("version needed"); }
		if( !is_numeric($version) ){ exit("version must be a number"); }
		if ($this->migration->version($version) === FALSE)
		{
			show_error($this->migration->error_string());
		} else {
			if($version == 0){ exit( json_encode( array("version" => 0) ) ); }
			$list = $this->migrationList;
			$data = array(
				"version" => date("Y-m-d H:i:s", strtotime($version)),
				"file" => $list[$version]
			);
			exit( json_encode( $data ) );
		}
	}

	public function get_next_version( $migration_name = "migration_name" )
	{
		$now = date("YmdHis");
		$name = ucfirst($migration_name);
		echo json_encode( array( "new_migration" => $now."_".$name.".php") );
	}

	public function get_next( $migration_name = 'migration_name' )
	{
		$this->get_next_version( $migration_name );
	}
}

/* End of file Migrate.php */
/* Location: ./application/controllers/Migrate.php */
