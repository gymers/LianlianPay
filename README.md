# 连连支付sdk

## 环境要求

php >= 7.1

## 安装

```shell
composer require gymers/lianlianpay
```

## 特别说明

> 由于连连支付有IP白名单请求限制，所以无法做单元测试。可参考src/Test/Test.php示例

> 连连网关支付目前没有订单关闭接口，可以在发起订单请求的同时把订单有效期设置短一些
