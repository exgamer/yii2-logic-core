<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Список соцсетей
 * @package common\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SocialEnum extends Enum
{
    const FACEBOOK = 'facebook';
    const TWITTER = 'twitter';
    const INSTAGRAM = 'instagram';
    const GOOGLE = 'google';
    const VKONTAKTE = 'vkontakte';
    const MAILRU = 'mailru';
    const ODNOCLASSNIKI = 'odnoklassniki';
    const YANDEX = 'yandex';
    const YOUTUBE = 'youtube';
    const TELEGRAM = 'telegram';

    public static function labels()
    {
        return [
            self::FACEBOOK => Yii::t('common', "Facebook"),
            self::TWITTER => Yii::t('common', "Twitter"),
            self::INSTAGRAM => Yii::t('common', "Instagram"),
            self::GOOGLE => Yii::t('common', "Google"),
            self::VKONTAKTE => Yii::t('common', "Vkontakte"),
            self::MAILRU => Yii::t('common', "Mailru"),
            self::ODNOCLASSNIKI => Yii::t('common', "Odnoklassniki"),
            self::YANDEX => Yii::t('common', "Yandex"),
            self::YOUTUBE => Yii::t('common', "Youtube"),
            self::TELEGRAM => Yii::t('common', "Telegram")
        ];
    }
}
