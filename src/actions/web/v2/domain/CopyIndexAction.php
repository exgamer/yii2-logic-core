<?php
namespace concepture\yii2logic\actions\web\v2\domain;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\ActiveQuery;

/**
 * Экшен для вывода списка дял копирования между доменами
 *
 * Class CopyIndexAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CopyIndexAction extends Action
{
    public $view = 'copy-index';
    public $serviceMethod = 'getDataProvider';

    public function run()
    {
        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $this->extendSearch($searchModel);
        $searchModel->load(Yii::$app->request->queryParams);
        if(! $searchModel->domain_id){
            $searchModel->domain_id = -1;
        }

        $models = $this->getService()->catalog(null, null, null, true);
        $modelIds = array_keys($models);
        Yii::$app->domainService->setVirtualDomainId($searchModel->domain_id);
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel, null , function (ActiveQuery $query) use ($modelIds){
            $query->andWhere(['not in','id', $modelIds]);
        });
        Yii::$app->domainService->clearVirtualDomainId();

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