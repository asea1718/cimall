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

	

	public function search(){
		$this->load->view("search_goods.html");
	}

}