<?php
namespace concepture\yii2logic\models\traits;

use Exception;

/**
 * Содержит методы реализующие нефизическое удаление записей
 * Треит содержит методы для использования моделей
 * которые используют атрибут is_deleted
 *
 * Trait NonPhysicalDeleteTrait
 * @package concepture\yii2logic\models\traits
 */
trait NonPhysicalDeleteTrait
{
    /**
     * Метка определяющая возможность физического удаления записи
     *
     * @var bool
     */
    public $allow_physical_delete = true;

    /**
     * Переопределено для возможности реализации нефизического удаления
     *
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        if ($this->allow_physical_delete) {
            return parent::delete();
        }

        if (! $this->hasAttribute("is_deleted")){
            throw new Exception("for non physical delete table must have *is_deleted* field");
        }

        $this->is_deleted = 1;

        $this->update(false);

        return true;
    }

    /**
     * Восстановить удаленную нефизически запись
     *
     * @return bool
     * @throws Exception
     */
    public function undelete()
    {
        if ($this->allow_physical_delete) {
            throw new Exception("cannot undelete if allowed physical delete");
        }

        if (! $this->hasAttribute("is_deleted")){
            throw new Exception("for undelete table must have *is_deleted* field");
        }

        $this->is_deleted = 0;

        return $this->update(false, ['is_deleted']);
    }
}

