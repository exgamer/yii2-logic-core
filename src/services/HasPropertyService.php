<?php

namespace concepture\yii2logic\services;


use Yii;
use yii\web\Application;
use concepture\yii2logic\db\HasPropertyActiveQuery;
use concepture\yii2logic\services\traits\PropertyModifyTrait;
use concepture\yii2logic\services\traits\PropertyReadTrait;
use concepture\yii2logic\models\ActiveRecord;


/**
 * Базовый сервис для сущностей с пропертями
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class HasPropertyService extends Service
{
    use PropertyReadTrait,
        PropertyModifyTrait;

    /**
     * Получение записи по идентификатору на основе локали проперти
     *
     * @param int $id
     * @param int|null $locale_id
     * @param int|null $domain_id
     * @return ActiveRecord
     * @throws \ReflectionException
     */
    public function findByIdLocaleBased(int $id, int $locale_id = null, int $domain_id = null)
    {
        if(
            Yii::$app instanceof Application
        ) {
            $request = Yii::$app->getRequest();
            if(null === $locale_id && ($requestLocaleId = $request->get('locale_id'))) {
                $locale_id = $requestLocaleId;
            }

            if(null == $domain_id && ($requestDomainId = $request->get('domain_id'))) {
                $domain_id = $requestDomainId;
            }
        }

        $model = $this->getOneByCondition(function (HasPropertyActiveQuery $query) use($id, $domain_id, $locale_id) {
            $query->andWhere(['id' => $id]);
            if (! $locale_id) {
                $query->applyPropertyUniqueValue(['domain_id' => $domain_id]);
            } else {
                $query->applyPropertyUniqueValue(['domain_id' => $domain_id, 'locale_id' => $locale_id]);
            }
        });

        if($model) {
            return $model;
        }

        $originModelClass = $this->getRelatedModel();

        return $originModelClass::clearFind()->where(['id' => $id])->one();
    }
}