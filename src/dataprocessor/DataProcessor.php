<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\services\Service;
use Yii;
use yii\base\Component;
use concepture\yii2logic\console\traits\OutputTrait;
use yii\helpers\Console;

/**
 * Class DataProcessor
 *
 *  $config = [
 *     'dataHandlerClass' => SitemapDataHandler::class,
 *     'pageSize' => 5
 * ];
 *
 * DataProcessor::exec($config);
 *
 * @package concepture\yii2logic\dataprocessor
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DataProcessor extends Component
{
    use OutputTrait;

    public $dataHandlerClass;
    public $dataHandler;
    public $queryCondition;
    public $pageSize = 50;
    public $isDone = false;
    public $totalCount;
    public $currentPage = 0;
    public $targetPage = 0;
    public $bySinglePage = false;

    /** @var \DateTime Время начала выполнения скрипта */
    protected $timeStart;

    public function printMemoryUsage($message = '')
    {
        $memory = memory_get_usage();
        $this->outputSuccess( "MEMORY USED : " . ($memory/(1024) ) . " - " . $message, 'red');
    }

    public static function exec($config, &$inputData = null)
    {
        $processor = new static($config);
        $processor->execute($inputData);
    }

    public function init()
    {
        parent::init();
        $dataHandlerClass = $this->dataHandlerClass;
        $this->dataHandler = new $dataHandlerClass();
        $this->timeStart = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
    }

    /**
     * выполняет запрос и обработку данных
     *
     * @param array $inputData
     * @return type
     */
    public function execute(&$inputData = null)
    {
        if (! $inputData){
            $inputData = [];
        }

        $this->beforeExecute($inputData);
        if (! $this->isExecute($inputData))
        {
            return true;
        }

        do {
            try{
                gc_collect_cycles();
                $this->_execute($inputData);
            } catch (\Exception $dbEx){
                $this->noDbConnectionExceptionActions([], $dbEx);
                continue;
            }
        } while (! $this->isDone());

        $this->afterExecute($inputData);

        return true;
    }

    /**
     * exec query
     *
     * @param array $inputData //массив с входными данными, например для внесения полученных данных
     *
     * @return boolean
     */
    public function _execute(&$inputData = null)
    {
        $models = $this->executeQuery($inputData);
        $this->outputSuccess( "START PROCESS PAGE : " . $this->currentPage . " of " . ceil($this->totalCount/$this->pageSize) );
        $this->beforePageProcess($inputData);
        $count = count($models);
        Console::startProgress(0, $count);
        foreach ($models as $k => $model) {
            try{
                $this->prepareModel($model);
                $this->processModel($model, $inputData);
                $this->finishProcessModel($model, $inputData);
                Console::updateProgress($k, $count);
            } catch (\Exception $dbEx){
                $this->noDbConnectionExceptionActions($model, $dbEx);
                continue;
            }
        }
        $this->afterPageProcess($inputData);
        if ($this->bySinglePage){
            $this->isDone = true;
        }

        $models = null;
        $memory = memory_get_usage()/1024;
        $this->outputSuccess( "END PROCESS PAGE : "  . $this->currentPage . " of " . ceil($this->totalCount/$this->pageSize) . "; MEMORY USED: {$memory}");

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isDone()
    {

        return $this->isDone;
    }

    protected function getQuery()
    {
        return $this->dataHandler->getQuery();
    }

    /**
     *  get rows by sql
     */
    private function executeQuery($inputData = null)
    {
        $query = $this->dataHandler->getQuery();
        $this->dataHandler->setupQuery($query, $inputData);
        $condition = $this->queryCondition;
        if (is_callable($condition)){
            call_user_func($condition, $query);
        }

        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        $config = [
            'pagination' => [
                'pageSize' => $this->pageSize,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'page' => $this->currentPage
            ],
            'query' => $query
        ];
        $dataProvider = $this->dataHandler->getDataProvider($config );
        $models = $dataProvider->getModels();
        if (! $this->totalCount) {
            $this->totalCount = $dataProvider->getTotalCount();
        }

        $this->currentPage = $dataProvider->getPagination()->getPage();
        if ($this->currentPage+1 == $dataProvider->getPagination()->getPageCount()){
            $this->isDone = true;
        }

        if ($dataProvider->getCount() == 0){
            $this->isDone = true;
        }

        $this->currentPage +=1;
        $query = null;
        $config = null;
        $dataProvider = null;

        return $models;
    }

    /**
     * return total row count
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public function beforeExecute(&$inputData = null)
    {
        $this->dataHandler->beforeExecute($this, $inputData);
    }

    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public function afterExecute(&$inputData = null)
    {
        $this->dataHandler->afterExecute($this, $inputData);
    }

    /**
     *
     * @param type $inputData
     * @return boolean
     */
    public function isExecute(&$inputData = null)
    {
        return $this->dataHandler->isExecute($this, $inputData);
    }

    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public function finishProcessModel(&$data, &$inputData = null)
    {
        $this->dataHandler->finishProcessModel($this, $data, $inputData);
    }

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function beforePageProcess(&$inputData = null)
    {
        $this->dataHandler->beforePageProcess($this, $inputData);
    }

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function afterPageProcess(&$inputData = null)
    {
        $this->dataHandler->afterPageProcess($this, $inputData);
    }

    /**
     * Дейсвия при недоступности БД
     *
     * @param array $data
     * @param \yii\db\Exception $exception
     */
    public function noDbConnectionExceptionActions($data, $exception)
    {
        echo " ОШИБКА!!!  ".$exception->getMessage().PHP_EOL;
    }

    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     *
     * return array
     */
    public function prepareModel(&$data)
    {
        $this->dataHandler->prepareModel($this, $data);
    }

    /**
     * Выполняем необходимые действия с данными
     */
    public function processModel(&$data, &$inputData = null)
    {
        $this->dataHandler->processModel($this, $data, $inputData);
    }
}