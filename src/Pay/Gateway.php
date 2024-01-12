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

    public const ORDER_CLOSE_URI = 'https://openapi.lianlianpay.com/mch/v1/ipay/close';

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
                'user_info_bind_phone' => $arguments['user_info_bind_phone'],
                'user_info_dt_register' => $arguments['user_info_dt_register'],
            ]
        );

        $data = [
            'user_id' => $user_id,
            'oid_partner' => $this->config->oid_partner,
            'sign_type' => self::SIGN_TYPE,
            'busi_partner' => '101001',
            'no_order' => $arguments['no_order'],
            'dt_order' => $arguments['dt_order'],
            'name_goods' => mb_substr($arguments['name_goods'], 0, 42, 'UTF-8'),
            'money_order' => $arguments['money_order'],
            'notify_url' => $arguments['notify_url'],
            'valid_order' => $arguments['valid_order'] ?? '',
            'risk_item' => json_encode($risk_item),
            'pay_type' => $pay_type,
            'ext_param' => json_encode(['appid' => $arguments['appid'], 'openid' => $arguments['openid']]),
        ];

        $data['sign'] = $this->sign($this->format($data), $this->config->private_key);
        $json_string = json_encode($data);
        $pay_load = $this->payLoad($json_string, $this->config->lianlian_public_key);

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

    /**
     * orderClose.
     *
     * @return mixed
     */
    public function orderClose(array $arguments)
    {
        $data = [
            'mch_id' => $this->config->oid_partner,
            'txn_seqno' => $arguments['txn_seqno'],
            'txn_date' => $arguments['txn_date'],
        ];

        return $this->request(self::ORDER_CLOSE_URI, $data);
    }
}
