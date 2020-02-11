<?php

namespace concepture\yii2logic\models\traits;

use Firebase\JWT\JWT;

use Yii;
use yii\web\UnauthorizedHttpException;
use yii\web\Request as WebRequest;

/**
 * Trait to handle JWT-authorization process. Should be attached to User model.
 * If there are many applications using user model in different ways - best way
 * is to use this trait only in the JWT related part.
 */
trait JwtUserTrait
{
    /**
     * Getter for exp that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    protected static function getJwtExpire()
    {
        return Yii::$app->params['JWT_EXPIRE'];
    }

    /**
     * Getter for secret key that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    protected static function getSecretKey()
    {
        return Yii::$app->params['JWT_SECRET'];
    }

    /**
     * Logins user by given JWT encoded string. If string is correctly decoded
     * @param  string $accessToken access token to decode
     * @return mixed|null          User model or null if there's no user
     * @throws \yii\web\UnauthorizedHttpException if anything went wrong
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $errorText = "Incorrect token";
        $decodedArray = static::decodeJWT($token);
        if (!isset($decodedArray['uid'])) {
            throw new UnauthorizedHttpException($errorText);
        }
        // uid is unique identifier of user.
        $id = $decodedArray['uid'];
        return static::findByUid($id);
    }

    /**
     * Decode JWT token
     * @param  string $token access token to decode
     * @return array decoded token
     */
    public static function decodeJWT($token)
    {
        $secret = static::getSecretKey();
        $errorText = "Incorrect token";
        // Decode token and transform it into array.
        // Firebase\JWT\JWT throws exception if token can not be decoded
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            if(YII_DEBUG){
                throw new UnauthorizedHttpException($e->getMessage());
            }
            else{
                throw new UnauthorizedHttpException($errorText);
            }
        }
        $decodedArray = (array)$decoded;
        return $decodedArray;
    }

    /**
     * Finds User model using static method findOne
     * Override this method in model if you need to complicate id-management
     * @param  integer $id if of user to search
     * @return mixed       User model
     * @throws \yii\web\UnauthorizedHttpException if model is not found
     */
    public static function findByUid($id)
    {
        $model = static::findOne($id);
        $errorText = "Incorrect token";
        // Throw error if user is missing
        if (empty($model)) {
            throw new UnauthorizedHttpException($errorText);
        }
        return $model;
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
     * Returns some 'id' to encode to token. By default is current model id.
     * If you override this method, be sure that getPayloadUid is updated too
     * @return identifier of user
     */
    public function getPayloadUid()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @param array $payload
     * @return string encoded JWT
     */
    public function getJWT($payload = [])
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

        // Set up user id
        $payload['uid'] = $this->getPayloadUid();
        if (!isset($payload['exp'])) {
            $payload['exp'] = $currentTime + static::getJwtExpire();
        }
        return JWT::encode($payload, $secret, static::getAlgo());
    }
}
