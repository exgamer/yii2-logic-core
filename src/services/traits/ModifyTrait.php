<?php
namespace concepture\yii2logic\services\traits;


use concepture\yii2logic\forms\Form;
use concepture\yii2logic\models\ActiveRecord;
use Yii;
use yii\helpers\Json;
use yii\base\Exception;

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
     * Добавление записи в бд
     *
     * @param Form $form
     * @return ActiveRecord
     */
    public function create(Form $form)
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
     * @param Form $form
     * @param ActiveRecord $model
     * @return ActiveRecord
     */
    public function update(Form $form, ActiveRecord $model)
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
     * Сохранение формы
     *
     * @param Form $form класс для работы
     * @param ActiveRecord $model модель данных - передается при редактировании
     * @return ActiveRecord | boolean
     * @throws
     */
    protected function save(Form $form , ActiveRecord $model = null)
    {
        $modelClass = $this->getRelatedModelClass();
        if($model === null){
            $model = new $modelClass();
        }
        #флаг для понимания операции создания/редактирования
        $is_new_record = $model->isNewRecord;
        #заполнениe атрибутов
        $data = $form->attributes;
        if($model !== null) {
            #исключение атрибутов первичного ключа
            $pk = $model::primaryKey();
            $data = array_diff_key($data, array_flip($pk));
        }
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
     * @param Form $form
     * @param ActiveRecord $model
     */
    protected function setPrimaryKeysToFrom(Form $form, ActiveRecord $model)
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
    }


    /**
     * Дополнительные действия перед удалением
     * @param ActiveRecord $model
     */
    protected function beforeDelete(ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью перед сохранением
     * @param Form $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function beforeModelSave(Form $form , ActiveRecord $model, $is_new_record){}

    /**
     * Дополнительные действия с моделью после сохранения
     * @param Form $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function afterModelSave(Form $form , ActiveRecord $model, $is_new_record){}

    /**
     * Дополнительные действия с моделью перед созданием
     * @param Form $form класс для работы
     */
    protected function beforeCreate(Form $form){}

    /**
     * Дополнительные действия с моделью после создания
     * @param Form $form класс для работы
     */
    protected function afterCreate(Form $form){}

    /**
     * Дополнительные действия с моделью перед обновлением
     * @param Form $form класс для работы
     * @param ActiveRecord $model
     */
    protected function beforeUpdate(Form $form, ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью после обновления
     * @param Form $form класс для работы
     * @param ActiveRecord $model
     */
    protected function afterUpdate(Form $form, ActiveRecord $model){}
}

