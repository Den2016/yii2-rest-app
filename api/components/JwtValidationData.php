<?php


namespace api\components;

use Yii;

class JwtValidationData extends \sizeg\jwt\JwtValidationData
{
    public function init()
    {
        $this->validationData->setIssuer(Yii::$app->params['jwt_issuer']);
        $this->validationData->setAudience(Yii::$app->params['jwt_audience']);
        $this->validationData->setId(Yii::$app->params['jwt_id']);
        parent::init();
    }
}