<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\services\adapters\PropertyReadAdapter;
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
    public $propertyReadAdapter;

    /**
     * Класс адаптера
     *
     * @var string
     */
    public $propertyReadAdapterClass = PropertyReadAdapter::class;

    /**
     * Возвращает адаптер дял рабоыт с проперти
     *
     * @return PropertyAdapter
     */
    public function readProperty()
    {
        if (! $this->propertyReadAdapter) {
            $modelClass = $this->getRelatedModelClass();
            $this->propertyReadAdapter = Yii::createObject([ 'class' => $this->propertyReadAdapterClass, 'propertyModelClass' => $modelClass::getPropertyModelClass()]);
        }

        return $this->propertyReadAdapter;
    }
}

