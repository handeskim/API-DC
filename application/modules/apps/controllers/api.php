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
									$this->r = $this->apps->_msg_response(1000);
									if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
									if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
									$date_start = strtotime($dstart);
									$date_end =  strtotime($dend);
									$obj = $this->mongo_db->where(array('client_id'=>$p->client_id,'reseller'=>$reseller))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('transfer_log');
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
												'transaction' => $v['transaction'],
												'type' => $v['type'],
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
	
	public function history_withdrawal_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							$reseller = (string)$this->apps->_token_reseller($p->token);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$this->r = $this->apps->_msg_response(1000);
									if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
									if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
									$date_start = strtotime($dstart);
									$date_end =  strtotime($dend);
									$obj = $this->mongo_db->where(array('client_id'=>$p->client_id,'reseller'=>$reseller))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('withdrawal');
									$object = array();
									foreach($obj as $v){
										if(!empty($v['money_transfer'])){ $money_transfer = $v['money_transfer'];}else{$money_transfer = null;}
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['fee'])){ $fee = $v['fee'];}else{$fee = null;}
										if(!empty($v['total_transfer'])){ $total_transfer = $v['total_transfer'];}else{$total_transfer = null;}
										if(!empty($v['balancer_clients'])){ $balancer_clients = $v['balancer_clients'];}else{$balancer_clients = null;}
										if(!empty($v['beneficiary_balancer'])){ $beneficiary_balancer = $v['beneficiary_balancer'];}else{$beneficiary_balancer = null;}
										if(!empty($v['balancer_plus'])){ $balancer_plus = $v['balancer_plus'];}else{$balancer_plus = null;}
										if(!empty($v['balancer_munis'])){ $balancer_munis = $v['balancer_munis'];}else{$balancer_munis = null;}
										if(!empty($v['payer_balancer'])){ $payer_balancer = $v['payer_balancer'];}else{$payer_balancer = null;}
										if(!empty($v['payer_id'])){ $payer_id = $v['payer_id'];}else{$payer_id = null;}
										if(!empty($v['beneficiary_id'])){ $beneficiary_id = $v['beneficiary_id'];}else{$beneficiary_id = null;}
										if(!empty($v['beneficiary'])){ $beneficiary = $v['beneficiary'];}else{$beneficiary = null;}
										if(!empty($v['payer_name'])){ $payer_name = $v['payer_name'];}else{$payer_name = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['client_name'])){ $client_name = $v['client_name'];}else{$client_name = null;}
										if(!empty($v['reseller'])){ $reseller = $v['reseller'];}else{$reseller = null;}
										if(!empty($v['transaction'])){ $transaction = $v['transaction'];}else{$transaction = null;}
										if(!empty($v['bank_id'])){ $bank_id = $v['bank_id'];}else{$bank_id = null;}
										if(!empty($v['bank_name'])){ $bank_name = $v['bank_name'];}else{$bank_name = null;}
										if(!empty($v['account_holders'])){ $account_holders = $v['account_holders'];}else{$account_holders = null;}
										if(!empty($v['bank_account'])){ $bank_account = $v['bank_account'];}else{$bank_account = null;}
										if(!empty($v['provinces_bank'])){ $provinces_bank = $v['provinces_bank'];}else{$provinces_bank = null;}
										if(!empty($v['branch_bank'])){ $branch_bank = $v['branch_bank'];}else{$branch_bank = null;}
										if(!empty($v['type'])){ $type = $v['type'];}else{$type = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										if(!empty($v['action'])){ $action = $v['action'];}else{$action = null;}
										if(!empty($v['transfer_transaction'])){ $transfer_transaction = $v['transfer_transaction'];}else{$transfer_transaction = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'money_transfer' => $money_transfer,
											'date_create' => $date_create,'fee' => $fee,'total_transfer' => $total_transfer,
											'balancer_clients' => $balancer_clients,'beneficiary_balancer' => $beneficiary_balancer,
											'balancer_plus' => $balancer_plus,'balancer_munis' => $balancer_munis,
											'payer_balancer' => $payer_balancer,
											'payer_id' => $payer_id,
											'beneficiary_id' => $beneficiary_id,
											'beneficiary' => $beneficiary,
											'payer_name' => $payer_name,
											'client_id' => $client_id,
											'client_name' => $client_name,
											'reseller' => $reseller,
											'transaction' => $transaction,
											'bank_id' => $bank_id,
											'bank_name' => $bank_name,
											'account_holders' => $account_holders,
											'bank_account' => $bank_account,
											'provinces_bank' => $provinces_bank,
											'branch_bank' => $branch_bank,
											'type' => $type,
											'status' => $status,
											'action' => $action,
											'transfer_transaction' => $transfer_transaction,
										);
									}
									$this->r['data'] = $this->result;
								}
							}
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	
	public function history_card_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							$reseller = (string)$this->apps->_token_reseller($p->token);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$this->r = $this->apps->_msg_response(1000);
									if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
									if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
									$date_start = strtotime($dstart);
									$date_end =  strtotime($dend);
									$obj = $this->mongo_db->where(array('client_id'=>$p->client_id,'reseller'=>$reseller))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
									$object = array();
									foreach($obj as $v){
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['card_seri'])){ $card_seri = $v['card_seri'];}else{$card_seri = null;}
										if(!empty($v['card_code'])){ $card_code = $v['card_code'];}else{$card_code = null;}
										if(!empty($v['card_type'])){ $card_type = $v['card_type'];}else{$card_type = null;}
										if(!empty($v['card_amount'])){ $card_amount = $v['card_amount'];}else{$card_amount = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['publisher'])){ $publisher = $v['publisher'];}else{$publisher = null;}
										if(!empty($v['reseller'])){ $reseller = $v['reseller'];}else{$reseller = null;}
										if(!empty($v['card_deduct'])){ $card_deduct = $v['card_deduct'];}else{$card_deduct = null;}
										if(!empty($v['card_rose'])){ $card_rose = $v['card_rose'];}else{$card_rose = null;}
										if(!empty($v['card_status'])){ $card_status = $v['card_status'];}else{$card_status = null;}
										if(!empty($v['card_message'])){ $card_message = $v['card_message'];}else{$card_message = null;}
										if(!empty($v['transaction_service'])){ $transaction_service = $v['transaction_service'];}else{$transaction_service = null;}
										if(!empty($v['transaction_card'])){ $transaction_card = $v['transaction_card'];}else{$transaction_card = null;}
										if(!empty($v['note'])){ $note = $v['note'];}else{$note = null;}
										if(!empty($v['tracking'])){ $tracking = $v['tracking'];}else{$tracking = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'note' => $note,
											'date_create' => $date_create,
											'card_seri' => $card_seri,
											'card_code' => $card_code,
											'card_type' => $card_type,
											'card_amount' => $card_amount,
											'client_id' => $client_id,
											'publisher' => $publisher,
											'reseller' => $reseller,
											'card_deduct' => $card_deduct,
											'tracking' => $tracking,
											'card_rose' => $card_rose,
											'card_status' => $card_status,
											'card_message' => $card_message,
											'transaction_service' => $transaction_service,
											'transaction_card' => $transaction_card,
										);
									}
									$this->r['data'] = $this->result;
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