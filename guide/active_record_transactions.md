Для обеспечения транзакции при модификации данных в рамках ActiveRecord
    используется метод transactions
    в базовом *concepture\yii2logic\models\ActiveRecord*
    транзакция включена для всех операции по умолчанию для сценария default.
    В случае добавления новых сценариев при необходиомсти нужно расширить метод transactions
    
```php
    /**
     * Врубаем транзакции по уолчнию для всех случаев модификации данных для сценария default
     * Для использования в стандартном методе AR   ::isTransactional($operation)
     *
     * @return array
     */
    public function transactions()
    {
        return [
            'default' => self::OP_ALL
        ];
    }

```