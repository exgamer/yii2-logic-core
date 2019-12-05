<?php
namespace concepture\yii2logic\actions\web\localized;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;
/**
 * Экшон для прсомотра списка локализованных сущностей
 *
 * Class IndexLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    use LocalizedTrait;

    public $view = 'index';
    public $serviceMethod = 'getDataProvider';

    public function run($locale = null)
    {
        $searchClass = $this->getSearchClass();
        $searchModel = new $searchClass();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel::$current_locale = $this->getConvertedLocale($locale);
        $dataProvider =  $this->getService()->{$this->serviceMethod}(Yii::$app->request->queryParams);

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}