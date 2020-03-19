<?php
namespace concepture\yii2logic\forms;


use Yii;

/**
 * Базовая форма для линкушек
 *
 * Class LinkForm
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class LinkForm extends Form
{
    public $entity_id;
    public $linked_id;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'entity_id',
                    'linked_id'
                ],
                'required'
            ],
        ];
    }
}
