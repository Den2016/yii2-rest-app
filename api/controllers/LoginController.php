<?php


namespace api\controllers;

use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use sizeg\jwt\Jwt;
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
                'create','login','signupb'
            ],
        ];

        return $behaviors;
    }

    /**
     * @return \yii\web\Response
     */

    public function actionCreate()
    {
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();


        // Adoption for lcobucci/jwt ^4.0 version
        $token = $jwt->getBuilder()
            ->issuedBy(Yii::$app->params['jwt_issuer'])// Configures the issuer (iss claim)
            ->permittedFor(Yii::$app->params['jwt_audience'])// Configures the audience (aud claim)
            ->identifiedBy(Yii::$app->params['jwt_id'], true)// Configures the id (jti claim), replicating as a header item
            ->issuedAt($time)// Configures the time that the token was issue (iat claim)
            ->expiresAt($time + 3600)// Configures the expiration time of the token (exp claim)
            ->withClaim('uid', 100)// Configures a new claim, called "uid"
            ->getToken($signer, $key); // Retrieves the generated token

        return $this->asJson([
            'token' => (string)$token,
        ]);
    }

    public function actionLogin(){
        return $this->actionCreate();
    }

    public function actionSignup()
    {
        return [];
    }
    public function actionIndex()
    {
        return [];
    }

    public function actionUpdate()
    {
        return [];
    }
}