https://github.com/yiimaker/yii2-social-share

```twig
    {{ use('/ymaker/social/share/widgets/SocialShare') }}
    
    {{ social_share_widget({
        'containerOptions': {
            'tag': 'div',
            'class': 'share__list'
        },
        'linkContainerOptions': {
            'tag': 'div',
            'class': 'share__item'
        },
        'configurator' : 'socialShare',
        'url'          : url(['/post/view'], {'seo_name' : model.seo_name}),
        'title'        : model.header,
        'description'  : model.anons,
        'imageUrl'     : cdn_image_path(model.image) ,
    }) }}
```

```php
<?php 
    use frontend\components\socialshare\drivers\Copy;
    use ymaker\social\share\drivers\Facebook;
    use ymaker\social\share\drivers\Odnoklassniki;
    use ymaker\social\share\drivers\Pinterest;
    use ymaker\social\share\drivers\Telegram;
    use ymaker\social\share\drivers\Tumblr;
    use ymaker\social\share\drivers\Twitter;
    use ymaker\social\share\drivers\Vkontakte;
    use ymaker\social\share\drivers\WhatsApp;
    use ymaker\social\share\drivers\Yahoo;

    return [
    'components' => [
        'socialShare' => [
            'class' => \ymaker\social\share\configurators\Configurator::class,
            'enableIcons' => true,
            'enableDefaultAsset' => false,
            'icons' =>  [
                Vkontakte::class     => 'icon icon-' . SocialEnum::VKONTAKTE,
                Facebook::class      => 'icon icon-' . SocialEnum::FACEBOOK,
                Twitter::class       => 'icon icon-' . SocialEnum::TWITTER,
                Pinterest::class     => 'icon icon-' . SocialEnum::VKONTAKTE,
                Telegram::class      => 'icon icon-' . SocialEnum::TELEGRAM,
                WhatsApp::class      => 'icon icon-wa-phone',
                Tumblr::class        => 'icon icon-' . SocialEnum::VKONTAKTE,
                Yahoo::class         => 'icon icon-' . SocialEnum::VKONTAKTE,
                Odnoklassniki::class => 'icon icon-' . SocialEnum::ODNOCLASSNIKI,
                Copy::class          => 'icon icon-copy',
            ],
            'socialNetworks' => [
                'telegram' => [
                    'class' => Telegram::class,
                    'options' => [
                        'onclick' => "return !window.open(this.href, 'telegram', 'width=500,height=500')"
                    ]
                ],
                'whatsapp' => [
                    'class' => WhatsApp::class,
                    'options' => [
                        'onclick' => "return !window.open(this.href, 'whatsapp', 'width=500,height=500')"
                    ]
                ],
                'twitter' => [
                    'class' => Twitter::class,
                    'options' => [
                        'onclick' => "return !window.open(this.href, 'twitter', 'width=500,height=500')"
                    ]
                ],
                'vkontakte' => [
                    'class' => Vkontakte::class,
                    'options' => [
                        'onclick' => "return !window.open(this.href, 'vkontakte', 'width=500,height=500')"
                    ]
                ],
                'facebook' => [
                    'class' => Facebook::class,
                    'options' => [
                        'onclick' => "return !window.open(this.href, 'facebook', 'width=500,height=500')"
                    ]
                ],
                'copy' => [
                    'class' => Copy::class,
                    'options' => [
                        'class' => 'js-copy-to-clipboard'
                    ]
                ],
            ],
        ],
   ]    ];   


```