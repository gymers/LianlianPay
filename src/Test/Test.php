<?php

namespace Gymers\LianlianPay\Test;

use Gymers\LianlianPay\LianlianPay;

class Test
{
    /**
     * 网关支付.
     */
    public function gatewayPay()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
            'lianlian_public_key' => '',  // 连连公钥路径
        ];
        $arguments = [
            'user_id' => '',  // 用户唯一编号 注：商户用户唯一编号 保证唯一
            'no_order' => '',  // 商户唯一订单号
            'dt_order' => '', // 商户订单时间 注：格式：YYYYMMDDH24MISS 14 位数字，精确到秒
            'name_goods' => '',  // 商品名称
            'money_order' => '', // 交易金额（单位：元） 注：精确到小数点后两位
            'notify_url' => '',  // 服务器异步通知地址
            'user_info_bind_phone' => '', // 用户绑定手机号 注：若未强制绑定手机号，在商户无法获取用户手机号的情况下，该参数可不传
            'user_info_dt_register' => '', // 用户在商户系统中的注册时间 注：格式须为yyyyMMddHHmmss
            'pay_type' => '', // 支付方式：L-支付宝扫码 V-支付宝应用支付（生活号、小程序） I-微信扫码 W-微信公众号支付 20-微信小程序 23-银联云闪付 U-银联二维码
            'appid' => '', // 扩展参数（可选）注微信公众号和微信小程序需传appid和openid, 微信APP支付需传 appid
            'openid' => '',
        ];
        $response = LianlianPay::gateway($config)->pay($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 网关支付订单查询.
     */
    public function gatewayOrderquery()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'no_order' => '',  // 平台订单号
        ];

        $response = LianlianPay::gateway($config)->orderQuery($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 网关支付退款.
     */
    public function gatewayRefund()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'no_refund' => '',  // 商户退款流水号
            'money_refund' => '', // 退款的金额，单位为元，精确到小数点后两位
            'no_order' => '',  // 原商户订单号
            'dt_order' => '',  // 原商户订单时间 注：格式为 yyyyMMddHHmmss
            'notify_url' => '',  // 接收异步通知的线上地址
        ];

        $response = LianlianPay::gateway($config)->refund($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 网关支付退款查询.
     */
    public function gatewayRefundQuery()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'no_refund' => '', // 商户退款流水号
            'dt_refund' => '', // 商户退款时间。格式为yyyyMMddHHmmss,
        ];

        $response = LianlianPay::gateway($config)->refundQuery($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 聚合支付.
     */
    public function aggregationPay()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'user_id' => '',  // 用户唯一编号
            'auth_code' => '',  // 付款码 注：支付宝、微信用户付款码信息
            'pay_type' => '', // 支付方式 注：WECHAT_MICROPAY-微信付款码 ALIPAY_MICROPAY-支付宝付款码 UNIONPAY_MICROPAY-银联付款码 DC_MICROPAY-数字人民币付款码
            'txn_seqno' => '',  // 商户支付订单号
            'order_amount' => '',  // 订单金额（单位：元） 注：精确到小数点后两位
            'notify_url' => '',  // 支付结果通知地址
        ];

        $response = LianlianPay::aggregation($config)->pay($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 聚合支付订单查询.
     */
    public function aggregationOrderquery()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'txn_seqno' => '',  // 平台订单号
        ];

        $response = LianlianPay::aggregation($config)->orderQuery($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 聚合支付退款.
     */
    public function aggregationRefund()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'refund_seqno' => '',  // 商户退款订单号
            'txn_seqno' => '',  // 商户支付订单号
            'txn_date' => '',  // 交易日期 注：支付交易发生日期，格式：YYYYMMDD
            'refund_reason' => '',  // 退款原因描述（可选）
            'refund_amount' => '',  // 退款的金额，单位为元，精确到小数点后两位
            'notify_url' => '',  // 结果通知地址
        ];

        $response = LianlianPay::aggregation($config)->refund($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 聚合支付退款查询.
     */
    public function aggregationRefundQuery()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'refund_seqno' => '',  // 商户退款订单号
            'refund_date' => '',  // 退款日期 退款交易发生日期，格式：YYYYMMDD
        ];

        $response = LianlianPay::aggregation($config)->refundQuery($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 聚合支付订单关闭.
     */
    public function aggregationOrderClose()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'txn_seqno' => '',  // 商户支付订单号
            'txn_date' => '',  // 交易日期 支付交易发生日期，格式：YYYYMMDD
        ];

        $response = LianlianPay::aggregation($config)->orderClose($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }

    /**
     * 连连微信小程序支付.
     */
    public function miniprogramPay()
    {
        $config = [
            'oid_partner' => '',  // 商户号
            'private_key' => '',  // 私钥路径
        ];

        $arguments = [
            'user_id' => '', // 用户唯一编号 注：商户用户唯一编号 保证唯一
            'no_order' => '',  // 商户唯一订单号
            'dt_order' => '', // 商户订单时间 格式：YYYYMMDDH24MISS 14 位数字，精确到秒
            'name_goods' => '',  // 商品名称
            'money_order' => '', // 交易金额（单位：元） 注：精确到小数点后两位
            'notify_url' => '',  // 服务器异步通知地址
            'user_info_bind_phone' => '', // 用户绑定手机号 若未强制绑定手机号，在商户无法获取用户手机号的情况下，该参数可不传
            'user_info_dt_register' => '', // 用户在商户系统中的注册时间， 格式须为yyyyMMddHHmmss
        ];

        $response = LianlianPay::miniprogram($config)->pay($arguments);

        if ('0000' == $response['ret_code']) {
        }

        throw new \Exception($response['ret_msg']);
    }
}
