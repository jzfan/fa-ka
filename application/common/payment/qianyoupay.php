<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/6/28
 * Time: 10:59
 */

namespace app\common\payment;

use think\Request;

class qianyoupay extends QianyouBase
{
    /**
     * @param $OrderNO 必填    接入商自己产生订单号（务超过21位）
     * @param $BankCardNO 必填    收款人的银行卡号
     * @param $ChineseName 必填    收款人姓名（需要进行URL编码）
     * @param $BankType 选填    开户行银联号
     * @param $Amount 必填    金额（单位元，精确度到分）
     * @param $ExtParam 选填    自定义扩展参数（需要进行URL编码）
     */
    public function pay($cash)
    {
        // 请注意这里的回调地址不能带有大写字符，必须纯小写，否则会验证签名失败！注意对应的类也必须要小写才能正确回调！
        $notifyUrl = Request::instance()->domain() . '/payment/qianyoupay/notify';

        $Amount = $cash->actual_money;

        $strParam = [
            'MerchNO' => $this->account->params->appKey,
            'OrderNO' => $cash->orderid,
            'BankCardNO' => $cash->bank_card,
            'ChineseName' => $cash->realname,
            'BankType' => '',
            'Amount' => $Amount,
            'NotifyUrl' => $notifyUrl,
            'ExtParam' => '',
        ];

        $strParamKey = $this->createDate($strParam) . urlencode($this->account->params->screctKey);

        $encrypted = '';
        $private_key = file_get_contents($this->paivateCert);
        $pi_key = openssl_pkey_get_private($private_key);
        $res = openssl_get_privatekey($pi_key);
        openssl_sign($strParamKey, $encrypted, $res);
        openssl_free_key($res);
        $encrypted = base64_encode($encrypted);

        $data = [
            'MerchNO' => $this->account->params->appKey,
            'OrderNO' => $cash->orderid,
            'BankCardNO' => $cash->bank_card,
            'ChineseName' => $cash->realname,
            'BankType' => '',
            'Amount' => $Amount,
            'NotifyUrl' => $notifyUrl,
            'ExtParam' => '',
            'Signature' => $encrypted,
        ];

        $url = 'http://payapi.adminfu.com/api/pay/PaySettleAccount';
        $data = $this->createDate($data);

        $res = json_decode($this->curlPost($url, $data), true);
        if (isset($res['Success']) && $res['Success'] == 'true') {
            return true;
        } else {
            $res['msg'] = $res['Message'];
            return $res;
        }
    }
}
