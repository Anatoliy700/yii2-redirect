# Redirect расширение для Yii2

Производит редирек со старых ссылок на новые. База ссылок может быть содержаться в разных источниках. 
В данной версии реализован репозиторий для работы с CSV файлами.

## Установка

Устанвливается через [Composer](http://getcomposer.org/download/) 

```bash
composer require anatoliy700/yii2-redirect
```
или добавить 

```json
"anatoliy700/yii2-redirect": "*"
```

в разделе `require` вашего composer.json файла.

## Использование

Подключить в конфигурации приложения в разделе модули:

```php
'modules' => [
    'redirect' => [
         'class' => \yii\base\Module::class,
         'controllerNamespace' => 'anatoliy700\redirect\controllers',
         'layout' => '/index',
         'viewPath' => '@app/extensions/myExtensions/redirect/src/views'
    ]
]
```
```php
 'container' => [
        'definitions' => [
             'anatoliy700\redirect\IRedirect' => 'anatoliy700\redirect\Redirect',
             'anatoliy700\redirect\repositories\IRepository' => [
                 'class' => 'anatoliy700\redirect\repositories\csv\CSVRepository',
                 'filePath' => '@app/redirectFile/redirect.csv',
             ],
             'anatoliy700\redirect\models\IRedirectItem' => 'anatoliy700\redirect\models\RedirectItem'
        ],
        'singletons' => [
             'krok\configure\ConfigureInterface' => function () {
                 $configurable = [
                     'anatoliy700\redirect\Configurable'
                 ];
                 $serializer = Yii::createObject('krok\configure\serializers\SerializerInterface');
                 
                 return new \krok\configure\Configure($configurable, $serializer);
             }
        ]
  ]
```
```php
'components' => [
     'class' => \yii\web\ErrorHandler::class,
      'errorAction' => 'redirect'
]
```

Выше указана минимально необходимая конфигурация модуля.
