<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Documents extends REST_Controller {
	protected $rest_format = 'xml';
   
	function __construct(){
		parent::__construct();
		
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		
		$this->r = array('status'=>200,'result'=>null);
		$this->obj = array( 'command'=> array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', )
					);
	}
	public function index_get(){
		
		$this->r['_api_key'] = $this->_api_key();
		$this->r['_level'] = $this->_level;
		$this->r['SERVER _ API'] = base_url();
		$this->r['hello'] = array(
			'url' => base_url('hello?'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'command' =>  array(
						'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
						'secret_key' => '(string) secret key //YOUR API CREATED', ),
			'response'=> array(
				'status'=> '(int) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'msg' => '(string) thông báo từ hệ thống',
			),
		);
		$this->r['connect'] = array(
			'url' => base_url('connect'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'param' => array(
				'username'=> 'Tài khoản Reseller ID',
				'password'=> 'Mật khẩu Reseller ID',
				'auth'=> 'Mật khẩu Bảo mật Reseller ID',
			),
			'command' =>  array(
					'api_name' => $this->apps->_api_name(),
					'secret_key' => '(string) secret key //YOUR API CREATED',
					'param' => 'encrypt({param})',
					),
			'response'=> array(
				'status'=> '(int) status code',
				'description'=> 'Hệ thống trả về chuỗi Token Nhận dạng Reseller để thao tác quản lý, Thêm, Sửa, Xóa.... bao gồm các trường có bắt buộc token',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'result' => array('(string) data // dữ liệu mã hóa, giải mã bằng hàm decrypt để lấy dữ liệu,',
						array('token'=>'string token'),
					),
				'msg' => '(string) thông báo từ hệ thống',
			),
		);
		
		$this->r['check'] = array(
			'url' => base_url('check'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'param' => array(
				'card_type' => array (
						'doctype'=>'(integer) Loại thẻ cào là value <option><item><value>',
						'option' => $this->apps->_card_config(),
						),
				'card_seri' => '(string) Số serial thẻ cào <card_type><card_amount><perfix_seri> Validate',
				'card_code' => '(string) Mã code thẻ cào <card_type><card_amount><perfix_code> Validate',
				'card_amount' => '(integer) Mệnh giá <card_type><amount> Validate',
			),
			'command' =>  array(
						'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
						'secret_key' => '(string) secret key //YOUR API CREATED',
						'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', 
						'data' => '(string) encrypt(param) dữ liệu đã mã hóa bằng hàm encrypt để đưa dữ liệu lên', 
						
						),
			'response'=> array(
				'status'=> '(integer) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'data' => '(string) data // dữ liệu mã hóa, giải mã bằng hàm decrypt để lấy dữ liệu',
				'msg' => '(string) thông báo từ hệ thống',
				'amount' => '(int) Mệnh giá thẻ cào nếu gửi yêu cầu thành công',
			),
		);
		$this->r['transaction'] = array(
			'url' => base_url('transaction'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'param' => array(
				'transaction_id' => '(string) transaction id ',
			),
			'command' =>  array(
						'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
						'secret_key' => '(string) secret key //YOUR API CREATED',
						'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', 
						'data' => '(string) encrypt(param) dữ liệu đã mã hóa bằng hàm encrypt để đưa dữ liệu lên', 
						
						),
			'response'=> array(
				'status'=> '(integer) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'data' => '(string) data // dữ liệu mã hóa, giải mã bằng hàm decrypt để lấy dữ liệu',
				'msg' => '(string) thông báo từ hệ thống',
			),
		);
		$this->r['balancer'] = array(
			'url' => base_url('balancer'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'command' =>  array(
						'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
						'secret_key' => '(string) secret key //YOUR API CREATED',
						'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', 
						'data' => '(string) encrypt(param) dữ liệu đã mã hóa bằng hàm encrypt để đưa dữ liệu lên', 
						),
			'response'=> array(
				'status'=> '(integer) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'data' => '(string) data // dữ liệu mã hóa, giải mã bằng hàm decrypt để lấy dữ liệu',
				'msg' => '(string) thông báo từ hệ thống',
			),
		);
		$this->r['transfer'] = array(
			'url' => base_url('transfer'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'description' => 'Là giao dịch dùng để chuyển số dư giữa các tài khoản',
			'param' => array(
				'token' => 'Token API Reseller',
				'amount' => '(int) min amount 1000 transaction id ',
				'beneficiary' => '(string) tài khoản hưởng thụ',
				'auth' => '(string) mật khẩu chuyển tiền của bạn',
				'option'=> array(
					'doctype'=>'lựa chọn kiểu giao dịch value 1,2,3  (mặc định hệ thống là 1) bạn có thể chọn hoặc không',
					'option_value' => array(
							array('doctype'=>'là giao dịch cần chờ xác thực lần 2', 'value'=>1),
							array('doctype'=>'chuyển luôn không cần xác thực','value'=>2), 
						),
					),
				'notification'=> array(
					'doctype'=>'lựa chọn thông báo giao dịch value 1,2,3 (mặc định hệ thống là 1) bạn có thể chọn hoặc không',
					'option_value' => array(
							array('doctype'=> 'không gửi email', 'value'=>1),
							array('doctype'=> 'gửi email','value'=>2), 
						),
					),
			),
			'command' =>  array(
						'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
						'secret_key' => '(string) secret key //YOUR API CREATED',
						'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', 
						'data' => '(string) encrypt(param) dữ liệu đã mã hóa bằng hàm encrypt để đưa dữ liệu lên', 
						
						),
			'response'=> array(
				'status'=> '(integer) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'data' => '(string) data // dữ liệu mã hóa, giải mã bằng hàm decrypt để lấy dữ liệu',
				'msg' => '(string) thông báo từ hệ thống',
			),
		);
		$this->r['code_status'] = array(
			'url' => base_url('code?'.$this->apps->_api_name().'={your_secret_key}'),
			'method' => 'GET',
			'description' => array(
				'status_0' => '1 => 999 Mã lỗi thuộc hệ thống, kết nối...',
				'status_1' => '1000 Mã thành công tuyệt đối',
				'status_2' => '1001 => 1999 mã lỗi thành công nhưng gặp vấn đề trong xử lý được phép tạm giữ rollback ',
				'status_3' => '2000 => 2999 mã lỗi thiếu thông tin, trường',
				'status_4' => '3000 => 3999 mã lỗi thuộc liên kết với các telco, provider, banking',
			),
			'param' => array( 'code' => 'Mã lỗi' ),
			'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
			'response'=> array(
				'status'=> '(int) status code',
				'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
				'data' => 'info mã lỗi',
			)
		);	
		if($this->_level == 2){
			$this->r['site'] = array(
			'url' => base_url('site/info?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED', ),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_site',
				),
			);
			$this->r['user'] = array(
			'url' => base_url('user?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'Description' => 'Phương thức GET là để Lấy Thông tin user, ',
				'param' => array(
					'id_user' => 'ID tài khoản (required)',
				),
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_user',
				),
			);
			$this->r['user_create'] = array(
			'url' => base_url('user/create?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'Description' => 'Phương thức GET là để Thêm user, ',
				'param' => array(
					'token' => 'Token API Reseller',
					'email' => 'email tài khoản (required)',
					'username' => 'tên người dùng không dưới 6 ký tự (required)',
					'password' => 'mật khẩu không dưới 8 ký tự  <= 63 ký tự (required)',
					'full_name' => 'họ và tên (required)',
					'phone' => 'Số điện thoại',
					'birthday' => 'Ngày sinh',
					'address' => 'Địa chỉ, dãy nhà, dãy phố, số nhà, số đường, tên đường',
					'country' => 'Quốc gia',
					'city' => 'Thành phố',
					'auth' => 'Mật khẩu cấp 2, (mật khẩu giao dịch)',
					
				),
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_user',
				),
			);
			$this->r['user_update'] = array(
			'url' => base_url('user/update?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'Description' => 'Phương thức GET là để cập nhập thông tin user, ',
				'param' => array(
					'token' => 'Token API Reseller',
					'id_user' => 'ID tài khoản (required)',
					'email' => 'email tài khoản (required)',
					'username' => 'tên người dùng không dưới 6 ký tự (required)',
					'password' => 'mật khẩu không dưới 8 ký tự  <= 32 ký tự (required)',
					'full_name' => 'họ và tên (required)',
					'phone' => 'Số điện thoại',
					'birthday' => 'Ngày sinh',
					'address' => 'Địa chỉ, dãy nhà, dãy phố, số nhà, số đường, tên đường',
					'country' => 'Quốc gia',
					'city' => 'Thành phố',
					'auth' => 'Mật khẩu cấp 2, (mật khẩu giao dịch)',
					'avatar' => 'URL images',
				),
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_user',
				),
			);
			$this->r['user_del'] = array(
			'url' => base_url('user/del?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'Description' => 'Phương thức GET là để xóa user, ',
				'param' => array(
					'token' => 'Token API Reseller',
					'id_user' => 'ID tài khoản (required)',
					
				),
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_user',
				),
			);
			$this->r['user_info'] = array(
			'url' => base_url('user/info?'.$this->apps->_api_name().'={your_secret_key}'),
				'method' => 'GET',
				'Description' => 'Phương thức GET là để xem thông tin user duy nhất, ',
				'param' => array(
					'token' => 'Token API Reseller',
					'client_id' => 'ID tài khoản (required)',
					
				),
				'command' =>  array(
							'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
							'secret_key' => '(string) secret key //YOUR API CREATED',
							),
				'response'=> array(
					'status'=> '(int) status code',
					'transaction_id'=> '(string) Transaction ID mã giao dịch api sử dụng để kiểm tra giao dịch',
					'data' => 'info_user',
				),
			);
		}
		
		$this->response($this->r);
	}
	
	
	
	
	
}

?>

