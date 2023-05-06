<?php

namespace Gymers\LianlianPay\Contracts;

interface GatewayPayInterface extends PayInterface
{
    public function orderQuery(array $arguments);

    public function refund(array $arguments);

    public function refundQuery(array $arguments);
}
