####Мини гайд по использованию dependency injection

Для переопределния любого класса можно вооспользоваться инекцией зависимостей через config.php
К примеру подменить модель StaticBlock на свой

    <?php
    return [
        'container' => [
            'definitions' => [
                'concepture\yii2article\models\StaticBlock' => ['class' => 'backend\models\StaticBlock'],
            ],
        ],
    ]
    
    
    
Для того чтобы работала dependency injection
все новые обьекты должны создаваться вот так

Yii::createObject($className);