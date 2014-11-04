<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Good extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('yike');
	}

	public function index(){
		$rs = array();
		for($i=1;$i<30;$i++){
			$postdata = array(
				"productTypeId" => $i,
			    "nowPage" => 1,
			    "pageCount" => 25
				);
			$rs[] = $this->yike->getProductList($postdata);
		}
		//prpre($rs);exit;
		$this->load->view("search_goods.html");
	}

	public function single(){
		$segments = $this->uri->uri_to_assoc();
		if(empty($segments)) 
			redirect('store');		
		$tid = (int)$segments['tid'];
		$pid = (int)$segments['pid'];
					
		$postdata1 = array(
			"productTypeId" => $tid,
		    "nowPage" => 1,
		    "pageCount" => 25
			);
		$ptl = $this->yike->getProductList($postdata1);
		$good = array();
		if(!empty($ptl['productList'])){
			foreach($ptl['productList'] as $v){
				$v = get_object_vars($v);
				if($v['productId'] == $pid){
					$good = $v;
					break;
				}
			}
		}
		//prpre($good);exit;
		$data['good'] = $good;

		$this->load->view("goods_index.html", $data);
	}

	public function search(){
		$this->load->view("search_goods.html");
	}

}