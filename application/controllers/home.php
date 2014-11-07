<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();	
		$this->load->library('yike');
		//$this->load->library('postapi');
	}
	
	public function index(){
		$this->load->view('index.html');
	}

	
	public function search(){		
		$postdata = array(
			"pageCount" => 25,
			"nowPage" => 1,
			"productName" => "粮油",
			"storeId" => 1
			);		
		$sh = $this->yike->getProductListForSearch($postdata);		
		prpre($sh);
	}	
	public function map(){
		$this->load->view('map.html');
	}

	public function login(){
		if($this->session->userdata('uphone'))
			redirect('home');
		else{		
			//prpre(ltrim($_SERVER['REQUEST_URI'], '/'));exit;	
			$data['backurl'] = $_SERVER['QUERY_STRING'];			
			$this->load->view('login.html', $data);
		}			
	}

	public function doLogin(){		
		
		$lg['userPhone'] = trim($this->input->post('user_name'));
		$passwd = trim($this->input->post('password'));
		$backurl = trim($this->input->post('backurl')) == '' ? 'home' : trim($this->input->post('backurl'));
		$loginType = 1;
		$captchaCode = 'm5afl';	
		
		# loginType登录方式 1 密码登录 2 短信验证登录
		if($loginType == 1){
			$lg['loginType'] = 1;
			$lg['passWord'] = $passwd;
		}elseif($loginType == 2){
			$lg['loginType'] = 2;
			$lg['matchContent'] = $captchaCode;
		}			
		$rs = $this->yike->userLogin($lg);
		/*
		 * resultCode-1 用户名或密码不正确   -2 验证码不正确  
		 * -3 异常   0 登录成功   user 用户对象
		 * "userId": 1, "userPhone
		 */
		//prpre($rs);exit;
		if($rs['resultCode'] == 0){
			$ur = get_object_vars($rs['user']);
			$user = array(
				'uid' => $ur['userId'],
				'uphone' => $ur['userPhone']
				);
			$this->session->set_userdata($user);			
			$data = array(
				'message' => '登陆成功！',
				'time'    => '2',
				'goto'    => $backurl
				);
			$this->load->view('show_message.html', $data);
		}elseif($rs['resultCode'] == -1){
			$data = array(
				'message' => '用户名或密码不正确！',
				'time'    => '2',
				'goto'    => site_url('home/login').'?'.$backurl
				);
			$this->load->view('show_message.html', $data);
		}elseif($rs['resultCode'] == -2){
			$data = array(
				'message' => '验证码不正确！',
				'time'    => '2',
				'goto'    => site_url('home/login').'?'.$backurl
				);
			$this->load->view('show_message.html', $data);
		}
		
	}

	/*
	 * 退出登陆
	 */
	public function loginOut(){
		$user = array(
				'uid' => '',
				'uphone' => ''
				);
		$this->session->unset_userdata($user);
		$backurl = $_SERVER['QUERY_STRING'];
		$data = array(
			'message' => '退出成功！',
			'time'    => '1',
			'goto'    => $backurl
			);
		$this->load->view('show_message.html', $data);
	}

	/*
	 * 注册页面
	 */
	public function register(){
		if($this->session->userdata('uphone'))
			redirect('home');
		else{
			$data['backurl'] = $_SERVER['QUERY_STRING'];
			$this->load->view('register.html', $data);
		}			
	}	

	/*
	 * 处理注册信息
	 */
	public function doRegister(){

		$backurl = trim($this->input->post('backurl')) == '' ? 'home' : trim($this->input->post('backurl'));
			
		if($this->input->post('re_accountPasswd') != $this->input->post('accountPasswd')){
			$data = array(
			'message' => '两次输入密码不一致！',
			'time'    => '2',
			'goto'    => site_url('home/register').'?'.$backurl
			);
			$this->load->view('show_message.html', $data);
			return;
		}			
		
		# 验证码
		$auth['matchContent'] = trim($this->input->post('captchaCode'));
		# 用户信息
		$auth['user']['userPhone'] = trim($this->input->post('accountName'));
		$auth['user']['passWord'] = trim($this->input->post('accountPasswd'));	
		$auth['user']['userAddress'] = trim($this->input->post('address'));
		
		/*		
		 $auth = array(
			"user" => array(
				"userPhone" => "15816825058",
		        "passWord" => "147258",
		        "userAddress" => "深圳市御桥路98号"
				),
			"matchContent" => "nkb08"
			);	
		
		*/
		//prpre($auth);		
		$rs = $this->yike->register($auth);
		//prpre($rs);
		/*
		 * resultCode-1 已注册  -2 验证码不正确  -3 异常  0注册成功
		 * userId用户ID
		 */
		/*if($rs['resultCode'] == 0){
			echo '注册成功！';
			$this->load->view('success_register.html');
		}*/
		if($rs['resultCode'] == 0){			
			$user = array(
				'uid' => $rs['userId'],
				'uphone' => $auth['user']['userPhone']
				);
			$this->session->set_userdata($user);			
			$data = array(
			'message' => '注册成功！',
			'time'    => '2',
			'goto'    => $backurl
			);
			$this->load->view('show_message.html', $data);
		}elseif($rs['resultCode'] == -2){
			$data = array(
			'message' => '验证码不正确！',
			'time'    => '2',
			'goto'    => site_url('home/register').'?'.$backurl
			);
			$this->load->view('show_message.html', $data);
		}elseif($rs['resultCode'] == -1){
			$data = array(
			'message' => '已经注册过了！',
			'time'    => '2',
			'goto'    => site_url('home/login').'?'.$backurl
			);
			$this->load->view('show_message.html', $data);
		}else{
			redirect('home/register');
		}
	}	

	/*
	 * 短信验证函数
	 */
	public function sendPhone(){
		//echo 0;exit;
		if(!$this->input->post()) redirect('home');
		$info = trim($this->input->post('info'));
		$phone = trim($this->input->post('phone'));
		if($info == 'register'){
			$messageRecordType = 1;
		}elseif($info == "login"){
			$messageRecordType = 2;
		}else{
			redirect('home/login');
		}
		$postdata = array(
			"messageRecordType" => $messageRecordType,
			"userPhone" => $phone
			);		
		# resultCode -1 异常-2 超过发送次数 0 发送成功
		$rs = $this->yike->authCode($postdata);
		//prpre($rs);
		echo $rs['resultCode'];
	}



	////test/////
	public function tt(){
		echo $this->session->userdata('uid');
		//echo $this->session->userdata('uphone');
	}
	public function test1(){
		echo $_COOKIE['myname'];
	}

	public function sm(){
		$data = array(
			'message' => '登陆成功！',
			'time'    => '4',
			'goto'    => site_url('home')
			);
		$this->load->view('show_message.html', $data);
	}

	public function dl(){		
		
		$lg['userPhone'] = '13162330272';
		$passwd = '';
		$loginType = 2;
		$captchaCode = 'kzhzm';	
		
		# loginType登录方式 1 密码登录 2 短信验证登录
		if($loginType == 1){
			$lg['loginType'] = 1;
			$lg['passWord'] = $passwd;
		}elseif($loginType == 2){
			$lg['loginType'] = 2;
			$lg['matchContent'] = $captchaCode;
		}			
		$rs = $this->yike->userLogin($lg);
		/*
		 * resultCode-1 用户名或密码不正确   -2 验证码不正确  
		 * -3 异常   0 登录成功   user 用户对象
		 * "userId": 1, "userPhone
		 */
		echo $lg['userPhone']."<br>".md5(111111)."<br>".md5(123456);
		prpre($rs);exit;	
	}	

	public function sp(){		
		$info = 'login';
		$phone = '13162330272';
		if($info == 'register'){
			$messageRecordType = 1;
		}elseif($info == "login"){
			$messageRecordType = 2;
		}else{
			redirect('home/login');
		}
		$postdata = array(
			"messageRecordType" => $messageRecordType,
			"userPhone" => $phone
			);		
		# resultCode -1 异常-2 超过发送次数 0 发送成功
		$rs = $this->yike->authCode($postdata);
		//prpre($rs);
		echo $rs['resultCode'];
	}
	
}



