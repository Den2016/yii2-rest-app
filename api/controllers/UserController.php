<?php

namespace api\controllers;

use api\components\RestJWTActiveController;
use api\models\User;

class UserController extends RestJWTActiveController
{
    public $modelClass = User::class;

}
