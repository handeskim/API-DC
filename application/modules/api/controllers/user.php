<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class User extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->apps = new core;
		$this->params = null;
		$this->resller = null;
		$this->param = array();
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->obj = array( 'command'=> array(
				'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
				'secret_key' => '(string) secret key //YOUR API CREATED',
				'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', )
		);
	}
	public function info_get(){
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									$this->params = $this->apps->_user_info($p->client_id);
									if(!empty($this->params)){
											$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
									}else{ $this->r = $this->apps->_msg_response(2012);}	
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function create_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key());
							if(!empty($p->email) || !empty($p->username) || !empty($p->password) || !empty($p->auth) || !empty($p->phone) || !empty($p->full_name) ){
								if(email_regex($p->email)==true){
									if(strlen($p->password) > 5 || strlen($p->password) < 32){
										if(strlen($p->auth) > 5 || strlen($p->auth) < 32){
											if(!empty($p->address)){ $address = $p->address;}else{ $address = null; }
											if(!empty($p->city)){ $city = $p->city;}else{ $city = null; }
											if(!empty($p->country)){ $country = $p->country;}else{ $country = null; }
											if(!empty($p->birthday)){ $birthday = $p->birthday; }else{ $birthday = null; }
											if(!empty($p->avatar)){ $avatar = $p->avatar; }else{ $avatar = null; }
											if(!empty($p->token)){
												$this->resller = $this->apps->_token_reseller($p->token);
												if(!empty($this->resller)){
													$check = $this->apps->_user_create_check($p->username,$p->email);
													if(empty($check)){
														$this->param = array(
															'email'=> $p->email,
															'username'=> $p->username,
															'password'=> md5($p->password),
															'auth'=> md5($p->auth),
															'phone'=> $p->phone,
															'full_name'=> $p->full_name,
															'address'=> $city,
															'city'=> $city,
															'country'=> $country,
															'balancer'=> 0,
															'birthday'=> $birthday,
															'date_create'=>date('Y-m-d H:i:s'),
															'time_crate'=>time(),
															'avatar'=> $avatar,
															'role'=> 4,
															'reseller'=> $this->resller,
														);
														$this->params = $this->apps->_action_insert_user($this->param);
														if(!empty($this->params)){
															$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
														}else{ $this->r = $this->apps->_msg_response(199);}
													}else{ $this->r = $this->apps->_msg_response(2012);}
												}else{ $this->r = $this->apps->_msg_response(2011);}
											}else{ $this->r = $this->apps->_msg_response(2011);}
										}else{ $this->r = $this->apps->_msg_response(2007);}
									}else{ $this->r = $this->apps->_msg_response(2005);}
								}else{ $this->r = $this->apps->_msg_response(2002);}
							}else{ $this->r = $this->apps->_msg_response(2001);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	
}


?>