<?php


namespace api\actions;


use yii\rest\Action;

class ErrorAction extends Action
{
    public $modelClass='api\actions\ErrorAction';

    public function run(){
        return [
            'success'=>false,
        ];
    }

}