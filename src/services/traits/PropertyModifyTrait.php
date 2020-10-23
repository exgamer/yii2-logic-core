<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\services\adapters\PropertyModifyAdapter;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит для подключения к сервисам которые используют модель со свойствами
 * для модификации
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait PropertyModifyTrait
{
    /**
     * Адаптер
     *
     * @var
     */
    public $propertyModifyAdapter;

    /**
     * Класс адаптера
     *
     * @var string
     */
    public $propertyModifyAdapterClass = PropertyModifyAdapter::class;

    /**
     * Возвращает адаптер дял рабоыт с проперти
     *
     * @return PropertyModifyAdapter
     */
    public function modifyProperty()
    {
        if (! $this->propertyModifyAdapter) {
            $modelClass = $this->getRelatedModelClass();
            $this->propertyModifyAdapter = Yii::createObject([ 'class' => $this->propertyModifyAdapterClass, 'propertyModelClass' => $modelClass::getPropertyModelClass()]);
        }

        return $this->propertyModifyAdapter;
    }
}

