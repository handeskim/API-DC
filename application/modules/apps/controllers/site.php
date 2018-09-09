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
		$this->result = array();
		$this->param = array();
	}
	public function system_info_get(){
		$this->obj['user'] = $this->mongo_db->count("ask_users");
		$this->obj['card_change'] = $this->mongo_db->count("log_card_change");
		$this->obj['withdrawal'] = $this->mongo_db->count("withdrawal");
		$this->obj['transfer_log'] = $this->mongo_db->count("transfer_log");
		////////////////////
		$command_withdrawal = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => 1,),),);
		$this->obj['withdrawal_group'] = $this->mongo_db->aggregate('withdrawal',$command_withdrawal);
		////////////////////
		$command_transfer_log = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => 1,),),);
		$this->obj['transfer_log_group'] = $this->mongo_db->aggregate('transfer_log',$command_transfer_log);
		////////////////////
		$transfer_transaction = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['transfer_transaction'] = $this->mongo_db->aggregate('transfer_log',$transfer_transaction);
		////////////////////
		$withdrawal_transaction = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['withdrawal_transaction'] = $this->mongo_db->aggregate('withdrawal',$withdrawal_transaction);
		////////////////////	
		
		$card_transaction = array('$group' => array('_id' => '$transaction_card','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['card_transaction'] = $this->mongo_db->aggregate('log_card_change',$card_transaction);
		////////////////////
		$command_transaction = array('$group' => array('_id' => '$transaction_card','count' => array( '$sum' => 1,),),);
		$this->obj['card_change_hod'] = $this->mongo_db->aggregate('log_card_change',$command_transaction);
		////////////////////
		$command_api = array('$group' => array('_id' => '$level','count' => array( '$sum' => 1,),),);
		$this->obj['api_group'] = $this->mongo_db->aggregate('api_keys',$command_api);
		////////////////////
		$this->response($this->obj);
	}
	
	public function SlugBlogs_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		try{
			if(!empty($p->alias)){
					$this->obj = $this->mongo_db->select(array('alias',))->where(array('alias' => $p->alias))->get("news");
					$this->r['result'] = $this->obj;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}	
	public function alias_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		if(!empty($p->alias)){
			$this->obj = $this->mongo_db->where(array('alias'=>$p->alias))->get("news");
			if(!empty($this->obj)){
				$c = $this->obj[0]['categories'];
				$this->param = $this->mongo_db->where(array('categories'=>$c))->where_ne('alias',$p->alias)->order_by(array('time_create' => 'DESC'))->limit(7)->get("news");
			}
			$this->r['related'] = $this->param;
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function site_notifacation_top_post(){
		$this->obj = $this->mongo_db->where(array('categories'=>'faq'))->order_by(array('time_create' => 'DESC'))->limit(3)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function info_service_payments_get(){
		$this->obj = $this->mongo_db->where(array('status'=>'active'))->get("payment_service");
		if(!empty($this->obj)){
			foreach($this->obj as $v){
				$this->param[] = array(
					'_id' => getObjectid($v['_id']),
					'name_service' => $v['name_service'],
					'url_api' => $v['url_api'],
					'receiver' => $v['receiver'],
					'merchant_id' => $v['merchant_id'],
					'merchant_pass' => $v['merchant_pass'],
					'status' => $v['active'],
					'title' => $v['title'],
				);
			}
			$this->r['result'] = $this->param;
		}
		$this->response($this->r);
	}
	public function site_load_site_faq_post(){
		$this->obj = $this->mongo_db->where(array('categories'=>'faq'))->order_by(array('time_create' => 'DESC'))->limit(5)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
		
	}
	public function site_notification_post(){
		$this->obj = $this->mongo_db->select(array('alias','title','description','date_create'))->where(array('categories'=>'notification'))->order_by(array('time_create' => 'DESC'))->limit(1)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj[0];
		}
		$this->response($this->r);
		
	}
	public function news_box_post(){
		$this->obj = $this->mongo_db->where_ne('categories','faq')->order_by(array('time_create' => 'DESC'))->limit(5)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
		
	}
	
	public function BlogsAdd_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		try{
			
			if(!empty($p->alias)){ $this->param['alias'] = slugify(url_encoded($p->alias)); }else{ $this->param['alias'] = time(); }
			if(!empty($p->categories)){ $this->param['categories'] = $p->categories; }else{ $this->param['categories'] = time(); }
			if(!empty($p->title)){ $this->param['title'] = $p->title; }else{ $this->param['title'] = time(); }
			if(!empty($p->images)){ $this->param['images'] = $p->images; }else{ $this->param['images'] = time(); }
			if(!empty($p->keywords)){ $this->param['keywords'] = $p->keywords; }else{ $this->param['keywords'] = time(); }
			if(!empty($p->description)){ $this->param['description'] = $p->description; }else{ $this->param['description'] = time(); }
			if(!empty($p->description_seo)){ $this->param['description_seo'] = $p->description_seo; }else{ $this->param['description_seo'] = time(); }
			if(!empty($p->title_seo)){ $this->param['title_seo'] = $p->title_seo; }else{ $this->param['title_seo'] = time(); }
			if(!empty($p->contents)){ $this->param['contents'] = $p->contents; }
		
			$this->param['time_create'] = time();
			$this->param['date_create'] = date("Y-m-d H:i:s",time());
			$check_alias =  $this->mongo_db->where(array('alias'=>slugify(url_encoded($p->alias))))->get('news');
			if(empty($check_alias)){
				$this->obj = $this->mongo_db->insert('news',$this->param);
				$this->r['result'] = true;
			}else{
				$this->r['result'] = false;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function blogs_update_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		try{
			if(!empty($p->alias)){ $this->param['alias'] = $p->alias; }
			if(!empty($p->categories)){ $this->param['categories'] = $p->categories;  }
			if(!empty($p->title)){ $this->param['title'] = $p->title; }
			if(!empty($p->images)){ $this->param['images'] = $p->images; }
			if(!empty($p->keywords)){ $this->param['keywords'] = $p->keywords; }
			if(!empty($p->description)){ $this->param['description'] = $p->description; }
			if(!empty($p->description_seo)){ $this->param['description_seo'] = $p->description_seo; }
			if(!empty($p->title_seo)){ $this->param['title_seo'] = $p->title_seo; }
			if(!empty($p->contents)){ $this->param['contents'] = (string)$p->contents; }
			$this->param['time_create'] = time();
			$this->param['date_create'] = date("Y-m-d H:i:s",time());
			if(!empty($p->keys)){
				$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->param)->update('news');
				$this->r['result'] = true;
			}else{
				$this->r['result'] = false;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_card_get(){
	
		$this->obj = $this->mongo_db->where(array('status'=>'active'))->get("card");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function min_transfer_get(){
	
		$this->obj = $this->mongo_db->select('min_withdraw')->get("config");
		if(!empty($this->obj)){
			$this->r['result'] = (int)$this->obj[0]['min_withdraw'];
		}
		$this->response($this->r);
	}
	public function info_clients_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		
		try{
			$this->obj = $this->mongo_db->select(array('username','reseller'))->where(array('_id' => new \MongoId($p->keys)))->get("ask_users");
			$this->r['result'] = $this->obj[0];
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_publisher_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		try{
			$this->obj = $this->mongo_db->select(array('username'))->where(array('_id' => new \MongoId($p->keys),'client_id'=>$p->client_id))->get("publisher");
			$this->r['result'] = $this->obj[0];
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->get("site");
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> encrypt_obj(json_encode($this->obj),$this->_api_key,$this->_is_private_key)); }
				}
			}
		}
		$this->response($this->r);
	}
	public function config_update_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->transfer)){ $this->obj['transfer'] = (int)$p->transfer; }
						if(!empty($p->withdraw)){ $this->obj['withdraw'] = (int)$p->withdraw; }
						if(!empty($p->min_withdraw)){ $this->obj['min_withdraw'] = (int)$p->min_withdraw; }
						if(!empty($p->rose_reseller)){ $this->obj['rose_reseller'] = (int)$p->rose_reseller; }
						if(!empty($p->rose_client)){ $this->obj['rose_client'] = (int)$p->rose_client; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('config');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function config_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->get("config");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}
}


?>