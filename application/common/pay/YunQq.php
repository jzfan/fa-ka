<?php

namespace app\common\pay;

use app\common\Yun;

/**
 * QQ免签
 * Class YunQq
 * @package app\common\pay
 */
class YunQq extends Yun
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return mixed
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->data['type'] = 2;
        $this->data['money'] = sprintf('%.2f', $totalAmount);
        $this->data['data'] = $outTradeNo;

        return $this->request();
    }
}