<?php
// +----------------------------------------------------------------------
// | JuhePHP [ NO ZUO NO DIE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2015 http://juhe.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Juhedata <info@juhe.cn-->
// +----------------------------------------------------------------------

//----------------------------------
// 聚合数据-银行卡三元素检测API调用类
//----------------------------------
namespace Util\Verify;
class BankCard {
    private $verifybankcard = 'http://v.juhe.cn/verifybankcard3/query';

    public function __construct(){
        $this->appkey = config('juhe_config.APPKEY');
        $this->openid = config('juhe_config.OPPENID');
    }

    public function query($bankcard, $realname, $idcard, $isshow = 1){
    	$uorderid = $this->out_trade_no();
    	$params = 'key='.$this->appkey.'&bankcard='.$bankcard.'&realname='.urlencode($realname).'&idcard='.$idcard.'&uorderid='.$uorderid.'&isshow='.$isshow;
        $content = $this->juhecurl($this->verifybankcard, $params);
        $result = $this->_returnArray($content);
        return $result;
    }


    /**
     * 将JSON内容转为数据，并返回
     * @param string $content [内容]
     * @return array
     */
    public function _returnArray($content){
        return json_decode($content,true);
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

    public function out_trade_no() {

    	$rand = rand(1, 99999);
    	$str = '';
    	if (count($rand) < 8) {

    		for ($i = 0; $i < 8 - count($rand); $i++) {
    			$str .= '0';
    		}
    	}
    	return $str = date('YmdHis') . $str . $rand;
    }
}

?>
