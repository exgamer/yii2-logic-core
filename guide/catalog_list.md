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

5. Для глобальной модификации запроса при получении каталога можно использовать классы
   унаследованные от QueryActor.php.
    !!! Можно использовать например для сущностей с мультиязычной property для получения 
    в админке каталогов с языком приложения а не с языком домена !!!
    
    Можно указать параметр в backend/config/params.php
```php
<?php
$params = [
    'yii2logic' => [
        'catalogQueryGlobalExtendClass' => \concepture\yii2handbook\actors\db\LocaleBasedPropertyQueryActor::class
    ],
];

return $params;
```