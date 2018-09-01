<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Core.php';
class Site extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>100,'result'=>null);
		$this->obj = array();
		$this->apps = new core;
		$this->_api_key = $this->_api_key();
		$this->encryption_key = $this->config->item('encryption_key');
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
	}
	
	public function info_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1){
					$this->obj = $this->mongo_db->get("site");
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> encrypt_obj(json_encode($this->obj),$this->_api_key,$this->_is_private_key)); }
				}
			}
		}
		$this->response($this->r);
	}
	
	
}


?>