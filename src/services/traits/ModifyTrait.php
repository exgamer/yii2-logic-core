<?php

namespace concepture\yii2logic\services\traits;

use Yii;
use yii\db\Command;
use yii\helpers\Json;
use yii\base\Exception;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\services\events\modify\AfterBatchInsertEvent;
use concepture\yii2logic\services\events\modify\AfterCreateEvent;
use concepture\yii2logic\services\events\modify\AfterDeleteEvent;
use concepture\yii2logic\services\events\modify\AfterModelSaveEvent;
use concepture\yii2logic\services\events\modify\AfterUpdateEvent;
use concepture\yii2logic\services\events\modify\BeforeBatchInsertEvent;
use concepture\yii2logic\services\events\modify\BeforeCreateEvent;
use concepture\yii2logic\services\events\modify\BeforeDeleteEvent;
use concepture\yii2logic\services\events\modify\BeforeModelSaveEvent;
use concepture\yii2logic\services\events\modify\BeforeUpdateEvent;
use concepture\yii2logic\services\events\modify\AfterModifyEvent;

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
     * Вставка 1 записи
     *
     * @param $data
     * @return bool
     */
    public function insert($data)
    {
        return $this->getDb()->createCommand()->insert($this->getTableName(), $data)->execute();
    }

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
        $transaction = $db->beginTransaction();
        try {
            $this->beforeBatchInsert($fields, $rows);
            $sql = $db->queryBuilder->batchInsert($this->getTableName(), $fields, $rows);
            $update = [];
            foreach ($fields as $field){
                $update[] = "`" . $field."`= VALUES(`$field`)";
            }

            if ($this->isMysql()){
                $result = $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE ' . implode(",", $update))->execute();
            }

            /**
             * @TODO дописать для постгреса
             */
            if ($this->isPostgres()){
                $result = $db->createCommand($sql . ' ON CONFLICT DO NOTHING ')->execute();
            }

            $this->afterBatchInsert($fields, $rows);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }

        return $result ?? false;
    }

    /**
     * TODO переделать batchInsert на подобное либо использовать это
     * TODO убрать вызов batchInsertTemporary в проекте
     *
     * метод выше не работает с Expression проблема в том вызывается execute с новым экземпляром Command
     * при этом все параметры забинденные при вызове $db->queryBuilder->batchInsert() обнуляются
     *
     * @param array $fields
     * @param array $rows
     * @return int
     */
    public function batchInsertTemporary($fields, $rows)
    {
        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->beforeBatchInsert($fields, $rows);
            /** @var Command $command */
            $command = $db->createCommand()->batchInsert($this->getTableName(), $fields, $rows);
            $params = $command->params;
            $update = [];
            foreach ($fields as $field){
                $update[] = "`" . $field."`= VALUES(`$field`)";
            }

            if ($this->isMysql()){
                $command->setSql($command->getSql() . ' ON DUPLICATE KEY UPDATE ' . implode(",", $update));
                if ($params && is_array($params)) {
                    foreach ($params as $k => $v) {
                        $command->bindValue($k, $v);
                    };
                }

                $result = $command->execute();
            }

            /**
             * @TODO дописать для постгреса
             */
            if ($this->isPostgres()){
                $command->setSql($command->getSql() . ' ON CONFLICT DO NOTHING ');
                if ($params && is_array($params)) {
                    foreach ($params as $k => $v) {
                        $command->bindValue($k, $v);
                    };
                }

                $result = $command->execute();
            }

            $this->afterBatchInsert($fields, $rows);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }

        return $result ?? false;
    }

    /**
     * Вызывает стандартный deleteAll связанной модели
     *
     * @param $condition
     * @param array $params
     * @return mixed
     */
    public function deleteAllByCondition($condition, $params = [])
    {
        $model = $this->getRelatedModel();

        return $model::deleteAll($condition, $params);
    }

    /**
     * Вызывает стандартный updateAll связанной модели
     *
     * @param array $attributes attribute values (name-value pairs) to be saved into the table
     * @param $condition
     * @param array $params
     * @return mixed
     */
    public function updateAllByCondition($attributes, $condition = '', $params = [])
    {
        $model = $this->getRelatedModel();

        return $model::updateAll($attributes, $condition, $params);
    }

    /**
     * Добавление записи в бд
     *
     * @param Model $form
     * @return ActiveRecord
     */
    public function create(Model $form)
    {
        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->beforeCreate($form);
            $model = $this->save($form);
            if (! $model) {
                return $model;
            }
            $this->afterCreate($form);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }
        return $model ?? false;
    }

    /**
     * обновление записи в бд
     *
     * @param Model $form
     * @param ActiveRecord $model
     * @param bool $validate
     * @return ActiveRecord
     */
    public function update(Model $form, ActiveRecord $model, $validate = true)
    {
        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->beforeUpdate($form, $model);
            $model = $this->save($form, $model, $validate);
            if (! $model) {
                return $model;
            }
            $this->afterUpdate($form, $model);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }

        return $model ?? false;
    }

    /**
     * Обновление модели
     *
     * @param $model
     * @param array $data
     * @param string|null $formName
     * @param bool $validate
     * @param null $scenario
     * @return bool|ActiveRecord|Form
     */
    public function updateByModel($model, $data, $formName = '', $validate = true, $scenario = null)
    {
        $form = $this->getRelatedForm();
        if($scenario) {
            $form->scenario = $scenario;
        }

        $form->setAttributes($model->attributes, false);
        if (method_exists($form, 'customizeForm')) {
            $form->customizeForm($model);
        }

        $scope = $formName === null ? $form->formName() : $formName;
        if ($scope !== '' && isset($data[$scope])) {
            $data = $data[$scope];
        }

        /**
         * Присваивание атрибутов делаем явно, потому что если сделать load
         * потеряются атрибуты типа json и pojo которые затираются загрузке в форму, если их нет в data
         */
        foreach ($data as $attribute => $value) {
            if (! property_exists($form, $attribute)) {
                continue;
            }

            $form->{$attribute} = $value;
        }

        $model->setAttributes($form->attributes);
        if ($validate) {
            if (!$form->validate(null, true, $model)) {
                $form->addErrors($model->getErrors());

                return $form;
            }
        }

        return $this->update($form, $model, $validate);
    }

    /**
     * Обновление записи по id
     *
     * @param $id
     * @param array $data
     * @param string|null $formName
     * @param bool $validate
     * @param null $scenario
     * @return bool|ActiveRecord|Form
     */
    public function updateById($id, $data, $formName = '', $validate = true, $scenario = null)
    {
        $model = $this->findById($id);

        return $this->updateByModel($model, $data, $formName, $validate, $scenario);
    }

    /**
     * TODO тут когда нибудь нужен рефактор, метод взят как есть из V3
     *
     * Сохранение формы
     *
     * @param Model $form класс для работы
     * @param ActiveRecord $model модель данных - передается при редактировании
     * @param bool $validate
     * @return ActiveRecord | boolean
     */
    public function save(Model $form , ActiveRecord $model = null, $validate = true)
    {
        if($model === null){
            $model = $this->getRelatedModel();
        }

        $scenarios = $model->scenarios();
        if(isset($scenarios[$form->scenario])) {
            $model->setScenario($form->scenario);
        }
        #флаг для понимания операции создания/редактирования
        $is_new_record = $model->isNewRecord;
        #заполнениe атрибутов
        $this->beforeModelLoad($form, $model);
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
        if ($model->save($validate) === false) {
            $form->addErrors($model->getErrors());

            return false;
        }
        $this->setPrimaryKeysToFrom($form, $model);
        $this->afterModelSave($form, $model, $is_new_record);
        # todo: понаблюдать не разъебет ли все к ебеням
        foreach ($model->attributes as $attribute => $value) {
            if(! $form->hasProperty($attribute)) {
                continue;
            }

            $form->{$attribute} = $value;
        }

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
        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->beforeDelete($model);
            $result = $model->delete();
            $this->afterDelete($model);
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }

        return $result ?? false;
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
                Yii::t('core','model undelete exception - {errors}', [
                    'errors' => Json::encode($model->getErrors())
                ])
            );
        }
    }

    /**
     * Дефствия перед загрузкой формы в модель $model->load($data) в функции save
     *
     * @param Model $form
     * @param ActiveRecord|null $model
     */
    protected function beforeModelLoad(Model $form , ActiveRecord $model = null)
    {

    }

    /**
     * Дополнительные действия с моделью перед сохранением
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function beforeModelSave(Model $form , ActiveRecord $model, $is_new_record)
    {
        $this->trigger(static::EVENT_BEFORE_MODEL_SAVE, new BeforeModelSaveEvent(['form' => $form, 'model' => $model, 'is_new_record' => $is_new_record]));
    }

    /**
     * Дополнительные действия с моделью после сохранения
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     * @param boolean $is_new_record
     */
    protected function afterModelSave(Model $form , ActiveRecord $model, $is_new_record)
    {
        $this->trigger(static::EVENT_AFTER_MODEL_SAVE, new AfterModelSaveEvent(['form' => $form, 'model' => $model, 'is_new_record' => $is_new_record]));
        $this->trigger(static::EVENT_AFTER_MODIFY, new AfterModifyEvent(['form' => $form, 'model' => $model, 'is_new_record' => $is_new_record]));
    }

    /**
     * Дополнительные действия с моделью перед созданием
     * @param Model $form класс для работы
     */
    protected function beforeCreate(Model $form)
    {
        $this->trigger(static::EVENT_BEFORE_CREATE, new BeforeCreateEvent(['form' => $form]));
    }

    /**
     * Дополнительные действия с моделью после создания
     * @param Model $form класс для работы
     */
    protected function afterCreate(Model $form)
    {
        $this->trigger(static::EVENT_AFTER_CREATE, new AfterCreateEvent(['form' => $form]));
    }

    /**
     * Дополнительные действия с моделью перед обновлением
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function beforeUpdate(Model $form, ActiveRecord $model)
    {
        $this->trigger(static::EVENT_BEFORE_UPDATE, new BeforeUpdateEvent(['form' => $form, 'model' => $model]));
    }

    /**
     * Дополнительные действия с моделью после обновления
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function afterUpdate(Model $form, ActiveRecord $model)
    {
        $this->trigger(static::EVENT_AFTER_UPDATE, new AfterUpdateEvent(['form' => $form, 'model' => $model]));
    }

    /**
     * Дополнительные действия перед удалением
     * @param ActiveRecord $model
     */
    protected function beforeDelete(ActiveRecord $model)
    {
        $this->trigger(static::EVENT_BEFORE_DELETE, new BeforeDeleteEvent(['model' => $model]));
    }

    /**
     * Дополнительные действия после удалением
     * @param ActiveRecord $model
     */
    protected function afterDelete(ActiveRecord $model)
    {
        $this->trigger(static::EVENT_AFTER_DELETE, new AfterDeleteEvent(['model' => $model]));
        $this->trigger(static::EVENT_AFTER_MODIFY, new AfterModifyEvent(['model' => $model]));
    }

    /**
     * @param $fields
     * @param $rows
     */
    protected function beforeBatchInsert($fields, $rows)
    {
        $this->trigger(static::EVENT_BEFORE_BATCH_INSERT, new BeforeBatchInsertEvent(['fields' => $fields, 'rows' => $rows]));
    }

    /**
     * @param $fields
     * @param $rows
     */
    protected function afterBatchInsert($fields, $rows)
    {
        $this->trigger(static::EVENT_AFTER_BATCH_INSERT, new AfterBatchInsertEvent(['fields' => $fields, 'rows' => $rows]));
        $event = new AfterModifyEvent();
        $event->modifyData = $rows;
        $this->trigger(static::EVENT_AFTER_MODIFY, $event);
    }
}

