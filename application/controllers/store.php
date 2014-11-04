<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('yike');
		$this->load->library('postapi');
	}

	public function index(){	
		$postdata = array(
			"lng" => 121.38842553522,
		    "lat" => 31.202108422061,
			"distance" => 18.5,
			"name" => "便利店",
		    "nowPage" => 1,
		    "pageCount" => 25
			);
		$rs = $this->yike->getStoreList($postdata);
		//prpre($rs);exit;
		$this->load->view("index_search.html");
	}

	/**
	 * [single 单个商店页面商品显示]
	 * @param  integer $storeid [商店id]
	 * @return [type]           [array 商品分类+商品信息]
	 */
	public function single($storeid = 1){
		$postdata = array(
			"storeId" => (int)$storeid
			);		
		$rs = $this->yike->getProductTypeList($postdata);
		$pinfos = array();
		if(!empty($rs['productTypeList'])){
			foreach($rs['productTypeList'] as $ptl){
				$ptl = get_object_vars($ptl);				
				$postdata1 = array(
					"productTypeId" => $ptl['productTypeId'],
				    "nowPage" => 1,
				    "pageCount" => 25
					);
				$ptl['goods'] = $this->yike->getProductList($postdata1);

				$pinfos[] = $ptl;
			}
		}
		$data['storeid'] = (int)$storeid;
		$data['pinfos'] = $pinfos;
		//prpre($data);exit;
		$this->load->view("store_index.html", $data);
	}

	/**
	 * 商店搜索
	 */
	public function search($kw = ''){		
		$data['keyword'] = urldecode($kw);
		
		$this->load->view("store_search.html", $data);
	}

	/**
	 * 商品搜索
	 */
	public function goodSearch(){
		$segments = $this->uri->uri_to_assoc();
		$kw = urldecode($segments['keyword']);
		$sid = (int)$segments['sid'];
		//prpre($segments);exit;
		$postdata = array(
			"pageCount" => 25,
		    "nowPage" => 1,
		    "productName" => $kw,
		    "storeId" => $sid
			);
		$data['goods'] = $this->yike->getProductListForSearch($postdata);
		prpre($data['goods']);exit;
		$this->load->view('aa.html', $data);
	}
}