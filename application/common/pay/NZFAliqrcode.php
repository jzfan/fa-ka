<?php

namespace app\common\pay;

use app\common\NZF;

class NZFAliqrcode extends NZF
{

    function order($outTradeNo, $subject, $totalAmount)
    {
        return $this->requestForm('Aliqrcode', 'NZFAliqrcode', $outTradeNo, $subject, $totalAmount);
    }
}
