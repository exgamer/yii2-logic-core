####Мини гайд по использованию метода catalog сервиса

1. В search модели реалзиуем методы 

```php
    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'domain';
    }

```

2. Вызываем Yii::$app->someService->catalog();