<?php

namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\helpers\JwtHelper;
use Firebase\JWT\JWT;
use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * Trait to handle JWT-authorization process. Should be attached to User model.
 * If there are many applications using user model in different ways - best way
 * is to use this trait only in the JWT related part.
 */
trait JwtUserTrait
{
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
        $errorText = "Incorrect token";
        try {
            $decoded = JwtHelper::decodeJWT($token);
        } catch (\Exception $e) {
            if(YII_DEBUG){
                throw new UnauthorizedHttpException($e->getMessage());
            }
            else{
                throw new UnauthorizedHttpException($errorText);
            }
        }

        return $decoded;
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
        $payload = [];
        // Set up user id
        $payload['uid'] = $this->getPayloadUid();

        return JwtHelper::getJWT($payload);
    }
}
