<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Domain;
use concepture\yii2logic\services\Service;
use yii\helpers\Url;
use Yii;

/**
 * Class DomainService
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainService extends Service
{
    /**
     * Возвращает id текущего домена
     *
     * @param bool $reset
     *
     * @return int|null
     */
    public function getCurrentDomainId($reset = false)
    {
        static $result;

        if($result && ! $reset) {

            return $result;
        }

        if (! isset(Yii::$app->params['yii2handbook'])){

            return null;
        }

        if (! isset(Yii::$app->params['yii2handbook']['domainMap'])){

            return null;
        }

        $domainMap = Yii::$app->params['yii2handbook']['domainMap'];
        $host = $this->getCurrentHost();
        if(! $host) {
            return null;
        }

        if (! isset($domainMap[$host])){
            $domainMap = array_flip($domainMap);
            if (! isset($domainMap[$host])){
                return null;
            }

            $host = $domainMap[$host];
            $domainMap = array_flip($domainMap);
        }

        $domainAlias = $domainMap[$host];
        $domains = $this->catalog(false);
        $domains = array_flip($domains);
        if (! isset($domains[$domainAlias])){

            return null;
        }

        return $domains[$domainAlias];
    }

    /**
     * Возвращает текущий хост
     *
     * @return string
     */
    public function getCurrentHost()
    {
        static $result;

        if($result) {
            return $result;
        }

        $currentDomain = null;
        if (Yii::$app instanceof \yii\web\Application) {
            $currentDomain = Url::base(true);
        }
        $parsed = parse_url($currentDomain);

        return $parsed['host'];
    }

    /**
     * Возвращает запись текущего домена
     *
     * @param bool $reset
     *
     * @return Domain
     */
    public function getCurrentDomain($reset = false)
    {
        static $result;

        if($result && ! $reset) {
            return $result;
        }

        $currentDomainId = $this->getCurrentDomainId();
        $result = $this->getCatalogModel($currentDomainId);

        return $result;
    }
}