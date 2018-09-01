<?php 
class Core extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->obj_core = array();
		$this->obj = array();
		$this->prams = array();
		$this->_token_reseller = null;
		$this->_token = null;
		$this->level = 0;
		$this->role = 0;
		$this->is_private_key = null;
		$this->private_key = $this->config->item('private_key');
		$this->load->config('rest');
		$this->api_name = '';
		$this->code = 100;
		$this->msg = null;
	}
	public function _token($p){
		$param = array('username'=> $p->username,'password'=> md5($p->password),'auth'=> md5($p->auth));
		try{
			$this->obj_core = $this->mongo_db->select(array('role','email'))->where($param)->get('ask_users');
			if(!empty($this->obj_core)){ 
				return  array('token'=>handesk_encode(json_encode($this->obj_core[0])) );
			}
		}catch (Exception $e) { 
			return array('token'=> handesk_encode(json_encode($this->obj_core))); 
		}
	}
	public function _api_name(){
		return $this->config->item('rest_key_name');
	}
	public function _action_insert_user($param){
		try{
			$this->obj_core = $this->mongo_db->insert('ask_users',$param);
			$this->_insert_log($param,'ask_users');
			return $this->obj_core;
		}catch (Exception $e) { return $this->obj_core; }
	}
	public function _user_info($user_id){
		try{
			$this->obj_core = $this->mongo_db->where(array('_id' => new \MongoId($user_id)))->get('ask_users');
			if(!empty($this->obj_core)){
				return $this->obj_core[0];
			}
		}catch (Exception $e) { return $this->obj_core; }
	}
	public function _user_create_check($username,$email){
		try{
			$this->obj_core = $this->mongo_db->where_or(array('username'=>$username, 'email'=>$email))->get('ask_users');
			if(!empty($this->obj_core)){
				return getObjectId($this->obj_core);
			}
		}catch (Exception $e) { return $this->obj_core; }
	}
	
	private function _insert_log($params,$collection){
		try{
			$this->prams = array(
				'collect_insert' => $collection,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$params,
			);
			$this->mongo_db->insert('log_insert',$this->prams);
		}catch (Exception $e) {  }
	}
	public function _token_reseller($token){
		try{
			$token = json_decode(handesk_decode($token));
			return getObjectId($token->_id);
		}catch (Exception $e) { return $this->_token_reseller; }
	}
	public function _level_api($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('level'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0])){
				return $this->level = $this->obj_core[0]['level'];
			}
		}catch (Exception $e) { $this->level; }
		return $this->level;
		
	}
	public function _role($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('role'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0]['role'])){
				return $this->role = $this->obj_core[0]['role'];
			}
		}catch (Exception $e) { $this->role; }
		return $this->role;
		
	}
	public function _params($string,$api_key){
		return json_decode(decrypt_obj($string,$api_key,$this->_is_private_key($api_key)));
	}
	public function _result($status,$prams,$api_key){
		
		$this->obj = $this->_msg_response($status);
		$this->obj['result'] = encrypt_obj(json_encode($prams),$api_key,$this->_is_private_key($api_key));
		return $this->obj;
	}
	public function _msg_response($code=null){
		try{
			if(!empty($code)){
				$this->code = (int)$code;
				$this->obj_core = $this->mongo_db->where(array('code'=>$this->code))->get('msg_reponse');
				if(!empty($this->obj_core)){
					if(!empty($this->obj_core[0])){
						if(!empty($this->obj_core[0]['code'])){
							if(!empty($this->obj_core[0]['msg'])){
								$this->msg = $this->obj_core[0]['msg'];
							}
							$this->code = $this->obj_core[0]['code'];
							return array( 'status' => $this->code, 'msg'=> $this->msg);
						}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
					}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
				}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
			}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
		}catch (Exception $e) { 
			return array('status'=>100,'msg'=>'lỗi khong xác định');
		}
		
	}
	public function _is_private_key($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('is_private_key'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0])){
				return $this->is_private_key = $this->obj_core[0]['is_private_key'];
			}
		}catch (Exception $e) { $this->is_private_key; }
		return $this->is_private_key;
		
	}
	
	public function _card_config(){
		try{
			$obj_core = $this->mongo_db->select(array('name','value','card_amount'))->get('card_option');
			if(!empty($obj_core)){
				foreach($obj_core as $v){
					$this->obj_core[] = array(
						'name' => $v['name'],
						'value' => $v['value'],
						'card_amount' => $v['card_amount'],
					);
				}
			}
		}catch (Exception $e) { $this->obj_core; }
		return $this->obj_core;
	}
	public function _isValidDomainName($domain) {
		  return (preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain));
	}
	public function _isValidMx($mx) {
		  return (preg_match('/^(0?[0-9]|[0-5][0-0])$/', $mx));
	}
	public function _isValidEmailName($email) {
		  return (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email));
	}
	///////////////// Zone Validator ///////////////////////////
	
	public function _isValidIpAddressRegex($string){
		return (preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $string));
	}
	public function _isValidIpHostnameRegex($string){
		return (preg_match('/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/', $string));
	}
}

?>