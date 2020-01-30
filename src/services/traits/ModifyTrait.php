<?php

namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\CacheTagsEnum;
use concepture\yii2logic\forms\Form;
use Yii;
use yii\helpers\Json;
use yii\base\Exception;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Треит сервиса содержащий методы для модификации данных
 *
 * Trait ModifyTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait ModifyTrait
{
    /**
     * @var array
     */
    private $oldData = [];

    /**
     * Мультивставка записей если их нет
     *
     * поля которые нужно встатвить
     * @param $fields
     *
     * данные
     * @param $rows
     * @return boolean
     */
    public function batchInsert($fields, $rows)
    {
        $db = $this->getDb();
        $sql = $db->queryBuilder->batchInsert($this->getTableName(), $fields, $rows);
        $update = [];
        foreach ($fields as $field){
            $update[] = $field."= VALUES($field)";
        }
        return $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE ' . implode(",", $update))->execute();
    }

    /**
     * Добавление записи в бд
     *
     * @param Model $form
     * @return ActiveRecord
     */
    public function create(Model $form)
    {
        $this->beforeCreate($form);
        $model = $this->save($form);
        if (! $model) {
            return $model;
        }
        $this->afterCreate($form);

        return $model;
    }

    /**
     * обновление записи в бд
     *
     * @param Model $form
     * @param ActiveRecord $model
     * @return ActiveRecord
     */
    public function update(Model $form, ActiveRecord $model)
    {
        $this->beforeUpdate($form, $model);
        $model = $this->save($form, $model);
        if (! $model) {
            return $model;
        }
        $this->afterUpdate($form, $model);

        return $model;
    }

    /**
     * Обновление записи по id
     *
     * @param $id
     * @param array $data
     * @param string|null $formName
     * @return bool|ActiveRecord|Form
     */
    public function updateById($id, $data, $formName = '')
    {
        $model = $this->findById($id);
        $form = $this->getRelatedForm();
        $form->setAttributes($model->attributes, false);
        if (method_exists($form, 'customizeForm')) {
            $form->customizeForm($model);
        }

        if (! $form->load($data, $formName)) {

            return $form;
        }

        $model->setAttributes($form->attributes);
        if (! $form->validate(null, true, $model)) {
            $form->addErrors($model->getErrors());

            return $form;
        }

        return $this->update($form, $model);
    }

    /**
     * TODO тут когда нибудь нужен рефактор, метод взят как есть из V3
     *
     * Сохранение формы
     *
     * @param Model $form класс для работы
     * @param ActiveRecord $model модель данных - передается при редактировании
     * @return ActiveRecord | boolean
     * @throws
     */
    protected function save(Model $form , ActiveRecord $model = null)
    {
        if($model === null){
            $model = $this->getRelatedModel();
        }
        #флаг для понимания операции создания/редактирования
        $is_new_record = $model->isNewRecord;
        #заполнениe атрибутов
        $data = $form->attributes;
        /**
         * Блок закоментирован из за того, что когда ключ составной он вырезается из данных
         * Этот блок вроде был добавлен еще когда форма содержала id
         */
//        if($model !== null) {
//            #исключение атрибутов первичного ключа
//            $pk = $model::primaryKey();
//            $data = array_diff_key($data, array_flip($pk));
//        }
        $model->load($data, '');
        if($model !== null) {
            $this->setOldData($model->getOldAttributes());
        }
        $this->beforeModelSave($form, $model, $is_new_record);
        if (! $model->save()) {
            $form->addErrors($model->getErrors());

            return false;
        }
        $this->setPrimaryKeysToFrom($form, $model);
        $this->afterModelSave($form, $model, $is_new_record);

        return $model;
    }


    /**
     * @param array $oldData
     */
    protected function setOldData($oldData)
    {
        $this->oldData = $oldData;
    }

    /**
     * @return array
     */
    protected function getOldData()
    {
        return $this->oldData;
    }

    protected function getOldDataAttribute($name)
    {
        if (! isset($this->oldData[$name])){
            return null;
        }

        return $this->oldData[$name];
    }

    /**
     * Выставляем полученные примари ключи в форму
     * @param Model $form
     * @param ActiveRecord $model
     */
    protected function setPrimaryKeysToFrom(Model $form, ActiveRecord $model)
    {
        $primaryKeys = $model::primaryKey();
        foreach ($primaryKeys as $attribute) {
            if (property_exists($form, $attribute)){
                $form->{$attribute} = $model->{$attribute};
            }
        }
    }

    /**
     * удаление
     * @param ActiveRecord $model
     * @throws
     * @return boolean
     */
    public function delete(ActiveRecord $model)
    {
        $this->beforeDelete($model);
        if (! $model->delete()){
            throw new Exception(
                Yii::t('service','model delete exception - {errors}', [
                    'errors' => Json::encode($model->getErrors())
                ])
            );
        }

        $this->afterDelete($model);
    }

    /**
     * восстановление нефизически удаленной сущности
     *
     * @param ActiveRecord $model
     * @throws
     * @return boolean
     */
    public function undelete(ActiveRecord $model)
    {
        if (! $model->undelete()){
            throw new Exception(
                Yii::t('service','model undelete exception - {errors}', [
                    'errors' => Json::encode($model->getErrors())
                ])
            );
        }
    }


    /**
     * Дополнительные действия перед удалением
     * @param ActiveRecord $model
     */
    protected function beforeDelete(ActiveRecord $model){}

    /**
     * Дополнительные действия после удалением
     * @param ActiveRecord $model
     */
    protected function afterDelete(ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью перед сохранением
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function beforeModelSave(Model $form , ActiveRecord $model, $is_new_record){}

    /**
     * Дополнительные действия с моделью после сохранения
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function afterModelSave(Model $form , ActiveRecord $model, $is_new_record){}

    /**
     * Дополнительные действия с моделью перед созданием
     * @param Model $form класс для работы
     */
    protected function beforeCreate(Model $form){}

    /**
     * Дополнительные действия с моделью после создания
     * @param Model $form класс для работы
     */
    protected function afterCreate(Model $form){}

    /**
     * Дополнительные действия с моделью перед обновлением
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function beforeUpdate(Model $form, ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью после обновления
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function afterUpdate(Model $form, ActiveRecord $model){}
}

