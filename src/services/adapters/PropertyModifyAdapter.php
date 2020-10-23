<?php

namespace concepture\yii2logic\services\adapters;

use concepture\yii2logic\forms\Model;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\traits\HasDbConnectionTrait;
use concepture\yii2logic\services\traits\ModifyTrait;
use concepture\yii2logic\services\traits\SqlModifyTrait;
use Exception;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * Адаптер для работы с проперти
 *
 * Class PropertyAdapter
 * @package concepture\yii2logic\services\adapters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PropertyModifyAdapter extends Component
{
    use ModifyTrait;
    use SqlModifyTrait;
    use HasDbConnectionTrait;

    public $propertyModelClass;


    /**
     * Получить класс связанной модели
     *
     * @throws Exception
     */
    public function getRelatedModelClass()
    {
        return $this->propertyModelClass;
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @return string
     * @throws Exception
     *
     * Получить класс связанной формы
     *
     */
    public function getRelatedFormClass()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Получить класс связанной search модели
     *
     * @throws Exception
     */
    public function getRelatedSearchModelClass()
    {
        throw new Exception("unsupported method");
    }


    /**
     * @deprecated метод не поддерживается
     *
     * Получить новый обьект формы
     *
     * @throws Exception
     */
    public function getRelatedForm()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Получить новый обьект серч формы
     *
     * @throws Exception
     */
    public function getRelatedSearchModel()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @param $model
     * @throws Exception
     */
    protected function getEntityService($model)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @param $tableName
     * @throws Exception
     */
    protected function getServiceByEntityTable($tableName)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Добавление записи в бд
     *
     * @param Model $form
     * @throws Exception
     */
    public function create(Model $form)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * обновление записи в бд
     *
     * @param Model $form
     * @param ActiveRecord $model
     * @param bool $validate
     * @throws Exception
     */
    public function update(Model $form, ActiveRecord $model, $validate = true)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Обновление модели
     *
     * @param $model
     * @param array $data
     * @param string|null $formName
     * @param bool $validate
     * @param null $scenario
     * @throws Exception
     */
    public function updateByModel($model, $data, $formName = '', $validate = true, $scenario = null)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Обновление записи по id
     *
     * @param $id
     * @param array $data
     * @param string|null $formName
     * @param bool $validate
     * @param null $scenario
     * @throws Exception
     */
    public function updateById($id, $data, $formName = '', $validate = true, $scenario = null)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     *
     * TODO тут когда нибудь нужен рефактор, метод взят как есть из V3
     *
     * Сохранение формы
     *
     * @param Model $form класс для работы
     * @param ActiveRecord $model модель данных - передается при редактировании
     * @param bool $validate
     * @throws Exception
     */
    public function save(Model $form , ActiveRecord $model = null, $validate = true)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     *
     * удаление
     * @param ActiveRecord $model
     * @throws
     * @return boolean
     */
    public function delete(ActiveRecord $model)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     *
     * восстановление нефизически удаленной сущности
     *
     * @param ActiveRecord $model
     * @throws Exception
     */
    public function undelete(ActiveRecord $model)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @param $fields
     * @param $rows
     */
    protected function beforeBatchInsert($fields, $rows)
    {

    }

    /**
     * @param $fields
     * @param $rows
     */
    protected function afterBatchInsert($fields, $rows)
    {

    }
}
