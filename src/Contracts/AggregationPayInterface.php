<?php

namespace Gymers\LianlianPay\Contracts;

interface AggregationPayInterface extends PayInterface
{
    public function orderQuery(array $arguments);

    public function refund(array $arguments);

    public function refundQuery(array $arguments);

    public function orderClose(array $arguments);
}
