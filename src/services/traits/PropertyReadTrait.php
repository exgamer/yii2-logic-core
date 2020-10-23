<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\services\adapters\PropertyAdapter;
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
}

