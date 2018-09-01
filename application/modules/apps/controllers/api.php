<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
class Api extends REST_Controller {
	function __construct(){
		parent::__construct();
		// $this->load->model('reset_model','ResetMD');
	}
	public function AdCheck_post(){
		
	}
	
	
	public function ProfileUpdateMe_post(){
		$params = $this->input->post('params');
		$id_clients = $params['data']['id_client'];
		$param = array(
			'email' => $params['data']['email'],
			'phone' => $params['data']['phone'],
		);
		$response = array(
			'results' => Appscore::MeUpdateProfile($id_clients,$param),
		);	
		$this->response($response);
	}	
	public function PasswordUpdateMe_post(){
		$params = $this->input->post('params');
		$id_clients = $params['data']['id_client'];
		$passwordold = $params['data']['passwordold'];
		$passwordnew = $params['data']['passwordnew'];
		$response = array(
			'results' => Appscore::MeUpdatePasswords($id_clients,$passwordold,$passwordnew),
		);	
		$this->response($response);
	}
	
	public function profile_post(){	
		$params = $this->input->post('params');
		$response = array(
			'results' => Appscore::LoadProfile($params),
		);	
		$this->response($response);
	}
	
	public function convert_zone_get(){
		$params = $this->input->get('params');
		$domainDb = Appscore::db_check_domain($params);
		if($domainDb==true){
			$id_clients = (string)$domainDb[0]['_id'];
		}
		$response = array(
			'clients' => $id_clients
		);	
		$this->response($response);
	}
	public function url_redirect_get(){
		$name_record = $this->input->get('name_record');
		$params = array(
			'type_record' => 'IURL',
			'name_record' => $name_record,
		);
		$db_redirect = Appscore::domain_redirect_url($params);
		$response = array(
			'result' => $db_redirect
		);	
		$this->response($response);
	}
	
