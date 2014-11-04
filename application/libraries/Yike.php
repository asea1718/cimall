<?php

class Yike {
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
    public $isJson = true;

    public function __construct(){
        require_once('CurlHelp/CurlHelp.php');
        $this->_curl = new CurlHelp();
    }

    /**
     * 发送验证码
     * @return mixed
     */
    public function authCode(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::AUTHCODE_URL;
        return $this->_postJson($url);
    }

    /**
     * 用户注册
     * @return mixed
     */
    public function register(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::REGISTER_URL;
        return $this->_postJson($url);
    }

    /**
     * 用户登录
     * @return mixed
     */
    public function userLogin(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::LOGIN_URL;
        return $this->_postJson($url);
    }

    /**
     * 根据经纬度获取附近便利店
     * @return mixed
     */
    public function getStoreList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::STORYLISTS_URL;
        return $this->_postJson($url);
    }

    /**
     * 获取便利店商品分类
     * @return mixed
     */
    public  function getProductTypeList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::PRODUCTTYPELIST_URL;
        return $this->_postJson($url);
    }

    /**
     * 根据商品分类获取商品列表
     * @return mixed
     */
    public function getProductList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::PRODUCTLIST_URL;
        return $this->_postJson($url);
    }

    /**
     * 在便利店里，根据商品名称模糊查询商品列表
     * @return mixed
     */
    public function getProductListForSearch(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::PRODUCTLIST_SEARCH_URL;
        return $this->_postJson($url);
    }

    /**
     * 提交订单
     * @return mixed
     */
    public function commitOrder(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::COMMITORDER_URL;
        return $this->_postJson($url);
    }

    /**
     * 我的订单
     * @return mixed
     */
    public function getOrderList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::MYORDERLIST_URL;
        return $this->_postJson($url);
    }

    /**
     * 订单详细
     * @return mixed
     */
    public function getOrderDetail(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::ORDERDETAIL_URL;
        return $this->_postJson($url);
    }

    /**
     * 我的积分
     * @return mixed
     */
    public function getPointList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::POINTLIST_URL;
        return $this->_postJson($url);
    }

    /**
     * 优惠券验证
     * @return mixed
     */
    public function verifyCoupon(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::VERIFYCOUPON_URL;
        return $this->_postJson($url);
    }

    /**
     * 我的优惠券
     * @return mixed
     */
    public function getCouponList(array $data){
        $this->setPostData($data);
        $url = $this->_baseUrl.self::MYCOUPONLIST_URL;
        return $this->_postJson($url);
    }


/*********************************************************/
    private function _postJson($url){
        if(!$this->_curl){
            require_once('CurlHelp/CurlHelp.php');
            $this->_curl = new CurlHelp();
        }
        if($this->isJson){
            $this->_curl->setHeader('Content-Type','application/json;charset=utf-8');
        }
        $curl_error = $this->_curl->post($url,$this->_post);
        if($curl_error !== 0){
            die($this->_curl->error_message);
        }
        return get_object_vars($this->_curl->response); // 转换object类型为array        
    }

    public function setPostData(array $data){
        $this->_post = $this->isJson ? json_encode($data) : $data;
    }

    public function getPostData(){
        return $this->_post;
    }

    public function _destruct(){
        $this->_curl->close();
    }
}
