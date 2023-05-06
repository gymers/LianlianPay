<?php

namespace Gymers\LianlianPay\Pay;

use Gymers\LianlianPay\Client\Client;
use Gymers\LianlianPay\Config;
use Gymers\LianlianPay\Contracts\PayInterface;

/**
 * 连连微信小程序支付.
 */
class Miniprogram extends Pay implements PayInterface
{
    public const URI = 'https://payserverapi.lianlianpay.com/v1/paycreatebill';

    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * pay.
     *
     * @return mixed
     */
    public function pay(array $arguments)
    {
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
            'api_version' => '1.0',
            'time_stamp' => date('YmdHis'),
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
            'flag_pay_product' => '20',
            'flag_chnl' => '3',
            'flag_wx_h5' => 'Y',
            'req_domain' => $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_HOST'],
        ];

        $data['sign'] = $this->sign($this->format($data), $this->config->private_key);

        $headers = ['content-type' => 'application/json;charset=UTF-8'];
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $client = new Client();
        $client->setUri(self::URI)->setHeaders($headers)->setBody($body);

        return $client->request();
    }
}
