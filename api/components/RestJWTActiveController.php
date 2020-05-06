<?php


namespace api\components;
/**
 * базовый контроллер для всех создаваемых контроллеров. Реализует сериализацию данных,
 * настройку выдачи в формате JSON и JWT аутентификацию
 */

use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\Response;

class RestJWTActiveController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_JSON,
                ]
        ];
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login',
            ],
        ];
        return $behaviors;
    }

}