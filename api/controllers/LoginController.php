<?php


namespace api\controllers;

use api\components\TokenComponent;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;

class LoginController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login','signup'
            ],
        ];

        return $behaviors;
    }

    public function actionLogin(){
        $t = new TokenComponent();
        return $t->Login();
    }

    public function actionSignup()
    {
        $t =new TokenComponent();
        return $t->RegisterUser();
    }

    public function actionLogout()
    {
        $t = new TokenComponent();
        return $t->Logout();
    }
}