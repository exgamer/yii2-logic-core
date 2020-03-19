####Мини гайд по использованию моделей с нефизическим удалением


1. Для нефизического удаления таблица должна содержать поле is_deleted типа smallint
2. Унаследовать модель AR от *concepture\yii2logic\models\ActiveRecord*
3. Выставить метку public $allow_physical_delete = false;
4. Для контроллера подключить экшен для восстановления если нужно UndeleteAction или UndeleteLocalizedAction

Пример: 

```php
  <?php
  namespace concepture\yii2banner\models;
  
  use concepture\yii2banner\enum\BannerTypesEnum;
  use concepture\yii2user\models\User;
  use Yii;
  use concepture\yii2logic\models\ActiveRecord;
  use concepture\yii2logic\validators\TranslitValidator;
  use concepture\yii2logic\models\traits\HasLocalizationTrait;
  use concepture\yii2logic\models\traits\StatusTrait;
  use concepture\yii2logic\models\traits\IsDeletedEnum;
  use concepture\yii2handbook\converters\LocaleConverter;
  use concepture\yii2handbook\models\traits\DomainTrait;
  use concepture\yii2banner\models\traits\BannerTrait;
  use concepture\yii2banner\models\traits\BannerUrlLinkTrait;
  
  /**
   * Banner model
   *
   * @property integer $id
   * @property integer $domain_id
   * @property integer $user_id
   * @property integer $locale
   * @property string $title
   * @property string $content
   * @property string $seo_name
   * @property string $image
   * @property string $url
   * @property string $target
   * @property integer $status
   * @property datetime $from_at
   * @property datetime $to_at
   * @property datetime $created_at
   * @property datetime $updated_at
   *
   * @author Olzhas Kulzhambekov <exgamer@live.ru>
   */
  class Banner extends ActiveRecord
  {
      public $allow_physical_delete = false;
  
      use HasLocalizationTrait;
      use StatusTrait;
      use IsDeletedEnum;
      use DomainTrait;
      use BannerTrait;
      use BannerUrlLinkTrait;
  
      public $locale;
      public $title;
      public $content;
      public $image;
      public $from_at;
      public $to_at;
      public $url;
      public $target;
  
  
      /**
       * {@inheritdoc}
       */
      public static function tableName()
      {
          return '{{banner}}';
      }
  
      /**
       * {@inheritdoc}
       */
      public function rules()
      {
          return [
              [
                  [
                      'status',
                      'user_id',
                      'domain_id',
                      'locale',
                      'type'
                  ],
                  'integer'
              ],
              [
                  [
                      'content'
                  ],
                  'string'
              ],
              [
                  [
                      'title',
                      'seo_name',
                      'url',
                      'image',
                  ],
                  'string',
                  'max'=>1024
              ],
              [
                  [
                      'target'
                  ],
                  'string',
                  'max'=>20
              ],
              [
                  [
                      'seo_name',
                  ],
                  TranslitValidator::className(),
                  'source' => 'title'
              ],
              [
                  [
                      'seo_name',
                  ],
                  'unique'
              ],
              [
                  [
                      'from_at',
                      'to_at'
                  ],
                  'date',
                  'format' => 'php:Y-m-d'
              ],
              [
                  [
                      'from_at',
                      'to_at'
                  ],
                  'default',
                  'value' => null
              ],
              ['from_at', 'compare', 'compareAttribute' => 'to_at', 'operator' => '<=', 'enableClientValidation' => false]
          ];
      }
  
      public function attributeLabels()
      {
          return [
              'id' => Yii::t('banner','#'),
              'type' => Yii::t('banner','Тип баннера'),
              'user_id' => Yii::t('banner','Пользователь'),
              'domain_id' => Yii::t('banner','Домен'),
              'status' => Yii::t('banner','Статус'),
              'locale' => Yii::t('banner','Язык'),
              'title' => Yii::t('banner','Название'),
              'content' => Yii::t('banner','Контент'),
              'seo_name' => Yii::t('banner','SEO название'),
              'sort' => Yii::t('banner','Сортировка'),
              'image' => Yii::t('banner','Изображение'),
              'from_at' => Yii::t('banner','Дата с'),
              'to_at' => Yii::t('banner','Дата по'),
              'url' => Yii::t('banner','Ссылка'),
              'target' => Yii::t('banner','Атрибут target ссылки'),
              'created_at' => Yii::t('banner','Дата создания'),
              'updated_at' => Yii::t('banner','Дата обновления'),
              'id_deleted' => Yii::t('banner','Удален'),
          ];
      }
  
      public function afterSave($insert, $changedAttributes)
      {
          $this->saveLocalizations();
  
          return parent::afterSave($insert, $changedAttributes);
      }
  
      public function afterDelete()
      {
          $this->deleteLocalizations();
  
          return parent::afterDelete();
      }
  
      public function afterFind()
      {
          $this->setLocalizations();
  
          return parent::afterFind();
      }
  
      public static function getLocaleConverterClass()
      {
          return LocaleConverter::class;
      }
  
      /**
       * Возвращает метку для типа баннера
       *
       * @return string
       */
      public function getBannerTypeLabel()
      {
          return BannerTypesEnum::label($this->type);
      }
  }
 

```