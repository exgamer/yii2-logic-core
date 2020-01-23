####Мини гайд по генерации сущностей и crud

1. Для использования генератора подключаем gii в config/main-local.php

```php
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'concepture\yii2logic\gii\model\Generator',
            ],
            'crud' => [
                'class' => 'concepture\yii2logic\gii\crud\Generator',
            ],
        ],
        'allowedIPs' => ['*']
    ];
```

2. заходим в gii /gii и выбираем Model Generator где откроется форма
    - в форме указываем название таблицы
    - щелкаем по полю Model Class Name
    - Жмем кнопку Preview
    - Внизу формы появится список фаилов которые будут созданы
    - так же будет предложено переписать фаил services.php для регистрации нового сервиса. для этого нужно поставить галочку напротив фаила.
    - Нажать Generate
    
3. Для генерации Crud (контроллера и представлений для модели) заходим в CRUD generator
    - указываем Model Class с namespace
    - Жмем кнопку Preview
    - Нажать Generate