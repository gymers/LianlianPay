<?php

namespace Gymers\LianlianPay;

use Gymers\LianlianPay\Exception\PayException;
use Gymers\LianlianPay\Contracts\PayInterface;
use Gymers\LianlianPay\Pay\Aggregation;
use Gymers\LianlianPay\Pay\Gateway;
use Gymers\LianlianPay\Pay\Miniprogram;

/**
 * @method static Gateway     gateway($config)     网关支付
 * @method static Aggregation aggregation($config) 聚合支付
 * @method static Miniprogram miniprogram($config) 连连微信小程序支付
 */
class LianlianPay
{
    public $config;

    /**
     * @param array $config 配置参数
     */
    public function __construct($config)
    {
        $this->config = new Config($config);
    }

    public static function __callStatic($name, $arguments)
    {
        $self = new self($arguments[0]);

        return $self->create($name);
    }

    public function create($name)
    {
        $classname = __NAMESPACE__.'\\Pay\\'.$name;

        $class = new $classname($this->config);

        if ($class instanceof PayInterface) {
            return $class;
        }

        throw new PayException(sprintf('Call to undefined method %s', $name));
    }
}
