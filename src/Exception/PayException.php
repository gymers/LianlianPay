<?php

namespace Gymers\LianlianPay\Exception;

class PayException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct('Invalid Pay Function:'.$message);
    }
}
