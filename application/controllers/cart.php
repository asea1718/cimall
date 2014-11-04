<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('shopcart');
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
		
		$tid = (int)$this->input->post('tid');
		$pid = (int)$this->input->post('pid');
		$name = trim($this->input->post('name'));
		$price = trim($this->input->post('price'));
		$num = (int)$this->input->post('num');
		$img = trim($this->input->post('img'));
		# $id,$name,$price,$num,$img
		$this->shopcart->addItem($tid,$pid,$name,$price,$num,$img);
		echo 1;
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
	 * 购买页面
	 */
	public function buy(){
		$this->index();
	}

	/**
	 * 提交订单
	 */
	public function submitOrder(){
		$this->load->view('tijiao.html');
	}

	/**
	 * 去支付页面
	 */
	public function toPay(){
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