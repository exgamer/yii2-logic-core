<?php

namespace concepture\yii2logic\helpers;

use Yii;
use Firebase\JWT\JWT;
use yii\web\Request as WebRequest;

/**
 * Class JwtHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class JwtHelper
{
    /**
     * Getter for exp that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    public static function getJwtExpire()
    {
        return Yii::$app->params['JWT_EXPIRE'];
    }

    /**
     * Getter for secret key that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    public static function getSecretKey()
    {
        return Yii::$app->params['JWT_SECRET'];
    }

    /**
     * Getter for encryption algorytm used in JWT generation and decoding
     * Override this method to set up other algorytm.
     * @return string needed algorytm
     */
    public static function getAlgo()
    {
        return 'HS256';
    }

    /**
     * Decode JWT token
     * @param  string $token access token to decode
     * @return array decoded token
     */
    public static function decodeJWT($token)
    {
        $secret = static::getSecretKey();
        if (! $secret){
            throw new \Exception("JWT Secret Key may not be empty");
        }

        return (array)JWT::decode($token, $secret, [static::getAlgo()]);
    }

    /**
     * @param array $payload
     * @return string encoded JWT
     */
    public static function getJWT($payload = [])
    {
        $secret = static::getSecretKey();
        $currentTime = time();
        $request = Yii::$app->request;
        $hostInfo = '';

        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }

        $payload['iss'] = $hostInfo;
        $payload['aud'] = $hostInfo;
        $payload['iat'] = $currentTime;
        $payload['nbf'] = $currentTime;
        if (!isset($payload['exp'])) {
            $payload['exp'] = $currentTime + static::getJwtExpire();
        }

        return JWT::encode($payload, $secret, static::getAlgo());
    }
}

