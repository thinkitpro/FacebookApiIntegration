<?php


require(APPPATH.'libraries/facebook/facebook.php');

class fb
{
	private $_obj;
	private $_api_key = NULL;
	private $_secret_key = NULL;
	
	private $_login = "Login";
	private $_logout = "Logout";
	
	private $_user = NULL;
	private $_user_data = NULL;
	
	private $fb_sdk = FALSE;
	
	public function __construct()
	{
		$this->_obj =& get_instance();
		$this->_obj->load->config('facebook');
		$this->_obj->load->library('session');
		$this->_obj->load->helper('cookie');
		$this->_obj->load->helper('url');
		$this->_api_key = $this->_obj->config->item('facebook_api_key');
		$this->_secret_key = $this->_obj->config->item('facebook_secret_key');
		
		$this->fb_sdk = new Facebook_SDK(array(
			'appId' => $this->_api_key,
			'secret' => $this->_secret_key,
		));
		
		if($this->_obj->input->get('logout')) {
			$this->_obj->session->sess_destroy();
			delete_cookie('PHPSESSID');
			redirect(site_url('Facebook'),'refresh');
			$this->_user = null;
		}
		
		$this->_user = $this->fb_sdk->getUser();
		$this->initilize();
	}
	
	private function initilize()
	{
		if($this->_user){
			try {
				$this->_user_data = $this->fb_sdk->api('/me');
			} catch (FacebookApiException $e) {
				error_log($e);
				$this->_user = null;
			}
		}
	}
	
	public function set_button_text($login=null, $logout=null)
	{
		$this->_login = $login;
		$this->_logout = $logout;
	}
	
	public function get_url($array=false)
	{
		if($array==true){
			$ret = array();
			if($this->_user){
				$ret['url'] = $this->fb_sdk->getLogoutUrl();
				$ret['text'] = $this->_logout;
			} else {
				$ret['url'] = $this->fb_sdk->getLoginUrl();
				$ret['text'] = $this->_login;
			}
			return $ret;
		} else {
			if($this->_user){
				return $this->fb_sdk->getLogoutUrl();
			} else {
				return $this->fb_sdk->getLoginUrl();
			}
		}
	}
	
	public function getUserData($what=null)
	{
		if($what!=null && !is_array($what))
		{
			$d = array();
			foreach ($this->_user_data as $key => $value) {
				if($key == $what){
					return $value;
				}
			}
		} elseif($what!=null && is_array($what)) {
			$data = array();
			foreach($what as $val){
				if($this->_user_data[$val]){
					if($val=="location") {
						$data[$val] = $this->_user_data[$val]["name"];
					} else {
						$data[$val] = $this->_user_data[$val];
					}
				}
			}
			return $data;
		} else {
			//return $this->_user_data['name'];
			return $this->_user_data;
		}
	}
	
	public function LoginStatus()
	{
		return $this->fb_sdk->getAccessToken();
	}
}

?>