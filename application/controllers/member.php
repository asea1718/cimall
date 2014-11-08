<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {
	public function __construct(){
		parent::__construct();
		# 
		$this->load->library('yike');
		//获取当前页面的url参数
		$backurl = base_url().ltrim($_SERVER['REQUEST_URI'], '/');
		//判断是否登陆了
		if(!$this->session->userdata('uphone')){
			$data = array(
				'message' => '您还没登陆！',
				'time'    => '1',
				'goto'    => site_url('home/login').'?'.$backurl
				);
			$this->load->view('show_message.html', $data);
			return;
		}		
	}

	public function index(){
		$this->ddcx();
	}

	public function ddcx(){
		$segments = $this->uri->uri_to_assoc();
		//分页显示数
		$pagecount = 5;
		$data['page'] = $page = isset($segments['page']) ? (int)$segments['page'] : 1;
		$data['haspage'] = 1;

		$postdata = array(
			"pageCount" => $pagecount,
		    "userId" => 1,
		    "nowPage" => $page
			);		
		$orders = $this->yike->getOrderList($postdata);	
		//判断是否还有分页
		if($orders['totalCount'] <= ($pagecount * $page))
			$data['haspage'] = 0;	
		$data['orders'] = $orders;
		$data['url'] = site_url('member/ddcx');
		//prpre($data);exit;
		$this->load->view('checkitem.html', $data);
	}

	/*
	 * 订单详情页
	 */
	public function order($orderid = 0){			
		$postdata = array(
			"orderNo" => trim($orderid)
			);		
		$data = $this->yike->getOrderDetail($postdata);
		//prpre($data);prpre(get_object_vars($data['productSubtotal']));exit;
		
		$this->load->view('order_detail.html', $data);

	}

	/*
	 * 我的积分
	 */
	public function wdjf(){
		$postdata = array(
			"pageCount" => 25,
		    "userId" => 1,
		    "nowPage" => 1
			);		
		$data = $this->yike->getPointList($postdata);
		//prpre($data);exit;
		$this->load->view("wodejifen.html", $data);
	}

	/*
	 * 我的优惠券
	 */
	public function wdyhq(){
		$data = array();
		for($i=1;$i<20;$i++){
		$postdata = array(
			"userId" => $i,
		    "pageCount" => 5,
		    "nowPage" => 2
			);		
		$data[] = $this->yike->getCouponList($postdata);
		}
		//prpre($data);exit;
		$this->load->view('wodegouwuquan.html', $data);
	}

	/*
	 * 验证购物券
	 */
	public function yzyhq(){
		$uid = (int)$this->session->userdata('uid');
		$yhq = $this->input->post('yhq');
		$postdata = array(
			"userId" => 1,
    		"couponNo" => $yhq  // 007004406    307004406
			);		
		$rs = $this->yike->verifyCoupon($postdata);	
		// resultCode  0 验证成功 -1 不存在优惠劵  -2 已验证 -3  已使用 -4  取消  -5 过期	
		//prpre($rs);exit;
		if(!empty($rs)){
			switch ($rs['resultCode']) {
				case  0:	echo urlencode('验证成功');	break;
				case -1:	echo urlencode('不存在该优惠劵');	break;
				case -2:	echo urlencode('已经验证');	break;
				case -3:	echo urlencode('已经使用');	break;
				case -4:	echo urlencode('已经取消');	break;
				case -5:	echo urlencode('已经过期');	break;				
				default:	echo urlencode('不存在该优惠劵');	break;
			}
		}else{
			echo urlencode('不存在该优惠劵');
		}
	}

	/*
	 * 使用优惠券
	 */
	public function syyhq(){
		$this->load->view('shiyongyouhuiquan.html');
	}

	/*
	 * 验证购物券
	 */
	public function yzyhq1(){
		$uid = (int)$this->session->userdata('uid');
		$yhq = $this->input->post('yhq');
		$postdata = array(
			"userId" => 1,
    		"couponNo" => $yhq  // 
			);		
		$rs = $this->yike->verifyCoupon($postdata);	
		// resultCode  0 验证成功 -1 不存在优惠劵  -2 已验证 -3  已使用 -4  取消  -5 过期	
		//prpre($rs);exit;
		if(!empty($rs)){
			$yhqinfo = array();
			$yhqinfo = get_object_vars($rs['coupon']);
			$yhqinfo['statuscode'] = $rs['resultCode'];
			switch ($rs['resultCode']) {
				case  0:	$yhqinfo['status'] = '验证成功';	break;
				case -1:	$yhqinfo['status'] = '不存在该优惠劵';	break;
				case -2:	$yhqinfo['status'] = '已经验证';	break;
				case -3:	$yhqinfo['status'] = '已经使用';	break;
				case -4:	$yhqinfo['status'] = '已经取消';	break;
				case -5:	$status = '已经过期';	break;				
				default:	$yhqinfo['status'] = '不存在该优惠劵';	break;
			}
			echo json_encode($yhqinfo, true);
		}else{
			echo json_encode(array('statuscode' => '9'), true);
		}
	}

}