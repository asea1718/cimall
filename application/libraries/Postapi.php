<?php

	class Postapi{
		const AUTHCODE_URL = '/messageRecord/authCode';
	    const REGISTER_URL = '/user/register';
	    const LOGIN_URL = '/user/userLogin';

	    const STORYLISTS_URL = '/store/getStoreListForPaginationByLngAndLatAndDistance';
	    const PRODUCTTYPELIST_URL = '/producttype/getProductTypeListExceptProductCountZero';
	    const PRODUCTLIST_URL = '/product/getProductListForPaginationByProductTypeId';
	    const PRODUCTLIST_SEARCH_URL = '/product/getProductListForPaginationBystoreIdAndProductName';

	    const COMMITORDER_URL = '/order/commitOrder';
	    const MYORDERLIST_URL = '/order/getOrderListForPagination';
	    const ORDERDETAIL_URL = '/order/getOrderDetail';

	    const POINTLIST_URL = '/point/getPointListForPaginationByUserId';
	    const VERIFYCOUPON_URL = '/coupon/verifyCoupon';
	    const MYCOUPONLIST_URL = '/coupon/getCouponListForPaginationByUserId';
	    private $_baseUrl = 'http://www.yikego.com:8080/ykcore';
	    private $_post = array();
	    private $_curl;
	    public $format = 'json';

	    public function __construct(){
	        
	    }

	    /**
	     * 获取便利店商品分类
	     * @return mixed
	    */
	    public function getProductTypeList($postdata = array()){
	        $url = $this->_baseUrl.self::PRODUCTTYPELIST_URL;
	        return $this->_postData($url, json_encode($postdata));
	    }	

	    /**
	     * 在便利店里，根据商品名称模糊查询商品列表
	     * @return mixed
	     */
	    public function getProductListForSearch($postdata = array()){
	        $url = $this->_baseUrl.self::PRODUCTLIST_SEARCH_URL;
	        return $this->_postData($url, json_encode($postdata));
	    }   

	    public function _postData($url, $data = array()){
	    	$ch = curl_init($url);
	    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");	    	
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	    	$rs = curl_exec($ch);
	    	curl_close($ch);
	    	return $rs;
	    }
	}