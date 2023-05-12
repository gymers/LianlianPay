<?php

namespace Gymers\LianlianPay;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Config
{
    public $serializer;

    public function __construct($config)
    {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
        $array = $this->serializer->normalize($config);
        $object = $this->serializer->denormalize($array, self::class);

        return $object;
    }

    /**
     * 商户号.
     */
    public $oid_partner;

    /**
     * 私钥路径.
     */
    public $private_key;

    /**
     * 公钥路径.
     */
    public $public_key;

    /**
     * 连连公钥路径.
    */
    public $lianlian_public_key;
}
