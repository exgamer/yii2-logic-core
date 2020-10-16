<?php
namespace concepture\yii2logic\actions\traits;

use Yii;

/**
 * @deprecated
 *
 * Trait LocalizedTrait
 * @package concepture\yii2logic\actions\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait LocalizedTrait
{
    /**
     * Получить из запроса экшона локаль
     * по умолчанию вернет язык приложения
     *
     * @return string
     */
    protected function getLocale()
    {
        if (Yii::$app->getRequest()->getQueryParam('locale') === null) {
            $language = Yii::$app->language;
            if(is_string($language)) {
                list($languageIso, ) = @explode('-', $language);
                if($languageIso) {
                    $language = $languageIso;
                }
            }

            return $language;
        }

        return Yii::$app->getRequest()->getQueryParam('locale');
    }

    /**
     * Возвращает конвертированную локаль для сущности
     *
     * @param string $locale
     * @return mixed
     */
    protected function getConvertedLocale($locale = null)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $modelClass = $this->getService()->getRelatedModelClass();
        $localeConverterClass = $modelClass::getLocaleConverterClass();
        $localeId = $localeConverterClass::key($locale);
        if ($localeId != $locale){
            return Yii::$app->localeService->catalogKey('ru', 'id', 'locale');
        }

        return $localeId;
    }
}

