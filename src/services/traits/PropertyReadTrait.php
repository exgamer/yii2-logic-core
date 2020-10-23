<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\data\ActiveDataProvider;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\adapters\PropertyAdapter;
use Exception;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит для подключения к сервисам которые используют модель со свойствами
 * для чтения
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait PropertyReadTrait
{
    /**
     * Адаптер
     *
     * @var
     */
    public $propertyAdapter;

    /**
     * Класс адаптера
     *
     * @var string
     */
    public $propertyAdapterClass = PropertyAdapter::class;

    /**
     * Возвращает адаптер дял рабоыт с проперти
     *
     * @return PropertyAdapter
     */
    public function readProperty()
    {
        if (! $this->propertyAdapter) {
            $modelClass = $this->getRelatedModelClass();
            $this->propertyAdapter = Yii::createObject([ 'class' => $this->propertyAdapterClass, 'propertyModelClass' => $modelClass::getPropertyModelClass()]);
        }

        return $this->propertyAdapter;
    }

    /**
     * @deprecated метод пока не поддерживается
     *
     * @param array $queryParams
     * @param array $config
     * @param null $searchModel
     * @param null $formName
     * @param null $condition
     * @throws Exception
     */
    public function getDataProvider($queryParams = [], $config = [], $searchModel = null, $formName = null, $condition = null)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @param $id
     * @param array $with
     * @param bool $asArray
     * @return mixed
     * @throws Exception
     * @deprecated метод не поддерживается
     *
     */
    public function findById($id , $with = [], $asArray = false)
    {
        throw new Exception("unsupported method, use getOneByCondition");
    }
}

