<?php 
class Core extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->obj_core = $this->_msg_response(2000);
		$this->confirm = array();
		$this->obj = array();
		$this->limits_obj = array();
		$this->prams = array();
		$this->_token_reseller = null;
		$this->_token = null;
		$this->transfer_p = 0;
		$this->level = 0;
		$this->role = 0;
		$this->is_private_key = null;
		$this->private_key = $this->config->item('private_key');
		$this->load->config('rest');
		$this->api_name = '';
		$this->code = 100;
		$this->balancer = 0;
		$this->msg = null;
	}
	public function _token($p){
		$param = array('username'=> $p->username,'password'=> md5($p->password),'auth'=> md5($p->auth));
		try{
			$this->obj_core = $this->mongo_db->select(array('role','email'))->where($param)->get('ask_users');
			if(!empty($this->obj_core)){ 
				return  array('token'=>handesk_encode(json_encode($this->obj_core[0])) );
			}else{  return $this->_msg_response(2000); }
		}catch (Exception $e) {  return $this->_msg_response(2000);}
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
	public function _balancer_users($user_id,$token){
	
		$reseller = $this->_token_reseller($token);
		try{
			$this->balancer = $this->mongo_db->where(array('_id' => new \MongoId($user_id),'reseller'=>$reseller))->get('ask_users');
			if(!empty($this->balancer)){
				if(isset($this->balancer[0]["balancer"])){
					return $this->balancer[0]["balancer"];
				}else{ return $this->_msg_response(2000); }
			}else{  return $this->_msg_response(2000); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_login($p){
		
		try{
			$reseller = (string)$this->_token_reseller($p->token);
			$this->obj_core = $this->mongo_db->where(array('username'=>$p->username,'password'=>md5($p->password),'reseller'=>$reseller))->get('ask_users');
			if(!empty($this->obj_core[0]['_id'])){
					return $this->obj_core[0]['_id'];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_bank($p){
	
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
				$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				if(!empty(	$this->obj_core)){
					$this->obj_core = $this->mongo_db->where(array( 'client_id' => $p->client_id,'reseller'=> $reseller))->get('ask_bank');
					if(!empty($this->obj_core)){
							$info = array();
							foreach($this->obj_core as $v){
									$info[] = array(
										'bank_id' => getObjectId($v['_id']),
										'bank_name' => $v['bank_name'],
										'account_holders' => $v['account_holders'],
										'bank_account' => $v['bank_account'],
										'branch_bank' => $v['branch_bank'],
										'provinces_bank' => $v['provinces_bank'],
										'reseller' => $v['reseller'],
										'client_id' => $v['client_id'],
									);
							}
							return $info;
					}else{ return $this->_msg_response(1002); }
				}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_bank_del($p){
		
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
				$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				if(!empty(	$this->obj_core)){
					$this->obj_core = $this->mongo_db->where(array( '_id' => new \MongoId($p->bank_id),'client_id' => $p->client_id,'reseller'=> $reseller))->get('ask_bank');
					if(!empty($this->obj_core)){
							$del = $this->mongo_db->where(array('_id' => new \MongoId($p->bank_id),'client_id'=>$p->client_id,'reseller'=> $reseller,))->delete('ask_bank');
							if(!empty($del)){
								return $this->_msg_response(2016);
							}else{ return $this->_msg_response(2014); }
					}else{ return $this->_msg_response(1002); }
				}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_bank_add($p){
		
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'auth'=>md5($p->auth),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$array_bank = array( 'client_id' => $p->client_id,'bank_account' => $p->bank_account,'reseller'=> $reseller,);
				$this->obj_core = $this->mongo_db->where($array_bank)->get('ask_bank');
				if(empty(	$this->obj_core)){
						$this->obj = array();
						if(!empty($p->client_id)){ $this->obj['client_id'] = $p->client_id; }
						if(!empty($p->bank_id)){ $this->obj['bank_id'] = $p->bank_id; }
						if(!empty($p->bank_name)){ $this->obj['bank_name'] = $p->bank_name; }
						if(!empty($p->bank_option)){ $this->obj['bank_option'] = $p->bank_option; }
						if(!empty($p->account_holders)){ $this->obj['account_holders'] = $p->account_holders; }
						if(!empty($p->bank_account)){ $this->obj['bank_account'] = $p->bank_account; }
						if(!empty($p->branch_bank)){ $this->obj['branch_bank'] = $p->branch_bank; }
						if(!empty($p->provinces_bank)){ $this->obj['provinces_bank'] = $p->provinces_bank; }
						if(!empty($reseller)){ $this->obj['reseller'] = $reseller; }
						$this->obj_core = $this->mongo_db->insert('ask_bank',$this->obj);
						if(!empty($this->obj_core)){
							return array('bank_id'=> getObjectId($this->obj_core));
						}else{  return $this->_msg_response(100); }
				}else{  return $this->_msg_response(2015); }
			}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_change_password($p){
		$reseller = (string)$this->_token_reseller($p->token);
		
		$check_array = array('_id' => new \MongoId($p->client_id),'password'=>md5($p->password_old),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				
			if(!empty($this->obj_core)){
				
				if(!empty($p->password_new)){ 
					$this->obj['password'] = md5($p->password_new); 
				}
				$this->obj['date_update'] = date("Y-m-d H:i:s",time());	
				$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'password'=> md5($p->password_old),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
				if($update==true){
					return  $this->_msg_response(1000);
				}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}

	public function _users_change_auth($p){
		$reseller = (string)$this->_token_reseller($p->token);
		
		$check_array = array('_id' => new \MongoId($p->client_id),'auth'=>md5($p->password_old),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				
			if(!empty($this->obj_core)){
				
				if(!empty($p->password_new)){ 
					$this->obj['auth'] = md5($p->password_new); 
				}
				$this->obj['date_update'] = date("Y-m-d H:i:s",time());	
				$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'auth'=> md5($p->password_old),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
				if($update==true){
					return  $this->_msg_response(1000);
				}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_update($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
					if(!empty($p->email)){ $this->obj['email'] = $p->email; }
					if(!empty($p->full_name)){ $this->obj['full_name'] = $p->full_name; }
					if(!empty($p->phone)){ $this->obj['phone'] = $p->phone; }
					if(!empty($p->address)){ $this->obj['address'] = $p->address; }
					if(!empty($p->city)){ $this->obj['city'] = $p->city; }
					if(!empty($p->country)){ $this->obj['country'] = $p->country; }
					if(!empty($p->birthday)){ $this->obj['birthday'] = date("d/m/Y",strtotime($p->birthday)); }
					if(!empty($p->auth)){ $this->obj['auth'] = md5($p->auth); }
					if(!empty($p->password)){ $this->obj['password'] = md5($p->password); }
					$this->obj['reseller'] = $reseller;
					$this->obj['date_create'] = date("Y-m-d H:i:s",time());
					$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
					if($update==true){
						return $update;
					}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_developer($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$this->obj_core = $this->mongo_db->where(array('users'=>$p->client_id,))->get('api_keys');
				if(!empty($this->obj_core)){
					foreach($this->obj_core as $k){
						$level = $k['level'];
						$level_p = null;
						if($level==1){
							$level = "Not Active Limit Client";
						}else if($level==2){
							$level = "Active Supper";
						}else if($level==3){
							$level = "Active Limit Resller";
						}
						$this->obj_core['developer'] = array(
								'level'=> $level,
								'date_created'=> $k['date_created'],
								'merchant_id'=> $k['key'],
								'secret_key'=> $k['is_private_key'],
						);
					}
					return $this->obj_core['developer'];
				}else{  return $this->_msg_response(1002); }
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_developer_create($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$this->obj_core = $this->mongo_db->where(array('users'=>$p->client_id,'reseller'=> $reseller,))->get('api_keys');
				if(empty($this->obj_core)){
						if(!empty($p->client_id)){ $this->obj['users'] = $p->client_id; }	
						if(!empty($p->website)){ $this->obj['website'] = $p->website; }	
						$this->obj['reseller'] = $reseller;
						$hash = core_encrypt($this->private_key .'-'.time().'-'.$reseller.'-'.$p->client_id);
						$key = md5(sha1($hash));
						$this->obj['key'] = $key;
						$this->obj['is_private_key'] = $hash;
						$this->obj['ignore_limits'] = true;
						$this->obj['level'] = 1;
						$this->obj['role'] = 4;
						$this->obj['date_created'] = date("Y-m-d H:i:s",time());
						$this->obj['time_created'] = time();
						$this->obj['ip_addresses'] = $this->input->ip_address();
						$this->limits_obj['api_key'] = $key;
						$this->limits_obj['date_created'] = date("Y-m-d H:i:s",time());
						$this->limits_obj['time_created'] = time();
						$this->limits_obj['count'] = 100000;
						$this->limits_obj['uri'] = 'api/check';
						$this->limits_obj['hour_started'] = time();
						$this->obj_core = $this->mongo_db->insert('api_keys',$this->obj);
						$this->mongo_db->insert('api_limits',$this->limits_obj);
						return $this->obj_core;
				}else{  return $this->_msg_response(2017); }
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_info($user_id){
		try{
			$this->obj_core = $this->mongo_db->where(array('_id' => new \MongoId($user_id)))->get('ask_users');
			if(!empty($this->obj_core)){
				return $this->obj_core[0];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_create_check($username,$email){
		try{
			$this->obj_core = $this->mongo_db->where_or(array('username'=>$username, 'email'=>$email))->get('ask_users');
			if(!empty($this->obj_core)){
				return getObjectId($this->obj_core);
			}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _logs_user_api($param,$action){
		try{
			$this->prams = array(
				'action' => $action,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=> $param,
			);
			$this->mongo_db->insert('logs_user_api',$this->prams);
		}catch (Exception $e) {  }
	}
	public function _transfer_plus($balancer,$client_id,$param){
		try{
			$update = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->set(array('balancer'=>$balancer))->update('ask_users');
			$status = false;
			if($update){ $status = true; }else{ $status = false; }
			$this->confirm = $param;
			$this->confirm['status'] = $status;
			$this->confirm['action'] = 'plus';
			$this->confirm['date_update_transfer'] = date("Y-m-d H:i:s A");
			$this->confirm['time_update_transfer'] = time();
			$w =  $this->_transfer_log($this->confirm);
			if($w==true){
				return true;
			}else{
				return false;
			}
		}catch (Exception $e) {  
			return false;
		}
	}
	public function _transfer_minus($balancer,$client_id,$param){
		try{
		$update = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->set(array('balancer'=>$balancer))->update('ask_users');
		$status = false;
			if($update){ $status = true; }else{ $status = false; }
		$this->confirm = $param;
		$this->confirm['status'] = $status;
		$this->confirm['action'] = 'minus';
		$this->confirm['date_update_transfer'] = date("Y-m-d H:i:s A");
		$this->confirm['time_update_transfer'] = time();
		$w = $this->_transfer_log($this->confirm);
		if($w==true){
			return true;
		}else{
			return false;
		}
		}catch (Exception $e) {  
			return false;
		}
	}
	private function _transfer_log($param){
		try{
			return $this->mongo_db->insert('transfer_log',$param);
		}catch (Exception $e) { 
			return false;
		}
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
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }	
	}
	public function _role($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('role'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0]['role'])){
				return $this->obj_core[0]['role'];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _params($string,$api_key){
		return json_decode(decrypt_obj($string,$api_key,$this->_is_private_key($api_key)));
	}
	public function _result($status,$prams,$api_key){
		$this->obj  = $this->_msg_response($status);
		$this->obj['result'] = encrypt_obj(json_encode($prams),$api_key,$this->_is_private_key($api_key));
		return $this->obj;
	}
	public function _msg_response($code=null){
		try{
			if(!empty($code)){
				$this->code = (int)$code;
				$k = $this->mongo_db->where(array('code'=>$this->code))->get('msg_reponse');
				if(!empty($k)){
					$x = array();
					foreach($k as $v){
						$x['status'] = $v['code'];$x['msg'] = $v['msg'];}
					return $x;
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
	public function _transfer_fee(){
			try{
			$obj_core = $this->mongo_db->select(array('transfer'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['transfer'];
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
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