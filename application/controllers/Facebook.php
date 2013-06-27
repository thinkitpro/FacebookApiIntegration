<?php


class Facebook extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library("fb");
	}
	
	public function index()
	{
		$this->fb->set_button_text("LogIn", "LogOut");
		$button = $this->fb->get_url(true);
		//$data = array("name","location","username","id","gender","email");
		//$user_data = $this->fb->getUserData($data);
		$user_data = $this->fb->getUserData();
		echo 'Button: <a href="'.$button['url'].'">'.$button['text'].'</a>';
		//echo "<br />".$this->fb->LoginStatus();
		echo "<pre>";
		var_dump($user_data);
		echo "</pre>";
	}
}

?>