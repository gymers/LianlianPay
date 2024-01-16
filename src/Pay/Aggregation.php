<?php

namespace Gymers\LianlianPay\Pay;

use Gymers\LianlianPay\Client\Client;
use Gymers\LianlianPay\Config;
use Gymers\LianlianPay\Exception\PayTypeException;
use Gymers\LianlianPay\Contracts\AggregationPayInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * 聚合支付.
 */
class Aggregation extends Pay implements AggregationPayInterface
{
    public const PAY_URI = 'https://openapi.lianlianpay.com/mch/v1/ipay/directpay';

    public const ORDER_QUERY_URI = 'https://openapi.lianlianpay.com/query/v1/ipay/orderquery';

    public const REFUND_URI = 'https://openapi.lianlianpay.com/mch/v1/ipay/refund';

    public const REFUND_QUERY_URI = 'https://openapi.lianlianpay.com/query/v1/ipay/refundquery';

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
        $sign = $this->sign(md5(json_encode($data)), $this->config->private_key);

        $headers = [
            'content-type' => 'application/json;charset=UTF-8',
            'Signature-Type' => self::SIGN_TYPE,
            'Signature-Data' => $sign,
            'mch_id' => $this->config->oid_partner,
            'timestamp' => date('YmdHis'),
        ];
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        $log = new Logger('lianlian');
        $log->pushHandler(new StreamHandler($this->config->log_path, Logger::INFO, true, 0777));
        $log->addInfo('lianlian.request', ['uri' => $uri, 'headers' => $headers, 'body' => $body]);

        $client = new Client();
        $client->setUri($uri)->setHeaders($headers)->setBody($body);

        $response = $client->request();

        $log = new Logger('lianlian');
        $log->pushHandler(new StreamHandler($this->config->log_path, Logger::INFO, true, 0777));
        $log->addInfo('lianlian.response', ['response' => $response]);

        return $response;
    }

    /**
     * pay.
     *
     * @return mixed
     */
    public function pay(array $arguments)
    {
        $pay_type = $arguments['pay_type'];
        if (!in_array($pay_type, self::AGGREGATION_PAY_TYPE)) {
            throw new PayTypeException($pay_type);
        }

        $data = [
            'mch_id' => $this->config->oid_partner,
            'user_id' => $arguments['user_id'],
            'auth_code' => $arguments['auth_code'],
            'pay_type' => $pay_type,
            'busi_type' => '100001',
            'txn_seqno' => $arguments['txn_seqno'],
            'txn_time' => date('YmdHis'),
            'order_amount' => $arguments['order_amount'],
            'notify_url' => $arguments['notify_url'],
        ];
        if (isset($arguments['order_info']) && !empty($arguments['order_info'])) {
            $data['order_info'] = $arguments['order_info'];
        }
        if (isset($arguments['pay_expire']) && !empty($arguments['pay_expire'])) {
            $data['pay_expire'] = $arguments['pay_expire'];
        }

        return $this->request(self::PAY_URI, $data);
    }

    /**
     * orderQuery.
     *
     * @return mixed
     */
    public function orderQuery(array $arguments)
    {
        $data = [
            'mch_id' => $this->config->oid_partner,
            'txn_seqno' => $arguments['txn_seqno'],
            'txn_time' => $arguments['txn_time'] ?? date('YmdHis'),
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
            'mch_id' => $this->config->oid_partner,
            'refund_seqno' => $arguments['refund_seqno'],
            'refund_time' => date('YmdHis'),
            'txn_seqno' => $arguments['txn_seqno'],
            'txn_date' => $arguments['txn_date'],
            'refund_amount' => $arguments['refund_amount'],
            'notify_url' => $arguments['notify_url'],
        ];
        if (isset($arguments['refund_reason']) && !empty($arguments['refund_reason'])) {
            $data['refund_reason'] = $arguments['refund_reason'];
        }

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
            'mch_id' => $this->config->oid_partner,
            'refund_seqno' => $arguments['refund_seqno'],
            'refund_date' => $arguments['refund_date'],
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
            'txn_date' => $arguments['txn_date'],
        ];
        if (!empty($arguments['txn_seqno'])) {
            $data['txn_seqno'] = $arguments['txn_seqno'];
        }
        if (!empty($arguments['platform_txno'])) {
            $data['platform_txno'] = $arguments['platform_txno'];
        }

        return $this->request(self::ORDER_CLOSE_URI, $data);
    }
}
