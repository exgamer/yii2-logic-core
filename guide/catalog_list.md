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

!!!!
Если нужно в getListSearchAttribute вызвать метод, в модели 
создаем метод например 
```php
    public function getLabel()
    {
        return $this->domain."-".$this->id;
    }

```
и в методе getListSearchAttribute прописываем название метода без get и с маленькой буквы
```php

    public static function getListSearchAttribute()
    {
        return 'label';
    }

```
!!!!!

2. для получения записей таблицы как массива ключ=> значение 
вызываем Yii::$app->someService->catalog();

3. для получения ключа по значению Yii::$app->someService->catalogKey();

4. для получения значения по ключу Yii::$app->someService->catalogValue();
