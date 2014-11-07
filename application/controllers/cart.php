<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('shopcart');
		$this->load->library('yike');
	}

	public function index(){
		$data['totalprice'] = $this->shopcart->getPrice();
		$data['allitems'] = $this->shopcart->getAllItem();

		//prpre($data);exit;
		$this->load->view('cart.html', $data);
	}

	public function finish(){
		$this->load->view('finish_cart.html');
	}

	/*
	 * 添加购物车
	 */
	public function addCart(){
		
		$sid = (int)$this->input->post('sid');
		$tid = (int)$this->input->post('tid');
		$pid = (int)$this->input->post('pid');
		$name = trim($this->input->post('name'));
		$price = trim($this->input->post('price'));
		$num = (int)$this->input->post('num');
		$img = trim($this->input->post('img'));
		$desc = trim($this->input->post('desc'));
		# $id,$name,$price,$num,$img
		$this->shopcart->addItem($sid,$tid,$pid,$name,$price,$num,$img,$desc);
		$item = $this->shopcart->getItem($pid);
		$rt['num'] = $item['num'];
		$rt['sumnum'] = $this->shopcart->getNum();		
		echo json_encode($rt, true);
	}
	/**
	 * 完成购物车
	 */
	public function finishCart(){
		$this->load->view('success_cart.html');
	}
	
	/**
	 * 删除单个商品购物车
	 */
	public function delOne(){
		$id = (int)$this->input->post('pid');
		$this->shopcart->delItem($id);
		echo 1;
	}

	/**
	 * 更新单个商品购物车
	 */
	public function updateOne(){
		$id = (int)$this->input->post('pid');
		$num = (int)$this->input->post('num');
		$this->shopcart->modNum($id,$num);
		$item = $this->shopcart->getItem($id);
		if(!empty($item)){
			echo $item['num'];
		}else{
			echo 0;
		}
	}

	/**
	 * 获取单个商品信息
	 */
	public function getOne(){
		$pid = (int)$this->input->post('pid');
		$item = $this->shopcart->getItem($pid);
		if(!empty($item)){
			echo $item['num'];
		}else{
			echo 0;
		}
	}
	/**
	 * 操作 增加/减少按钮 动作更新购物车
	 */
	public function delNum(){
		$action = trim($this->input->post('action'));
		$id = trim($this->input->post('pid'));
		if($action == 'add'){
			$this->shopcart->incNum($id,1);
		}elseif($action == 'dec'){
			$this->shopcart->decNum($id,1);
		}
	}
	/**
	 * 获取购物车中商品的总数num
	 */
	public function getAllNum(){
		echo $this->shopcart->getNum();
	}

	/**
	 * 购买页面
	 */
	public function buy(){
		$this->index();
	}

	/**
	 * 提交订单
	 */
	public function delOrder(){
		//prpre($this->input->post());exit;
		$pids = $this->input->post('pids');
		$yhqs = $this->input->post('yhqs');
		$zffs = $this->input->post('zffs');
		$pids = $this->input->post('pids');

		$subject = $body = '';


		$userid = $this->session->userdata('uid');
		switch ($zffs) {
			case 'hdfk': $orderType = 1;break;
			case  'zfb': $orderType = 2;break;			
			default:     $orderType = 1;break;
		}

		# 
		// 购物车商品
		$carts = $this->shopcart->getAllItem();	
		foreach($pids as $id){
			$orderDetailList[] = array(
				"productId" => $id,
            	"count" => $carts[$id]['num']
				); 
			$subject .= $carts[$id]['name'];
			$body .= $carts[$id]['desc'];
		}		

		# 提交订单
		$postdata = array(
			"storeId" => 1,                //便利店ID
		    "userId" => $userid,           //userId 用户ID
		    "orderType" => $orderType,     //订单类型1 货到付款 2 在线支付
		    "subject" => $subject,         //商品名称（多个商品名称可以叠加长度256）
		    "body" => $body,               //商品描述（多个商品描述可以叠加长度400）
			"orderStatus" => 0,            //固定值0
			"orderSource" => 3,            //提交订单来源1 android 2 ios 3 web
		    "orderDetailList" => $orderDetailList  //订单商品列表
		    //"couponList" => $couponList             //优惠券列表
			);
		if(!empty($yhqs)){			
			foreach($yhqs as $k => $yhq){				
				$postdata['couponList'][] = array("couponNo" => $yhq);
				
			}
		}
		$rs = $this->yike->commitOrder($postdata);
		//prpre($postdata);prpre($rs);exit;
		# resultCode  0 提交订单成功 -1 异常 -2 加盟店或者商品不存在 -3 优惠
		if($rs['resultCode'] == 0 || $rs['resultCode'] == -3){
			foreach($pids as $id){
				$this->shopcart->delItem($id);
			}
			$this->load->view('success_order.html', $rs);
			return;
		}elseif($rs['resultCode'] == -2){
			$data = array(
			'message' => '加盟店或者商品不存在！',
			'time'    => '2',
			'goto'    => site_url('cart')
			);
			$this->load->view('show_message.html', $data);
			return;
		}else{
			$data = array(
			'message' => '提交订单异常！',
			'time'    => '2',
			'goto'    => site_url('cart')
			);
			$this->load->view('show_message.html', $data);
			return;
		}
	}

	/**
	 * 去支付页面
	 */
	public function toPay(){
		// 判断是否登录了
		$backurl = site_url().ltrim($_SERVER['REQUEST_URI'], '/');
		if(!$this->session->userdata('uphone')){
			$data = array(
				'message' => '登陆后才能进行购买！',
				'time'    => '2',
				'goto'    => site_url('home/login').'?'.$backurl
				);
			$this->load->view('show_message.html', $data);
			return;
		}
		if(!$this->input->post())
			redirect('cart');
		$pids = $this->input->post('p_id');
		//prpre($this->input->post('p_id'));exit;
		$items = array();
		foreach($pids as $pid){
			$items[] = $this->shopcart->getItem($pid);
		}
		$data['items'] = $items;
		//prpre($items);exit;
		$this->load->view('to_pay.html', $data);
	}

	/////////////////////////////

	public function test(){
		//$this->shopcart->addItem('10101','测试session','15.5','5','http://cimall.dev/assets/images/3_37.jpg');
		$rs = $this->shopcart->getAllItem();
		prpre($rs);
	}
	public function test1(){
		$this->shopcart->clear();
		
	}

}