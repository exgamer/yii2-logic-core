<?php

namespace concepture\yii2logic\components\jwt\services;

use Yii;
use yii\web\Request;
use concepture\yii2logic\base\Exception;
use concepture\yii2logic\services\Service;
use Firebase\JWT\JWT;
use concepture\yii2logic\components\jwt\interfaces\ITokenService;

/**
 * Вспомогательный класс для работы с JSON Web Tokens
 *
 * Yii::createObject([
 *      'class' => JWTService::class,
 *      'config' => [
 *          'secret' => 'secret',
 *          'expire' => 100
 *      ],
 *  ]);
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class JWTService extends Service implements ITokenService
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var string открытый ключ шифрования
     */
    private $secret;

    /**
     * @var integer время жизни в секундах
     */
    private $expire = (60 * 60);

    /**
     * @var string алгоритм шифрования
     */
    private $algo = 'HS256';

    /**
     * @inheritDoc
     */
    public function init()
    {
        foreach ($this->config as $key => $value) {
            if(! method_exists($this, "set{$key}")) {
                continue;
            }

            $this->{"set{$key}"}($value);
        }
    }

    /**
     * @param int $expire
     */
    public function setExpire(int $expire)
    {
        $this->expire = $expire;
    }

    /**
     * @return int
     */
    public function getExpire() : int
    {
        return $this->expire;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getSecret() : string
    {
        return $this->secret;
    }

    /**
     * @param string $algo
     */
    public function setAlgo(string $algo)
    {
        $this->algo = $algo;
    }

    /**
     * @return string
     */
    public function getAlgo() : string
    {
        return $this->algo;
    }

    /**
     * Кодирует и возвращает токен
     *
     * @param array $payload
     *
     * @return string encoded JWT
     */
    public function encode($payload = []) : string
    {
        $secret = $this->getSecret();
        $time = time();
        $hostInfo = '';

        $request = Yii::$app->request;
        if ($request instanceof Request) {
            $hostInfo = $request->getHostInfo();
        }

        if($hostInfo) {
            $payload['iss'] = $hostInfo;
            $payload['aud'] = $hostInfo;
        }
        #время когда токен начнет действовать
        if (! isset($payload['iat'])) {
            $payload['iat'] = $time;
        }
        #время выпуска токена
        $payload['nbf'] = $time;
        #время жизни токена
        if (! isset($payload['exp'])) {
            $payload['exp'] = $time + $this->getExpire();
        }

        return JWT::encode($payload, $secret, $this->getAlgo());
    }

    /**
     * Декодирование токена
     *
     * @param string $token
     * @return array
     * @throws JWTServiceException
     */
    public function decode(string $token) : array
    {
        $secret = $this->getSecret();
        $error = "Incorrect token";
        try {
            $decoded = JWT::decode($token, $secret, [$this->getAlgo()]);
        } catch (\Exception $e) {
            if(YII_DEBUG){
                throw new JWTServiceException($e->getMessage());
            } else{
                throw new JWTServiceException($error);
            }
        }

        $result = (array) $decoded;

        return $result;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class JWTServiceException extends Exception {}