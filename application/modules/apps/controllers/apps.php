<?php
require APPPATH . '/libraries/REST_Controller.php';
class Apps extends REST_Controller {
	function __construct(){
		parent::__construct();
		// $this->load->model('reset_model','ResetMD');
		$this->r = array('status'=>true,'result'=>null);
		$this->obj = array();
	}
	public function index_get(){
		
	}
	
	
}


?>