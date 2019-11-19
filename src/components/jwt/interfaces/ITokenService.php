<?php

namespace concepture\yii2logic\components\jwt\interfaces;

/**
 * Интерфейс класса для работы с токеном
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
interface ITokenService
{
    /**
     * @param int $expire
     */
    public function setExpire(int $expire);

    /**
     * @param string $secret
     */
    public function setSecret(string $secret);

    /**
     * @param string $algo
     */
    public function setAlgo(string $algo);

    /**
     * @param array $payload
     * @return string
     */
    public function encode($payload = []) : string;

    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token) : array;
}