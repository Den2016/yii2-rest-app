Creando enlaces desde el api al fronted
===========================================

Frecuentemente se necesita crear enlaces de la aplicación api a la aplicación frontend. Dado que la aplicación frontend puede contener sus propias
reglas del gestor de URL puedes necesitar duplicarlo para la aplicación api nombrandolo diferente:

```php
return [
    'components' => [
        'urlManager' => [
            // here is your normal api url manager config
        ],
        'urlManagerFrontend' => [
            // here is your frontend URL manager config
        ],

    ],
];
```

Una vez hecho, puedes coger una URL apuntando al frontend de la siguiente manera:

```php
echo Yii::$app->urlManagerFrontend->createUrl(...);
```