	public function company_info_get(){
		$params = $this->input->get('params');
		if($params==true){
			$results_response = Appscore::db_get_company_info($params);
			if(isset($results_response)){
				$id_reseller = (string)$results_response[0]["_id"];
				$key_api = Appscore::db_get_api_key($id_reseller);
				if(isset($key_api)){
					$key_api = core_encrypt_convert($key_api[0]['key']);
				}else{
					$key_api = null;
				}
				$response = array(
					'company' => $results_response[0]['company'],
					'key_api' => $key_api,
				);	
			}else{
				$response = array(
					'results' => Appscore::method_msg(2000),
				);	
			}
			
			$this->response($response);
		}
	}
	public function client_post(){
		$params = $this->input->post('params');
		if($params==true){
			$key_api = core_decrypt_convert($params['key']);
			$reseller = Appscore::get_reseller($key_api);
			if($reseller==true){
				$cmd = $params['cmd'];
				$data = $params['data'];
				$id_reseller = $reseller['id_reseller'];
				if($id_reseller==true ||$id_reseller!=null ){
					$results_response =  Appscore::method_client($cmd,$data,$id_reseller);
				}else{
					$results_response = Appscore::method_msg(2001);
				}
			}
		}else{
			$results_response = Appscore::method_msg(2000);
		}
		$response = array(
			'results' => $results_response,
		);	
		$this->response($response);
	}
	public function zone_post(){
		$params = $this->input->post('params');
		if($params==true){
			$key_api = core_decrypt_convert($params['key']);
			$reseller = Appscore::get_reseller($key_api);
			if($reseller==true){
				
				$cmd = $params['cmd'];
				$data = $params['data'];
				$id_reseller = $reseller['id_reseller'];
				if($id_reseller==true ||$id_reseller!=null ){
					$results_response =  Appscore::method_zone($cmd,$data,$id_reseller);
				}else{
					$results_response = Appscore::method_msg(2001);
				}
				
			}else{
				$results_response = Appscore::method_msg(2001);
			}
		}else{
			$results_response = Appscore::method_msg(2000);
		}
		$response = array(
			'results' => $results_response,
		);	
		$this->response($response);
	}
	public function record_post(){
		$params = $this->input->post('params');
		if($params==true){
			$key_api = core_decrypt_convert($params['key']);
			$reseller = Appscore::get_reseller($key_api);
			if($reseller==true){
				$cmd = $params['cmd'];
				$data = $params['data'];
				$id_reseller = $reseller['id_reseller'];
				if($id_reseller==true ||$id_reseller!=null ){
					$results_response =  Appscore::method_record($cmd,$data,$id_reseller);
				}else{
					$results_response = Appscore::method_msg(2001);
				}
				
			}
		}else{
			$results_response = Appscore::method_msg(2000);
		}
		$response = array(
			'results' => $results_response,
		);	
		$this->response($response);
	}
	public function nameserver_post(){
		$params = $this->input->post('params');
		if(isset($params)){
			$client_id = $params["client_id"];
			$results = Appscore::db_get_clients($client_id);
			if(isset($results[0]["id_reseller"])){
				$reseller_id = $results[0]["id_reseller"];
				$results_reseller =  Appscore::db_get_reseller_info($reseller_id);
				if($results_reseller==true){
					if(isset($results_reseller[0]["nameserver"]["ns1"])){
						$ns_1 = $results_reseller[0]["nameserver"]["ns1"];
						if($ns_1==null || $ns_1 == ''){
							$ns_1 = 'ns1.udns.asia';
						}
					}else{
						$ns_1 = 'ns1.udns.asia';
					}
					if(isset($results_reseller[0]["nameserver"]["ns2"])){
						$ns_2 = $results_reseller[0]["nameserver"]["ns2"];
						if($ns_2==null || $ns_2 == ''){
							$ns_2 = 'ns2.udns.asia';
						}
					}else{
						$ns_2 = 'ns2.udns.asia';
					}
					if(isset($results_reseller[0]["nameserver"]["ns3"])){
						$ns_3 = $results_reseller[0]["nameserver"]["ns3"];
						if($ns_3==null || $ns_3 == ''){
							$ns_3 = 'ns3.udns.asia';
						}
					}else{
						$ns_3 = 'ns3.udns.asia';
					}
					
					$response = array (
						'ns1' =>  $ns_1,
						'ns2' =>  $ns_2,
						'ns3' =>  $ns_3,
					);	
				}else{
					$response = array (
						'ns1' => 'ns1.udns.asia',
						'ns2' => 'ns2.udns.asia',
						'ns3' => 'ns3.udns.asia'
					);
				}
			}else{
				$response = array (
					'ns1' => 'ns1.udns.asia',
					'ns2' => 'ns2.udns.asia',
					'ns3' => 'ns3.udns.asia'
				);	
			}
		}else{
			$response = array (
				'ns1' => 'ns1.udns.asia',
				'ns2' => 'ns2.udns.asia',
				'ns3' => 'ns3.udns.asia'
			);
		}
		$this->response($response);
	}
	public function tasks_post(){
		$params = $this->input->post('data');
		if(isset($params)){
			$zone_id = $params["zone_id"];
			$client_id = $params["client_id"];
			$params_query = array(
				'id_zone'=> $zone_id,
				'client_id'=> $client_id,
			);
			$results = Appscore::db_get_idzone($params_query);
			$response = array(
				'results' => $results,
			);
		}else{
			$response = array(
				'results' => 'error',
			);
		}
		$this->response($response);
	}
	public function encrypt_post(){
		$params = $this->input->post('params');
		if($params==true){
			$results_response = core_encrypt_convert($params);
			$response = array(
				'results' => $results_response,
			);	
		}else{
			$response = array(
				'results' => 'error',
			);
		}
						
		$this->response($response);

	}
	public function login_account_post(){
		$params = $this->input->post('params');
		if($params==true){
			$key_api = core_decrypt_convert($params['key']);
			$reseller = Appscore::get_reseller($key_api);
			if($reseller==true){
				$cmd = $params['cmd'];
				$data = $params['data'];
				$id_reseller = $reseller['id_reseller'];
				if($id_reseller==true ||$id_reseller!=null || isset($reseller['id_reseller'])){
					if($data==true){
						if(isset($data['user_domain']) || isset($data['password'])){
							if($data['user_domain']!=null || $data['user_domain']!=''){
								if($data['password']!=null || $data['password']!=''){
									$user_domain = trim(strtolower($data['user_domain']));
									$password = md5(trim($data['password']));
									if($cmd=="LoginAccount"){
										$results_me_login =  Appscore::db_get_me_login($user_domain,$password);
										if($results_me_login==true){
											$results_response = array(
												'id' => (string)$results_me_login[0]['_id'],
												'data' => $results_me_login[0],
												'status' => true,
												'cmd' => $cmd,
												'message' => Appscore::method_msg(1000),
											);
										}else{
											$results_response = Appscore::method_msg(2040);
										}
									}else{
										$results_response = Appscore::method_msg(2040);
									}
								}else{
									$results_response = Appscore::method_msg(2040);
								}
							}else{
								$results_response = Appscore::method_msg(2001);
							}
						}else{
							$results_response = Appscore::method_msg(2001);
						}
						
					}else{
						$results_response = Appscore::method_msg(2001);
					}
				}else{
					$results_response = Appscore::method_msg(2001);
				}
			}else{
				$results_response = Appscore::method_msg(2001);
			}
		}else{
			$results_response = Appscore::method_msg(2000);
		}
		$response = array(
			'results' => $results_response,
		);	
		$this->response($response);
	}
	
///---End Class Apps REST---///
}
////------Start Class Core Apps-------////	
class Appscore extends MY_Controller{
	function __construct(){
		parent::__construct();
	}
	private function change_asscii($domain_name){
		mb_internal_encoding('utf-8');
		$Punycode = new Idna_convert();
		$domain_name_space = $Punycode->encode($domain_name);
		return $domain_name_space;

	}
	public function MeUpdateProfile($id_clients,$params){
		try{
			$update_zone =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_clients)))->set($params)->update('ureg_users');
			return $update_zone;
		}catch (Exception $e) {
			return false;
		}
	}
	public function MeUpdatePasswords($id_clients,$passwordold,$passwordnew){
		try{
			$whereArray = array(
				'_id'=>  new \MongoId($id_clients),
				'password'=>  md5($passwordold),
				
			);
			$params = array(
				'password' => md5($passwordnew),
			);
			$update_zone =	$this->mongo_db->where($whereArray)->set($params)->update('ureg_users');
			return $update_zone;
		}catch (Exception $e) {
			return false;
		}
	}
	public function LoadProfile($params){
		$id_clients = (string)$params["data"]["id_client"];
		try{
			$reponses = $this->mongo_db->get_where('ureg_users', array('_id' => new \MongoId($id_clients)));
			return $reponses;
		}catch (Exception $e) {
			return false;
		}
	}
	public function isValidDomainName($domain) {
		  return (preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain));
	}
	public function isValidMx($mx) {
		  return (preg_match('/^(0?[0-9]|[0-5][0-0])$/', $mx));
	}
	public function isValidEmailName($email) {
		  return (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email));
	}
	///////////////// Zone Validator ///////////////////////////
	
	public function isValidIpAddressRegex($string){
		return (preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $string));
	}
	public function isValidIpHostnameRegex($string){
		return (preg_match('/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/', $string));
	}
	public function db_get_me_login($username,$password){
		$results = $this->mongo_db->where(array('user_domain' => $username,'password' => $password))->get('ureg_users');
		return $results;
	}
	public function AdsDisplayCheck($params){
		
	}
	public function db_get_company_info($url){
		$results = $this->mongo_db->get_where('ureg_reseller', array('url' => $url));
		return $results;
	}
	
	public function db_check_username($username){
		$results = $this->mongo_db->get_where('ureg_users', array('username' => $username));
		return $results;
	}
	public function db_iframe_remove_record_active($params){
		$param = array(
			'id_zone' => $params['id_zone'],
			'client_id' => $params['client_id'],
			'resller_id' => $params['resller_id'],
		);
		$results = $this->mongo_db->get_where('ureg_zone_record', $param);
		foreach($results as $values){
			$params_result = array(
			'_id' =>  new \MongoId((string)$values['_id']),
			'id_zone' => $values['id_zone'],
			'client_id' => $values['client_id'],
			'resller_id' => $values['resller_id'],
			);
			$check = $this->mongo_db->where($params_result)->get('ureg_zone_record');
			if(!empty($check)){
				$deletel = $this->mongo_db->where($params_result)->delete('ureg_zone_record');
			}
		}
		
	}
	public function db_check_insert_record($params){
		$results = $this->mongo_db->get_where('ureg_zone_record', $params);
		return $results;
	}
	public function db_check_domain($user_domain){
		$results = $this->mongo_db->get_where('ureg_users', array('user_domain' => $user_domain));
		return $results;
	}
	public function db_create_clients($params){
		$results = $this->mongo_db->insert('ureg_users', $params);
		return $results;
	}
	public function db_create_zones($params){
		$results = $this->mongo_db->insert('ureg_zone', $params);
		return $results;
	}
	public function db_create_record($params){
		Appscore::add_process($params,'Add New Record');
		$results = $this->mongo_db->insert('ureg_zone_record', $params);
		
		return $results;
	}
	public function add_process($params,$command){
		$array_task_process = array(
			'params'=> $params,
			'action'=> $command,
			'status'=> 1,
			'date_query' => date("Y-m-d H:i:s A",time()),
			'date_process' => '',
		);
		$results = $this->mongo_db->insert('ureg_task_process', $array_task_process);
		return $results;
	}
	public function db_get_zone_id($id_zone){
		try{
			$Clients = $this->mongo_db->get_where('ureg_zone', array('_id' => new \MongoId($id_zone)));
			return $Clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_idzone($params){
		try{
			$zone_record = $this->mongo_db->where($params)->get('ureg_zone_record');
			return $zone_record;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_idzone_record($id_zone){
		try{
			$zone_record = $this->mongo_db->get_where('ureg_zone_record', array('id_zone' => $id_zone));
			return $zone_record;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_idZoneClients($id_clients){
		try{
			$zone_record = $this->mongo_db->get_where('ureg_zone', array('client_id' => $id_clients));
			return $zone_record;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_api_key($id_reseller){
		try{
			$Clients = $this->mongo_db->get_where('ureg_api_keys', array('id_reseller' => $id_reseller));
			return $Clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_clients($id_clients){
		try{
			$Clients = $this->mongo_db->get_where('ureg_users', array('_id' => new \MongoId($id_clients)));
			return $Clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_reseller_info($id_reseller){
		try{
			$reseller = $this->mongo_db->get_where('ureg_reseller', array('_id' => new \MongoId($id_reseller)));
			return $reseller;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_zone_clients($name_zone){
		try{
			$Clients = $this->mongo_db->get_where('ureg_users', array('user_domain' => $name_zone));
			return $Clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_reseller_name($name_reseller){
		try{
			$reseller = $this->mongo_db->get_where('ureg_reseller', array('name_reseller' => $name_reseller,));
			return $reseller;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_get_auth_clients($id_clients,$auth){
		try{
			$Clients = $this->mongo_db->get_where('ureg_users', array('_id' => new \MongoId($id_clients),'auth'=>$auth));
			return $Clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_check_zone_clients($name_zone){
		try{
			$ZoneClients = $this->mongo_db->get_where('ureg_zone', array('name_zone' => $name_zone));
			return $ZoneClients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_update_zone($id_zone,$prams){
		try{
			$update_zone =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_zone)))->set($prams)->update('ureg_zone');
			return $update_zone;
		}catch (Exception $e) {
			return false;
		}
	}
	
	public function db_remove_clients($id_clients){
		try{
			$remove_clients =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_clients)))->delete('ureg_users');
			return $remove_clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_hold_clients($id_clients){
		try{
			$hold_clients =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_clients)))->set('status', 0)->update('ureg_users');
			return $hold_clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_update_clients($id_clients,$params_update){
		try{
			$update_clients =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_clients)))->set($params_update)->update('ureg_users');
			return $update_clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function db_unhold_clients($id_clients){
		try{
			$hold_clients =	$this->mongo_db->where(array('_id'=>  new \MongoId($id_clients)))->set('status', 1)->update('ureg_users');
			return $hold_clients;
		}catch (Exception $e) {
			return false;
		}
	}
	public function get_reseller($key_api){
		if($key_api==true){
			$results = $this->mongo_db->get_where('ureg_api_keys', array('key' => $key_api));
			if($results==true || $id_reseller!=null ){
				$id_reseller = $results[0]['id_reseller'];
				if($id_reseller==true || $id_reseller!=null){
					$reseller = $this->mongo_db->get_where('ureg_reseller', array('_id' => new \MongoId($id_reseller)));
					$response = array(
						'key'=> $key_api,
						'id_reseller'=> $id_reseller,
						'reseller'=> $reseller,
					);
				}else{
					$response = array(
						'message' => Appscore::method_msg(3001),
					);
				}	
			}else{
				$response = array(
					'message' => Appscore::method_msg(3001),
				);
			}	
		}else{
			$response = array(
				'message' => Appscore::method_msg(3000),
			);
		}
		
		return $response;
	}
	public function domain_redirect_url($params){
		try{
			$response = $this->mongo_db->where($params)->get('ureg_zone_record');
			return $response;
		}catch (Exception $e) {
			
		}
		
	}
	public function method_record($cmd,$data,$id_reseller){
		$response = '';
		if($cmd==true){
			if($cmd=="CreateNewRecord"){
				if($data['id_zone']==true){
					$id_zone = core_decrypt_convert($data['id_zone']);
					$resultZone = Appscore::db_get_zone_id($id_zone);
					if($resultZone==true){
						$client_id = $resultZone[0]['client_id'];
						$resller_id = $resultZone[0]['resller_id'];
						$name_record = $data['name_record'];
						if($name_record==true){
							$type_record = $data['type_record'];
							if($type_record==true){
								$data_record = $data['data_record'];
								if($data_record==true){
									if($type_record=="MX"){
										$data_aux = $data['aux'];
										if($data_aux==null){
											$aux = 10;
										}else{
											$aux =  $data['aux'];
										}
										$validate_name_record_mx = Appscore::isValidDomainName($name_record);
										if($validate_name_record_mx==true){
											$validate_data_record_mx = Appscore::isValidDomainName($data_record);
											if($validate_data_record_mx==true){
												$validate_data_aux = Appscore::isValidMx($aux);
												if($validate_data_aux==true){
													$Cparams = array(
														'id_zone' => $id_zone,
														'client_id' => $client_id,
														'resller_id' => $resller_id,
														'name_record' => trim(strtolower($name_record)),
														'type_record' => $type_record,
														'data_record' => trim(strtolower($data_record)),
														'aux' => $aux,
													);
													$CCheck  = Appscore::db_check_insert_record($Cparams);
													if($CCheck==false){
														$params = array(
															'id_zone' => $id_zone,
															'client_id' => $client_id,
															'resller_id' => $resller_id,
															'name_record' => trim(strtolower($name_record)),
															'type_record' => $type_record,
															'data_record' => trim(strtolower($data_record)),
															'aux' => $aux,
															'ttl' => 3600,
															'stamp' => time(),
															'active' => $cmd,
															'status' => $cmd,
														);
														$InstallNewRecordMx = Appscore::db_create_record($params);
														if($InstallNewRecordMx==true){
															$create_RecordMx = true;
														}else{
															$create_RecordMx = false;
														}
														$response = array(
															'status' => $create_RecordMx,
															'cmd' => $cmd,
															'message' => Appscore::method_msg(1000),
														);	
													}else{
														$response = array(
															'cmd' => $cmd,
															'message' => Appscore::method_msg(2035),
														);
													}
													
												}else{
													$response = array(
														'cmd' => $cmd,
														'message' => Appscore::method_msg(2000),
													);
												}
											}else{
												$response = array(
													'cmd' => $cmd,
													'message' => Appscore::method_msg(2000),
												);
											}
											
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									if($type_record=="A" || $type_record=="AAAA"){
										$validate_name_record_a = Appscore::isValidIpHostnameRegex($name_record);
										if($validate_name_record_a==true){
											$validate_data_record_a =  Appscore::isValidIpAddressRegex($data_record);
											if($validate_data_record_a==true){
												$Cparams = array(
													'id_zone' => $id_zone,
													'client_id' => $client_id,
													'resller_id' => $resller_id,
													'name_record' => trim(strtolower($name_record)),
													'type_record' => $type_record,
												);
												$CCheck  = Appscore::db_check_insert_record($Cparams);
												if($CCheck==false){
													$params = array(
														'id_zone' => $id_zone,
														'client_id' => $client_id,
														'resller_id' => $resller_id,
														'name_record' => trim(strtolower($name_record)),
														'type_record' => $type_record,
														'data_record' => $data_record,
														'aux' => null,
														'ttl' => 3600,
														'stamp' => time(),
														'active' => $cmd,
														'status' => $cmd,
													);
													$InstallNewRecordA = Appscore::db_create_record($params);
													if($InstallNewRecordA==true){
														$create_RecordA = true;
													}else{
														$create_RecordA = false;
													}
													$response = array(
														'status' => $create_RecordA,
														'cmd' => $cmd,
														'message' => Appscore::method_msg(1000),
													);
												}else{
													// Check A Domain Name Primary  Mutipl//
													// $CheckPrimaryZone = Appscore::db_getinfo($id_zone);
													$response = array(
														'cmd' => $cmd,
														'message' => Appscore::method_msg(2035),
													);
												}
												
											}else{
												$response = array(
													'cmd' => $cmd,
													'message' => Appscore::method_msg(2000),
												);
											}
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									
									if($type_record=="CNAME"){
										$validate_name_record_cname = Appscore::isValidIpHostnameRegex($name_record);
										if($validate_name_record_cname==true){
											$validate_data_record_cname =  Appscore::isValidDomainName($data_record);
											if($validate_data_record_cname==true){
											$Cparams = array(
													'id_zone' => $id_zone,
													'client_id' => $client_id,
													'resller_id' => $resller_id,
													'name_record' => trim(strtolower($name_record)),
													'type_record' => $type_record
												);
											$CCheck  = Appscore::db_check_insert_record($Cparams);
											if($CCheck==false){
												$params = array(
													'id_zone' => $id_zone,
													'client_id' => $client_id,
													'resller_id' => $resller_id,
													'name_record' => trim(strtolower($name_record)),
													'type_record' => $type_record,
													'data_record' => trim(strtolower($data_record)),
													'aux' => null,
													'ttl' => 3600,
													'stamp' => time(),
													'active' => $cmd,
													'status' => $cmd,
												);
												$InstallNewRecordCNAME = Appscore::db_create_record($params);
												if($InstallNewRecordCNAME==true){
													$create_RecordCNAME = $InstallNewRecordCNAME;
												}else{
													$create_RecordCNAME = false;
												}
												$response = array(
													'status' => $create_RecordCNAME,
													'cmd' => $cmd,
													'message' => Appscore::method_msg(1000),
												);
											}else{
												$response = array(
													'cmd' => $cmd,
													'message' => Appscore::method_msg(2035),
												);
											}
											}else{
												$response = array(
													'cmd' => $cmd,
													'message' => Appscore::method_msg(2000),
												);
											}
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									if($type_record=="TXT"){
										$validate_name_record_text = Appscore::isValidDomainName($name_record);
										if($validate_name_record_text==true){
										$Cparams = array(
											'id_zone' => $id_zone,
											'client_id' => $client_id,
											'resller_id' => $resller_id,
											'name_record' => trim(strtolower($name_record)),
											'type_record' => $type_record,
										);
										$CCheck  = Appscore::db_check_insert_record($Cparams);
										if($CCheck==false){
											$params = array(
												'id_zone' => $id_zone,
												'client_id' => $client_id,
												'resller_id' => $resller_id,
												'name_record' => trim(strtolower($name_record)),
												'type_record' => $type_record,
												'data_record' => $data_record,
												'aux' => null,
												'ttl' => 3600,
												'stamp' => time(),
												'active' => $cmd,
												'status' => $cmd,
											);
											$InstallNewRecordTXT = Appscore::db_create_record($params);
											if($InstallNewRecordTXT==true){
												$create_RecordTXT = true;
											}else{
												$create_RecordTXT = false;
											}
											$response = array(
												'status' => $create_RecordTXT,
												'cmd' => $cmd,
												'message' => Appscore::method_msg(1000),
											);
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2035),
											);
										}
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									if($type_record=="NS"){
										$validate_name_record_NS = Appscore::isValidDomainName($name_record);
										if($validate_name_record_NS==true){
											$validate_data_record_NS =  Appscore::isValidDomainName($data_record);
											if($validate_data_record_NS==true){
												$Cparams = array(
													'id_zone' => $id_zone,
													'client_id' => $client_id,
													'resller_id' => $resller_id,
													'name_record' => trim(strtolower($name_record)),
													'type_record' => $type_record,
												);
												$CCheck  = Appscore::db_check_insert_record($Cparams);
												if($CCheck==false){
														$params = array(
															'id_zone' => $id_zone,
															'client_id' => $client_id,
															'resller_id' => $resller_id,
															'name_record' => trim(strtolower($name_record)),
															'type_record' => $type_record,
															'data_record' => trim(strtolower($data_record)),
															'aux' => null,
															'ttl' => 3600,
															'stamp' => time(),
															'active' => $cmd,
															'status' => $cmd,
														);
														$InstallNewRecordNS = Appscore::db_create_record($params);
														if($InstallNewRecordNS==true){
															$create_RecordNS = true;
														}else{
															$create_RecordNS = false;
														}
														$response = array(
															'status' => $create_RecordNS,
															'cmd' => $cmd,
															'message' => Appscore::method_msg(1000),
														);
												}else{
													$response = array(
														'cmd' => $cmd,
														'message' => Appscore::method_msg(2035),
													);
												}
											}else{
												$response = array(
													'cmd' => $cmd,
													'message' => Appscore::method_msg(2000),
												);
											}
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									
									if($type_record=="RURL" || $type_record=="IURL"){
										$validate_name_record_text = Appscore::isValidIpHostnameRegex($name_record);
										if($validate_name_record_text==true){
										$Cparams = array(
											'id_zone' => $id_zone,
											'client_id' => $client_id,
											'resller_id' => $resller_id,
											'name_record' => trim(strtolower($name_record)),
											'type_record' => $type_record,
										);
										Appscore::db_iframe_remove_record_active($params);
										$CCheck  = Appscore::db_check_insert_record($Cparams);
										if($CCheck==false){
											$params = array(
												'id_zone' => $id_zone,
												'client_id' => $client_id,
												'resller_id' => $resller_id,
												'name_record' => trim(strtolower($name_record)),
												'type_record' => $type_record,
												'data_record' => trim(strtolower($data_record)),
												'aux' => null,
												'ttl' => 3600,
												'stamp' => time(),
												'active' => $cmd,
												'status' => $cmd,
											);
											$InstallNewRecordTXT = Appscore::db_create_record($params);
											if($InstallNewRecordTXT==true){
												$create_RecordTXT = true;
											}else{
												$create_RecordTXT = false;
											}
											$response = array(
												'status' => $create_RecordTXT,
												'cmd' => $cmd,
												'message' => Appscore::method_msg(1000),
											);
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2035),
											);
										}
										}else{
											$response = array(
												'cmd' => $cmd,
												'message' => Appscore::method_msg(2000),
											);
										}
									}
									
									
									
								}else{
									$response = array(
										'message' => Appscore::method_msg(2033),
									);	
								}
							}else{
								$response = array(
									'message' => Appscore::method_msg(2032),
								);
							}
						}else{
							$response = array(
								'message' => Appscore::method_msg(2031),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}
			}
			if($cmd=="DeletelRecord"){
				if($data['id_record']==true){
					$id_record = $data['id_record'];
					$id_zone = $data['id_zone'];
					$id_clients = $data['id_clients'];
					$params = array(
						'_id' =>  new \MongoId($id_record),
						'id_zone' => $id_zone,
						'client_id' => $id_clients,
						'resller_id' => $id_reseller,
					);
					$response = array(
						'message' => $params,
					);
					$check = $this->mongo_db->where($params)->get('ureg_zone_record');
					if(!empty($check)){
						$deletel = $this->mongo_db->where($params)->delete('ureg_zone_record');
						if($deletel==true){
							Appscore::add_process($params,'Del Record');
							$response = array(
								'message' => Appscore::method_msg(2036),
							);	
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2031),
						);
					}
					
				}else{
					$response = array(
						'message' => Appscore::method_msg(2021),
					);
				}
			}
		}else{
			$response = array(
				'message' => Appscore::method_msg(2000),
			);
		}
		
		return $response;
		
	}
	public function method_zone($cmd,$data,$id_reseller){
		$response = '';
		if($cmd==true){
			if($cmd=="CreateNewZone"){
				if($data['name_zone']==true){
					$name_zone =  Appscore::change_asscii($data['name_zone']);
					$reslt_Clients = Appscore::db_get_zone_clients($name_zone);
					if($reslt_Clients==true){
						$id_clients = (string)$reslt_Clients[0]['_id'];
						$checkZone = Appscore::db_check_zone_clients($name_zone);
						if($checkZone==false){
							$params_create_zone = array(
								'name_zone' => trim(strtolower($name_zone)),
								'client_id' => $id_clients,
								'resller_id' => $id_reseller,
								'active' => 0,
								'auth' => sha1($id_clients),
								'status' => $cmd,
							);
							$InstallNewZone = Appscore::db_create_zones($params_create_zone);
							if($InstallNewZone==true){
								$create_zones = (string)$InstallNewZone;
								$status = true;
							}else{
								$create_zones = false;
								$status = false;
							}
							$response = array(
								'name_zone' => $name_zone,
								'data' => $create_zones,
								'status' => $status,
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2020),
							);
						}
						
					}else{
						$response = array(
							'message' => Appscore::method_msg(2014),
						);
					}
					
				}else{
					$response = array(
						'message' => Appscore::method_msg(2000),
					);
				}
			}
			if($cmd=="DeletelZone"){
				if($data['id_zone']==true){
					$id_zone = core_decrypt_convert($data['id_zone']);
					if($id_zone==true){
						$infoZone = Appscore::db_get_zone_id($id_zone);
						if($infoZone==true){
							$params = array(
								'status' => $cmd,
								'active' => $cmd,
							);
							$UpdateZone = Appscore::db_update_zone($id_zone,$params);
							if($InstallNewZone==true){
								$DeletelZone = true;
							}else{
								$DeletelZone = false;
							}
							$response = array(
								'name_zone' => $name_zone,
								'status' => $DeletelZone,
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}else{
					$response = array(
						'message' => Appscore::method_msg(2000),
					);
				}
			}
			
			
			if($cmd=="HoldZone"){
				if($data['id_zone']==true){
					$id_zone = core_decrypt_convert($data['id_zone']);
					if($id_zone==true){
						$infoZone = Appscore::db_get_zone_id($id_zone);
						if($infoZone==true){
							$params = array(
								'status' => $cmd,
								'active' => $cmd,
							);
							$UpdateZone = Appscore::db_update_zone($id_zone,$params);
							if($InstallNewZone==true){
								$HoldZone = true;
							}else{
								$HoldZone = false;
							}
							$response = array(
								'name_zone' => $id_zone,
								'status' => $HoldZone,
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}else{
					$response = array(
						'message' => Appscore::method_msg(2000),
					);
				}
			}
			
			
			if($cmd=="InfoZone"){
				if($data['id_zone']==true){
					$id_zone = core_decrypt_convert($data['id_zone']);
					if($id_zone==true){
						$infoZone = Appscore::db_get_zone_id($id_zone);
						$inforecord = Appscore::db_get_idzone_record($id_zone);
						if($inforecord==true){
							$infoRecordZone = $inforecord[0];
						}else{
							$infoRecordZone = null;
						}
						if($infoZone==true){
							$response = array(
								'data_zone' => $infoZone[0],
								'data_record' => $infoRecordZone,
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}else{
				$response = array(
					'message' => Appscore::method_msg(2000),
					);
				}
			}
			
			if($cmd=="InfoZones"){
				if($data['id_clients']==true){
					$id_clients = core_decrypt_convert($data['id_clients']);
					if($id_clients==true){
						$infoZones = Appscore::db_get_idZoneClients($id_clients);
						$zone_id = (string)$infoZones[0]["_id"];
						$infoRecord = Appscore::db_get_idzone_record($zone_id);
						if($zone_id==true){
							$response = array(
								'data_zone' => $infoZones,
								'data_record' => $infoRecord,
								'total' => array('item' => count($infoRecord)),
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}else{
					$response = array(
						'message' => Appscore::method_msg(2000),
					);
				}
			}
			
			if($cmd=="UpdateZone"){
				if($data['id_zone']==true){
					$id_zone = core_decrypt_convert($data['id_zone']);
					if($id_zone==true){
						$infoZone = Appscore::db_get_zone_id($id_zone);
						if($infoZone==true){
							$params = array(
								'status' => 'active',
								'active' => $cmd,
							);
							$UpdateZone = Appscore::db_update_zone($id_zone,$params);
							if($UpdateZone==true){
								$HoldZone = true;
							}else{
								$HoldZone = false;
							}
							$response = array(
								'name_zone' => $id_zone,
								'status' => $HoldZone,
								'cmd' => $cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'message' => Appscore::method_msg(2021),
							);
						}
					}else{
						$response = array(
							'message' => Appscore::method_msg(2021),
						);
					}
				}else{
					$response = array(
						'message' => Appscore::method_msg(2000),
					);
				}
			}
			
		}else{
			$response = array(
				'message' => Appscore::method_msg(2000),
			);
		}
		
		return $response;
	}
	private function install_after_zone_clients($params){
		$results = $this->mongo_db->insert('ureg_zone', $params);
		return $results;
	}
	public function method_client($cmd,$data,$id_reseller){
		$response = '';
		if($cmd=='CreateNewClients'){
			if($data==true){
				$username = trim(strtolower($data['username']));
				$user_domain = trim(strtolower($data['user_domain']));
				$password = $data['password'];
				$email = $data['email'];
				$phone = $data['phone'];
				$auth_remote = $data['auth'];
				$checkusernameDb = Appscore::db_check_username($username);
				$checkuser_domainDb = Appscore::db_check_domain($user_domain);
				$checkDomain = Appscore::isValidDomainName($user_domain);
				$checkEmail = Appscore::isValidEmailName($email);
				
				if($username==null || $username=='' || strlen($username) < 4  || $checkusernameDb==true){
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2100),
					);

				}else if($user_domain==null || $user_domain=='' || strlen($user_domain) < 4 || $checkDomain == false || $checkuser_domainDb==true){
					$response = array(
						'checkDomain' => $checkDomain,
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2101),
					);
				}else if($password==null || $password=='' || strlen($password) < 6){
					$response = array(
						'checkDomain' => $checkDomain,
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2012),
					);
				}else if($email==null || $email=='' || $checkEmail==false){
					$response = array(
						'checkDomain' => $checkDomain,
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2013),
					);
				}else{
					$params = array(
						'username'  =>  Appscore::change_asscii($username),
						'user_domain'  => Appscore::change_asscii($user_domain),
						'password'  => md5($password),
						'email'  => $email,
						'phone'  => $phone,
						'id_reseller'  => $id_reseller,
						'id_levels'  => 1,
						'create_date'  => date("Y-m-d H:i:s",time()),
						'update_date'  => date("Y-m-d H:i:s",time()),
						'status'  => 1,
						'auth'  => $auth_remote,
					);
					$install_clients = Appscore::db_create_clients($params);
					if($install_clients==true){
						$create_clients = (string)$install_clients;
							$params_install_zone = array(
								'name_zone' => Appscore::change_asscii($user_domain), 
								'client_id' => $create_clients, 
								'resller_id' => $id_reseller, 
								'active' => 0, 
								'auth' => md5(md5($password)), 
								'status' => "CreateNewZone"
							);
						Appscore::install_after_zone_clients($params_install_zone);
						$status = true;
					}else{
						$create_clients = false;
						$status = false;
					}
					$response = array(
						'data' => $create_clients,
						'status' => $status,
						'cmd' => $cmd,
						'message' => Appscore::method_msg(1000),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		//////////////// end create news clients ////////////////////////////
		if($cmd=='DeletelClients'){
			if($data==true){
				$id_clients = core_decrypt_convert($data['id_clients']);
				$checkClients = Appscore::db_get_clients($id_clients);
				if($checkClients==true){
					$remove_clients = Appscore::db_remove_clients($id_clients);
					if($remove_clients==true){
						$response = array(
							'status' => $remove_clients,
							'cmd' => $cmd,
							'message' => Appscore::method_msg(1000),
						);
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2014),
						);
					}
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		//////////////// end Deletel Clients clients ////////////////////////////
		if($cmd=='UpdateClients'){
			if($data==true){
			$id_clients = core_decrypt_convert($data['id_clients']);
				$InfoClient = Appscore::db_get_clients($id_clients);
				if($InfoClient==true){
					$cmd_sub = $data['cmd_sub'];
					if($cmd_sub=="UpdateEmail"){
						$email = $data['email'];
						if($email==true){
							$checkEmail = Appscore::isValidEmailName($email);
							if($checkEmail==true){
								$params_update_email = array(
									'email' => $email,
								);
								$status = Appscore::db_update_clients($id_clients,$params_update_email);
								$response = array(
									'status'=> $status,
									'cmd' => $cmd_sub,
									'message' => Appscore::method_msg(1000),
								);
							}else{
								$response = array(
									'status'=> $status,
									'cmd' => $cmd_sub,
									'message' => Appscore::method_msg(2013),
								);
							}
								
						}else{
							$response = array(
								'cmd' => $cmd_sub,
								'message' => Appscore::method_msg(2014),
							);
						}
						
					}
					if($cmd_sub=="UpdatePassword"){
						$password = $data['password'];
						if($password==true){
							$params_update_password = array(
								'password' => md5($password),
							);
							$status = Appscore::db_update_clients($id_clients,$params_update_password);
							$response = array(
								'status'=> $status,
								'cmd' => $cmd_sub,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'cmd' => $cmd_sub,
								'message' => Appscore::method_msg(2014),
							);
						}
						
					}
					if($cmd_sub=="UpdatePhone"){
						$phone = $data['phone'];
						if($phone==true){
							$params_update_phone = array(
								'phone' => $phone,
							);
							$status = Appscore::db_update_clients($id_clients,$params_update_phone);
							$response = array(
								'status'=> $status,
								'cmd' => $cmd_sub,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'cmd' => $cmd_sub,
								'message' => Appscore::method_msg(2014),
							);
						}
						
					}
					if($cmd_sub=="UpdateAuth"){
						$auth = random_auth_core();
						$params_update_auth = array(
							'auth' => $auth,
						);
						$status = Appscore::db_update_clients($id_clients,$params_update_auth);
						$response = array(
							'status'=> $status,
							'cmd' => $cmd_sub,
							'message' => Appscore::method_msg(1000),
						);
					}
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		//////////////// end Deletel Clients clients ////////////////////////////
		if($cmd=='InfoClients'){
			if($data==true){
			$id_clients = core_decrypt_convert($data['id_clients']);
				$InfoClient = Appscore::db_get_clients($id_clients);
				if($InfoClient==true){
					$response = array(
						'id_clients' => (string)$InfoClient[0]['_id'],
						'data' => $InfoClient[0],
						'cmd' => $cmd,
						'message' => Appscore::method_msg(1000),
					);
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		//////////////// end Deletel Clients clients ////////////////////////////
		
		if($cmd=='HoldClients'){
			if($data==true){
			$id_clients = core_decrypt_convert($data['id_clients']);
				$InfoClient = Appscore::db_get_clients($id_clients);
				if($InfoClient==true){
					$holdClients = Appscore::db_hold_clients($id_clients);
					if($holdClients==true){
						$response = array(
							'status' => $holdClients,
							'cmd' => $cmd,
							'message' => Appscore::method_msg(1000),
						);
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2014),
						);
					}
					
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		
		//////////////// end HoldClients Clients clients ////////////////////////////
		if($cmd=='UnHoldClients'){
			if($data==true){
			$id_clients = core_decrypt_convert($data['id_clients']);
				$InfoClient = Appscore::db_get_clients($id_clients);
				if($InfoClient==true){
					$holdClients = Appscore::db_unhold_clients($id_clients);
					if($holdClients==true){
						$response = array(
							'status' => $holdClients,
							'cmd' => $cmd,
							'message' => Appscore::method_msg(1000),
						);
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2014),
						);
					}
					
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		//////////////// end Deletel Clients clients ////////////////////////////
		if($cmd=='TransferClients'){
			if($data==true){
				$id_clients = core_decrypt_convert($data['id_clients']);
				$auth = $data['auth'];
				$InfoClient = Appscore::db_get_auth_clients($id_clients,$auth);
				if($InfoClient==true){
					$sub_cmd = $data['sub_cmd'];
					if($sub_cmd=="ok"){
						$name_reseller = $data['name_reseller'];
						if($name_reseller==true){
							$response_reseller = Appscore::db_get_reseller_name($name_reseller);
							if($response_reseller==true){
								$id_reseller = (string)$response_reseller[0]['_id'];
								$params_update = array(
									'id_reseller' => $id_reseller,
									'status' => $sub_cmd,
								);
								$transfer = Appscore::db_update_clients($id_clients,$params_update);
								if($transfer==true){
									$response = array(
										'status' => $transfer,
										'cmd' => $sub_cmd,
										'message' => Appscore::method_msg(1000),
									);
								}else{
									$response = array(
										'id_reseller' => $response_reseller,
										'cmd' => $sub_cmd,
										'message' => Appscore::method_msg(2000),
									);
								}
							}else{
								$response = array(
									'cmd' => $sub_cmd,
									'cmd' => $sub_cmd,
									'message' => Appscore::method_msg(2000),
								);
							}
						}else{
							$response = array(
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(2000),
							);
						}
						
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2000),
						);
					}
					if($sub_cmd=="approved"){
						$params_update = array(
							'status' => $sub_cmd,
						);
						$transfer = Appscore::db_update_clients($id_clients,$params_update);
						if($transfer==true){
							$response = array(
								'status' => $transfer,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'id_reseller' => $response_reseller,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(2000),
							);
						}
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2000),
						);
					}
					if($sub_cmd=="reject"){
						$params_update = array(
							'status' => $sub_cmd,
						);
						$transfer = Appscore::db_update_clients($id_clients,$params_update);
						if($transfer==true){
							$response = array(
								'status' => $transfer,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'id_reseller' => $response_reseller,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(2000),
							);
						}
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2000),
						);
					}
					if($sub_cmd=="pendding"){
						$params_update = array(
							'status' => $sub_cmd,
						);
						$transfer = Appscore::db_update_clients($id_clients,$params_update);
						if($transfer==true){
							$response = array(
								'status' => $transfer,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(1000),
							);
						}else{
							$response = array(
								'id_reseller' => $response_reseller,
								'cmd' => $sub_cmd,
								'message' => Appscore::method_msg(2000),
							);
						}
					}else{
						$response = array(
							'cmd' => $cmd,
							'message' => Appscore::method_msg(2000),
						);
					}
				}else{
					$response = array(
						'cmd' => $cmd,
						'message' => Appscore::method_msg(2014),
					);
				}
			}else{
				$response = array(
					'cmd' => $cmd,
					'message' => Appscore::method_msg(2001),
				);
			}
			return $response;
		}
		if($cmd=='' || $cmd==null){
			$response = array(
				'cmd' => $cmd,
				'message' => Appscore::method_msg(2001),
			);
			return $response;
		}
		return $response;
	}
	
	
	
	
	
	public function method_msg($id){
		if($id=='1000') {
			
			return $msg =  array(
				'code' => "1000",
				'message' => "command successful",
			);
			break;
		}
		if($id=='2000') {
			
			return $msg =  array(
				'code' => "2000",
				'message' =>  "error syntax command",
			);
			break;
		}
		if($id=='2001') {
			
			return $msg =  array(
				'code' => "2001",
				'message' =>  "error syntax method",
			);
			break;
		}
		if($id=='2100') {
			return $msg =  array(
				'code' => "2100",
				'message' =>  "domain username syntax or not found or already exists (example.com)",
			);
			break;
		}
		if($id=='2101') {
			return $msg =  array(
				'code' => "2101",
				'message' =>  "user_domain syntax or not found, user_domain validator correct domain or already exists (example.com)",
			);
			break;
		}
		if($id=='2012') {
			return $msg =  array(
				'code' => "2012",
				'message' =>  "Password syntax method or Password Length is greater than 6 (123axcd456)",
			);
			break;
		}
		if($id=='2013') {
			return $msg =  array(
				'code' => "2013",
				'message' =>  "Email syntax method or Email Validator (example@example.com)",
			);
			break;
		}
		if($id=='2014') {
			return $msg =  array(
				'code' => "2014",
				'message' =>  "clients not founds",
			);
			break;
		}
		if($id=='2020') {
			return $msg =  array(
				'code' => "2020",
				'message' =>  "zone already exists",
			);
			break;
		}
		if($id=='2021') {
			return $msg =  array(
				'code' => "2021",
				'message' =>  "name zone not founds",
			);
			break;
		}
		if($id=='2031') {
			return $msg =  array(
				'code' => "2031",
				'message' =>  "name record not founds",
			);
			break;
		}
		if($id=='2032') {
			return $msg =  array(
				'code' => "2032",
				'message' =>  "type record not founds",
			);
			break;
		}
		if($id=='2033') {
			return $msg =  array(
				'code' => "2033",
				'message' =>  "data record not founds",
			);
			break;
		}
		if($id=='2034') {
			return $msg =  array(
				'code' => "2034",
				'message' =>  "aux record not founds",
			);
			break;
		}
		if($id=='2035') {
			return $msg =  array(
				'code' => "2035",
				'message' =>  "record already exists",
			);
			break;
		}
		if($id=='2036') {
			return $msg =  array(
				'code' => "2036",
				'message' =>  "Deletel record oke",
			);
			break;
		}
		if($id=='2040') {
			return $msg =  array(
				'code' => "2040",
				'message' =>  "Login False",
			);
			break;
		}
		if($id=='3000') {
			return $msg =  array(
				'code' => "3000",
				'message' =>  "error syntax key api",
			);
			break;
		}
		if($id=='3001') {
			
			return $msg =  array(
				'code' => "3001",
				'message' =>  "not found key api",
			);
			break;
		}
		if($id=='9999') {
			return $msg =  array(
				'code' => "9999",
				'message' =>  "error syntax not method command",
			);
			 break;
		}
	}
///---End Class Apps---///	
}
?>