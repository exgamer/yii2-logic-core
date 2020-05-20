<?php
namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\helpers\Url;

/**
 * Экшен для вывода списка
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    /**
     * @var string
     */
    public $view = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'getDataProvider';

    /**
     * @return string HTML
     */
    public function run()
    {
        $this->rememberUrl();
        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $this->extendSearch($searchModel);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Для доп обработки search модели
     * @param $searchModel
     */
    protected function extendSearch($searchModel){}
}