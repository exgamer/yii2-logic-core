<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\console\traits\OutputTrait;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * Вспомогательный класс для обработки данных
 *
 * @author CitizenZet
 */
abstract class DataHandler implements DataHandlerInterface
{
    use OutputTrait;

    protected $processor;

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->getService()->getQuery();
    }

    /**
     * @param $config
     * @return ActiveDataProvider
     */
    public function getDataProvider($config)
    {
        return $this->getService()->getDataProvider([], $config);
    }

    /**
     * признак работать ли дальше
     * @param $processor
     * @param type $inputData
     * @return bool
     */
    public function isExecute(&$inputData = null)
    {
        return true;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public function beforeExecute(&$inputData = null){}
    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public function afterExecute(&$inputData = null){}
    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public function finishProcessModel(&$data, &$inputData = null){}

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function afterPageProcess(&$inputData = null){}
    public function beforePageProcess(&$inputData = null){}
    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     *
     * return array
     */
    public function prepareModel(&$data){}

    /**
     * Выполняем необходимые действия с данными
     */
    public function processModel(&$data, &$inputData = null){}

    /**
     * настройка основного запроса
     * @param ActiveQuery $query
     * @param null $inputData
     */
    public function setupQuery(ActiveQuery $query, $inputData = null){}

    /**
     * Показываем сообщение после выполнения
     */
    public function showMessage($isUpdate, $model, $endMessage){}

    /**
     * @return DataProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @param DataProcessor $processor
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }
}