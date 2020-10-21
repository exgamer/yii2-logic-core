<?php

namespace concepture\yii2logic\dataprocessor\v2;

use concepture\yii2logic\console\traits\OutputTrait;
use concepture\yii2logic\dataprocessor\DataHandlerInterface;
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
     * @return bool
     */
    public function isExecute()
    {
        return true;
    }

    /**
     * Действия до запуска query
     */
    public function beforeExecute(){}
    /**
     * дествия после завершения всех запросов
     */
    public function afterExecute(){}
    /**
     * Действия после завершения операции
     * @param type $data
     */
    public function finishProcessModel(&$data){}

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function afterPageProcess(){}
    public function beforePageProcess(){}
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
    public function processModel(&$data){}

    /**
     * настройка основного запроса
     * @param ActiveQuery $query
     * @param null $inputData
     */
    public function setupQuery(ActiveQuery $query){}

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

    /**
     * set temp data
     * @see DataProcessor::setData()
     * @param $data
     */
    public function setData($data)
    {
        $this->getProcessor()->setData($data);
    }

    /**
     * get temp data
     *
     * @param $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        return $this->getProcessor()->getData($key);
    }
}