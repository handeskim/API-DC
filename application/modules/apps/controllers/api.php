<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Api extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->params = array();
		$this->confim = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(2000);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
		$this->obj = array( 'command'=> array(
				'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
				'secret_key' => '(string) secret key //YOUR API CREATED',
				'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', )
		);
		$this->_param = array();
	}
	public function history_balancer_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							$reseller = (string)$this->apps->_token_reseller($p->token);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$this->r['status'] = 1000;
									$obj = $this->mongo_db->where(array('client_id'=>$p->client_id,'reseller'=>$reseller))->get('transfer_log');
									$object = array();
									foreach($obj as $v){
										$object[] = array(
												'id' => getObjectId($v['_id']),
												'money_transfer' => $v['money_transfer'],
												'date_create' => $v['date_create'],
												'time_create' => $v['time_create'],
												'fee' => $v['fee'],
												'total_transfer' => $v['total_transfer'],
												'balancer_clients' => $v['balancer_clients'],
												'beneficiary_balancer' => $v['beneficiary_balancer'],
												'balancer_plus' => $v['balancer_plus'],
												'balancer_munis' => $v['balancer_munis'],
												'beneficiary_id' => $v['beneficiary_id'],
												'beneficiary' => $v['beneficiary'],
												'client_id' => $v['client_id'],
												'client_name' => $v['client_name'],
												'reseller' => $v['reseller'],
												'status' => $v['status'],
												'action' => $v['action'],
												'payer_id' => $v['payer_id'],
												'payer_name' => $v['payer_name'],
												'payer_balancer' => $v['payer_balancer'],
												'date_update_transfer' => $v['date_update_transfer'],
												'time_update_transfer' => $v['time_update_transfer'],
										);
									}
									$this->r['data'] = $object;
								}
							}
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	
	
}


?>