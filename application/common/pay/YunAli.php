<?php

namespace app\common\pay;

use app\common\Yun;

/**
 * 支付宝免签
 * Class YunAli
 * @package app\common\pay
 */
class YunAli extends Yun
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return mixed
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->data['type'] = 1;
        $this->data['money'] = sprintf('%.2f', $totalAmount);
        $this->data['data'] = $outTradeNo;

        return $this->request();
    }
}