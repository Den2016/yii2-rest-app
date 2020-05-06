<?php

namespace api\controllers;

use api\actions\ErrorAction;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        // немного перепишем стандартный вариант
        $behaviors = parent::behaviors();
        // always JSON response
        $behaviors['access'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],

        ];
        if (Yii::$app->request->method != 'OPTIONS') {
//            $behaviors['authenticator'] = [
//                'class' => JwtHttpBearerAuth::class,
//                'optional' => [
//                    'login',
//                ],
//            ];

//            $behaviors['access'] = [
//                'class' => ContentNegotiator::class,
//                'only' => ['login', 'logout', 'signup', 'api'],
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => ['login', 'signup'],
//                        'roles' => ['?'],
//                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['logout'],
//                        'roles' => ['@'],
//                    ],
////                    [
////                        'allow' => true,
////                        'actions' => ['menu'],
////                        'roles' => ['@'],
////                    ]
//                ],
//                //'denyCallback' => function($rule,$action){ return $this->Unauthorized(); }
//            ];
        }

        // setup CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Allow-Headers' => [
                    'Access-Control-Allow-Headers',
                    'Origin',
                    'Accept',
                    'X-Requested-With',
                    'Content-Type',
                    'Access-Control-Request-Method',
                    'Access-Control-Request-Headers',
                    'Auth-Token',
                    'Refresh-Token',
                ],
            ],

        ];


        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->asJson(['hello' => 'world']);
    }
//
//    /**
//     * Login action.
//     *
//     * @return string
//     */
//    public function actionLogin()
//    {
//        if (!Yii::$app->user->isGuest) {
//            return $this->asJson(Yii::$app->request->post());
////            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            $model->password = '';
//
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Logout action.
//     *
//     * @return string
//     */
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }
}
