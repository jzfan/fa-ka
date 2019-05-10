<?php

namespace app\common\pay;

use app\common\Yun;

/**
 * 微信免签
 * Class YunWx
 * @package app\common\pay
 */
class YunWx extends Yun
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return mixed
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->data['type'] = 3;
        $this->data['money'] = sprintf('%.2f', $totalAmount);
        $this->data['data'] = $outTradeNo;

        return $this->request();
    }
}