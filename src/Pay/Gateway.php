<?php

namespace Gymers\LianlianPay\Pay;

use Gymers\LianlianPay\Client\Client;
use Gymers\LianlianPay\Config;
use Gymers\LianlianPay\Exception\PayTypeException;
use Gymers\LianlianPay\Contracts\GatewayPayInterface;

/**
 * 网关支付.
 */
class Gateway extends Pay implements GatewayPayInterface
{
    public const PAY_URI = 'https://mpayapi.lianlianpay.com/v1/bankcardprepay';

    public const ORDER_QUERY_URI = 'https://queryapi.lianlianpay.com/orderquery.htm';

    public const REFUND_URI = 'https://traderapi.lianlianpay.com/refund.htm';

    public const REFUND_QUERY_URI = 'https://queryapi.lianlianpay.com/refundquery.htm';

    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * request.
     *
     * @return mixed
     */
    public function request(string $uri, array $data)
    {
        $data['sign'] = $this->sign($this->format($data), $this->config->private_key);

        $headers = ['content-type' => 'application/json;charset=UTF-8'];
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $client = new Client();
        $client->setMethod('POST')->setUri($uri)->setHeaders($headers)->setBody($body);

        return $client->request();
    }

    /**
     * pay.
     *
     * @return mixed
     */
    public function pay(array $arguments)
    {
        $pay_type = $arguments['pay_type'];
        if (!in_array($pay_type, self::GATEWAY_PAY_TYPE)) {
            throw new PayTypeException($pay_type);
        }

        $user_id = $arguments['user_id'] ?? '1';
        $risk_item = array_merge(
            [
                'frms_ware_category' => '1001',
                'user_info_mercht_userno' => $user_id,
            ],
            [
                'user_info_bind_phone' => $arguments['user_info_bind_phone'] ?? '',
                'user_info_dt_register' => date('YmdHis', $arguments['user_info_dt_register']),
            ]
        );

        $data = [
            'user_id' => $user_id,
            'oid_partner' => $this->config->oid_partner,
            'sign_type' => self::SIGN_TYPE,
            'busi_partner' => '101001',
            'no_order' => $arguments['no_order'],
            'dt_order' => $arguments['dt_order'],
            'name_goods' => $arguments['name_goods'],
            'money_order' => $arguments['money_order'],
            'notify_url' => $arguments['notify_url'],
            'risk_item' => json_encode($risk_item),
            'pay_type' => $pay_type,
            'ext_param' => json_encode($arguments['ext_param']),
        ];

        $data['sign'] = $this->sign($this->format($data), $this->config->private_key);
        $json_string = json_encode($data);
        $pay_load = $this->payLoad($json_string, $this->config->public_key);

        $headers = ['content-type' => 'application/json;charset=UTF-8'];
        $body = json_encode(['oid_partner' => $this->config->oid_partner, 'pay_load' => $pay_load], JSON_UNESCAPED_UNICODE);

        $client = new Client();
        $client->setUri(self::PAY_URI)->setHeaders($headers)->setBody($body);

        return $client->request();
    }

    /**
     * orderQuery.
     *
     * @return mixed
     */
    public function orderQuery(array $arguments)
    {
        $data = [
            'oid_partner' => $this->config->oid_partner,
            'sign_type' => self::SIGN_TYPE,
            'no_order' => $arguments['no_order'],
        ];

        return $this->request(self::ORDER_QUERY_URI, $data);
    }

    /**
     * refund.
     *
     * @return mixed
     */
    public function refund(array $arguments)
    {
        $data = [
            'oid_partner' => $this->config->oid_partner,
            'sign_type' => self::SIGN_TYPE,
            'no_refund' => $arguments['no_refund'],
            'dt_refund' => date('YmdHis'),
            'money_refund' => $arguments['money_refund'],
            'no_order' => $arguments['no_order'],
            'dt_order' => $arguments['dt_order'],
            'notify_url' => $arguments['notify_url'],
        ];

        return $this->request(self::REFUND_URI, $data);
    }

    /**
     * refundQuery.
     *
     * @return mixed
     */
    public function refundQuery(array $arguments)
    {
        $data = [
            'oid_partner' => $this->config->oid_partner,
            'sign_type' => self::SIGN_TYPE,
            'no_refund' => $arguments['no_refund'],
            'dt_refund' => $arguments['dt_refund'],
        ];

        return $this->request(self::REFUND_QUERY_URI, $data);
    }
}
