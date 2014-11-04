<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {

	/*
	 * 关于我们
	 */
	public function aboutUs(){
		$data['a_class'] = 'gywm';
		$this->load->view('about/aboutus.html', $data);
	}

	/*
	 * 广告赞助
	 */
	public function ggzz(){
		$data['a_class'] = 'ggzz';
		$this->load->view('about/ggzz.html', $data);
	}

	/*
	 * 服务声明
	 */
	public function fwsm(){
		$data['a_class'] = 'fwsm';
		$this->load->view('about/fwsm.html', $data);
	}
	
	/*
	 * 免责申明
	 */
	public function mzsm(){
		$data['a_class'] = 'mzsm';
		$this->load->view('about/mzsm.html', $data);
	}
	
	/*
	 * 法律申明
	 */
	public function flsm(){
		$data['a_class'] = 'flsm';
		$this->load->view('about/flsm.html', $data);
	}
	
	/*
	 * 信息反馈
	 */
	public function xxfk(){
		$data['a_class'] = 'xxfk';
		$this->load->view('about/xxfk.html', $data);
	}
	
	/*
	 * 联系我们
	 */
	public function contactUs(){
		$data['a_class'] = 'lxwm';
		$this->load->view('about/contactus.html', $data);
	}
	
	/*
	 * 加入我们
	 */
	public function jionUs(){
		$data['a_class'] = 'jrwm';
		$this->load->view('about/jionus.html', $data);
	}

	/*
	 * 超市入驻
	 */
	public function csrz(){
		$data['a_class'] = 'csrz';
		$this->load->view('about/chaoshiruzhu.html', $data);
	}

}