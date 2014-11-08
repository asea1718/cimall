<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('yike');
		$this->load->library('shopcart');
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
	public function single(){		
		$segments = $this->uri->uri_to_assoc();
		if(empty($segments))
			redirect('home');
		$storeid = (int)$segments['sid'];
		$tid = isset($segments['tid']) ? (int)$segments['tid'] : 0;

		$page = isset($segments['page']) ? (int)$segments['page'] : 1;	
		//每页显示数量
		$pagecount = 25;
		$data['page'] = $page;	
		// 购物车商品
		$data['carts'] = $this->shopcart->getAllItem();		
		//获取商品总数num
		$data['sumnum'] = $this->shopcart->getNum();

		$postdata = array(
			"storeId" => (int)$storeid
			);		
		$rs = $this->yike->getProductTypeList($postdata);
		$pinfos = array();
		$fenlei = array();
		//有分页 haspage为1，默认为1
		$data['haspage'] = 1;
		if($tid == 0){
			if(!empty($rs['productTypeList'])){
				foreach($rs['productTypeList'] as $ptl){
					$ptl = get_object_vars($ptl);		
					$fenlei[] = $ptl;		
					$postdata1 = array(
						"productTypeId" => $ptl['productTypeId'],
					    "nowPage" => $page,
					    "pageCount" => $pagecount
						);
					$ptl['goods'] = $this->yike->getProductList($postdata1);

					$pinfos[] = $ptl;
					$data['haspage'] = 0;
				}
			}	
			$data['url'] = site_url('store/single')."/sid/".$storeid;		
		}else{
			if(!empty($rs['productTypeList'])){
				foreach($rs['productTypeList'] as $ptl){
					$ptl = get_object_vars($ptl);		
					$fenlei[] = $ptl;	
					if($ptl['productTypeId'] == $tid){
						$postdata1 = array(
							"productTypeId" => $tid,
						    "nowPage" => $page,
					    	"pageCount" => $pagecount
							);
						$ptl['goods'] = $this->yike->getProductList($postdata1);
						// 判断时候还有分页
						if($ptl['goods']['totalCount'] <= ($page*$pagecount)){
							$data['haspage'] = 0;
						}
						$pinfos[] = $ptl;						
					}				
				}
			}	
			$data['url'] = site_url('store/single')."/sid/".$storeid.'/tid/'.$tid;		
		}
		$data['tid'] = $tid;
		$data['storeid'] = $storeid;		
		$data['fenlei'] = $fenlei;
		$data['pinfos'] = $pinfos;		
		//prpre($data);exit;
		$this->load->view("store_index.html", $data);
	}		

	/**
	 * 商品详情页
	 * @return [type] [description]
	 */
	public function singleGood(){		
		$segments = $this->uri->uri_to_assoc();
		if(empty($segments)) 
			redirect('store');		
		$data['storeid'] = $storeid = (int)$segments['sid'];
		$tid = (int)$segments['tid'];
		$pid = (int)$segments['pid'];
		// 购物车商品
		$data['carts'] = $this->shopcart->getAllItem();	
		//获取商品总数num
		$data['sumnum'] = $this->shopcart->getNum();
		// 获取本店商品分类
		$postdata = array(
			"storeId" => $storeid
			);	
		
		$rs = $this->yike->getProductTypeList($postdata);			

		//获取随机商品和要显示的商品
		foreach($rs['productTypeList'] as $ptl){
			$ptl = get_object_vars($ptl);		
			$data['fenlei'][] = $ptl;
			$postdata1 = array(
				"productTypeId" => $ptl['productTypeId'],
			    "nowPage" => 1,
			    "pageCount" => 25
				);
			$gg = $this->yike->getProductList($postdata1);	
			foreach($gg['productList'] as $g){
				$g = get_object_vars($g);
				if($g['productId'] == $pid && $ptl['productTypeId'] == $tid)				
					$data['good'] = $g;
				else
					$allgoods[] = $g; //$data['randgoods'][] = $g;					
			}				
		}
		//prpre(array_rand($allgoods, 2));exit;
				
		if(count($allgoods) < 7)  //7
			$data['randgoods'] = $allgoods;
		else{
			$randkey = array_rand($allgoods, 6);  //6
			foreach($randkey as $v){
				$data['randgoods'][] = $allgoods[$v];
			}
		}
		//prpre($data['randgoods']);exit;
		$this->load->view("goods_index.html", $data);		
	}

	/**
	 * 商品搜索
	 */
	public function goodSearch(){
		$segments = $this->uri->uri_to_assoc();
		$kw = urldecode($segments['keyword']);
		$data['storeid'] = $sid = (int)$segments['sid'];  
		$page = isset($segments['page']) ? (int)$segments['page'] : 1;	
		//每页显示数量
		$pagecount = 25;
		$data['page'] = $page;	
		//prpre($segments);exit;
		$postdata = array(
			"pageCount" => $pagecount,
		    "nowPage" => $page,
		    "productName" => $kw,
		    "storeId" =>   $sid
			);
		$data['goods'] = $this->yike->getProductListForSearch($postdata);
		$data['haspage'] = 1;
		if($data['goods']['totalCount'] <= ($page*$pagecount)){
			$data['haspage'] = 0;
		}
		
		// 购物车商品
		$data['carts'] = $this->shopcart->getAllItem();
		//获取商品总数num
		$data['sumnum'] = $this->shopcart->getNum();

		$data['url'] = site_url('store/goodSearch')."/sid/".$sid."/keyword/".$kw;

		//prpre($data);exit;
		$this->load->view('search_goods.html', $data);
	}

	/**
	 * 商店搜索
	 */
	public function search($kw = ''){		
		$data['keyword'] = urldecode($kw);
		
		$this->load->view("store_search.html", $data);
	}

	/**
	 * 点击分类返回内容
	 */
	public function fenlei(){
		//$storeid = (int)$this->input->post('sid');
		$tid = (int)$this->input->post('tid');
		// 购物车商品
		$data['carts'] = $this->shopcart->getAllItem();	

		$postdata = array(
			"productTypeId" => $tid,
		    "nowPage" => 1,
		    "pageCount" => 25
			);
		$ptl['goods'] = $this->yike->getProductList($postdata);
	}
}