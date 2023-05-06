<?php

namespace Gymers\LianlianPay\Exception;

class PayTypeException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct('Invalid Pay Type:'.$message);
    }
}
