<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/6/28
 * Time: 10:59
 */

namespace app\common\payment;

class QianyouBase extends DaifuBase
{
    //商户自身系统的回调地址，包含完整地址，且api交互之前，需要把这个地址交由代付系统技术更新到对应账号，方可生效

    //商户系统 - 私钥路径
    protected $paivateCert = './cert/qianyou/rsa_private_key.pem';
    //代付系统 - 公钥路径
    protected $adminPubCert = './cert/qianyou/adminfu_public_key_2048.pem';

    /**
     * @param $data 异步通知数据
     * @return bool
     */
    protected function checkSign($data)
    {
        $sign = $data['Signature'];
        unset($data['Signature']);
        $data = $this->createDate($data) . $this->account->params->screctKey;
        $pubKey = file_get_contents($this->adminPubCert);
        $res = openssl_get_publickey($pubKey);
        $result = (bool) openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    protected function createDate($data)
    {
        $data = http_build_query($data);
        preg_match_all('/%\w{2}/', $data, $arr);

        foreach ($arr[0] as $v) {
            $data = str_replace($v, strtolower($v), $data);
        }
        return $data;
    }

    protected function curlPost($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($curl, CURLOPT_USERPWD, 'ka20Api_User:mk209Ae*@u');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($curl);

        if ($result === false) {
            $result = curl_errno($curl);
        }
        curl_close($curl);
        return $result;
    }

    public function notify($params, $cash)
    {
        if ($this->checkSign($params)) {
            //验签成功，此处业务逻辑
            if (strtolower($params['Success']) == 'true') {
                //代付成功
                $cash->status = 1; //审核通过
                $cash->complete_at = $_SERVER['REQUEST_TIME'];
                $cash->save();
                // 记录用户金额变动日志
                $reason = "申请提现成功，提现金额{$cash->money}元，手续费{$cash->fee}元，实际到账{$cash->actual_money}元";
                record_user_money_log('cash_success', $cash->user_id, 0, $cash->user->money, $reason);
                echo 'True';
                exit();
            } else {
                echo '代付失败，' . $params['Message'];
            }
        }

        echo 'False';
        exit();
    }
}
