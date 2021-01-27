<?php


namespace api\components;

use api\models\Usertoken;
use Yii;
use api\models\User;
use sizeg\jwt\Jwt;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;

class TokenComponent
{

    protected function _checkToken($_token, $checkIsLive = true)
    {
        $res = [];
        $res['status'] = 200;
        $res['name'] = 'Valid token';
        if ($_token !== null) {
            $token = $_token;
            $jwt = Yii::$app->jwt;
            try {
                $token = $jwt->getParser()->parse((string)$token); // Разбираем токен
                $token->getHeaders(); // Получаем заголовки токена
                $token->getClaims(); // Retrieves the token claims
                // проверяем, а есть ли пользователь с id, зашифрованным в токене
                $user = User::findIdentity($token->getClaim('uid'));
                if (!$user || $user->status !== User::STATUS_ACTIVE) {
                    throw new \InvalidArgumentException('Invalid token');
                }
                $res['status'] = 200;
                $res['name'] = 'Valid token';
                $res['currenttime'] = time();
                $res['expires'] = $token->getClaim('exp');
                $res['remaintime'] = $res['expires'] - time();
                $utq = Usertoken::findAll([]);
                // первым делом проверим токены в базе и удалим невалидные auth_token,
                // которые за собой утянут пару refresh_token - просрал, так просрал, авторизуйся заново
                $tokens = $utq->all();
                foreach ($tokens as $t) {
                    $atoken = $jwt->loadToken($t->auth_token);
                    if ($atoken === null) {
                        $t->valid_atoken = 0;
                        $t->save();
                    }
                }
                if ($checkIsLive && !$this->TokenIsLive($_token)) {
                    $res['status'] = 403;
                    $res['name'] = 'Invalid token';
                } else {
                    $res['user'] = [
                        'username' => $user->username,
                        'email' => $user->email,
                        'status' => $user->status,
                        'id' => $user->id,
                    ];
                }
            } catch (\InvalidArgumentException $e) {
                $res['status'] = 403;
                $res['name'] = 'Invalid token';
                $res['Exception'] = $e->getMessage();
                if (YII_ENV == 'dev') {
                    $res['Trace'] = preg_split('/\n/', $e->getTraceAsString());
                    $res['File'] = $e->getFile();
                    $res['Line'] = $e->getLine();
                    $res['Code'] = $e->getCode();
                }
            }
        } else {
            // нет токена в заголовках
            $res['status'] = 401;
            $res['name'] = 'Unauthorized';

        }
        return $res;
    }

    public function CheckToken()
    {
        // получаем токен из заголовка
        $authHeader = Yii::$app->request->getHeaders()->get('auth-token');
        // проверяем
        $res = $this->_checkToken($authHeader);
        // если токен валидный, обновим информацию о логине и IP
        if ($res['status'] == 200) {
            $ut = Usertoken::find()->where(['auth_token' => $authHeader])->one();
            if ($ut) {
                $ut->last_login = date('Y-m-d H:i:s', time());
                $ut->last_ip = $_SERVER['REMOTE_ADDR'];
                $ut->save();
            }
        }
        return $res;
    }

    protected function GenerateNewToken($time, $expires, $user)
    {
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();

        // Adoption for lcobucci/jwt ^4.0 version
        $token = $jwt->getBuilder()
            ->issuedBy(Yii::$app->params['jwt_issuer'])// Configures the issuer (iss claim)
            ->permittedFor(Yii::$app->params['jwt_audience'])// Configures the audience (aud claim)
            ->identifiedBy(Yii::$app->params['jwt_id'], true)// Configures the id (jti claim), replicating as a header item
            ->issuedAt($time)// Configures the time that the token was issue (iat claim)
            ->expiresAt($time + $expires)// Configures the expiration time of the token (exp claim)
            ->withClaim('uid', $user->id)// Configures a new claim, called "uid"
            ->getToken($signer, $key); // Retrieves the generated token
        return (string)$token;
    }


    /**
     * Проверка доступности email для регистрации
     * невалидный email вызывает ошибку 403
     * наличие такого emaila у уже зарегистрированного пользователя вызывает ошибку 403
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function CheckEmailForRegister()
    {
        $email = Yii::$app->request->post('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ForbiddenHttpException('Неверный формат email');
        } else {
            $user = User::findByEmail($email);
            if ($user) {
                throw new ForbiddenHttpException('Такой email уже зарегистрирован');
            }
        }
        return true;
    }

    /**
     * Проверка возможности регистрации пользователя, если такой пользователь уже зарегистрирован - ошибка 403
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function CheckUsernameForRegister()
    {
        $user = User::findByUsername(Yii::$app->request->post('username'));
        if ($user) {
            throw new ForbiddenHttpException('Такой пользователь уже зарегистрирован');
        }
        return true;
    }

    /**
     * Функция логина. Из post запроса берем username & password, проверяем.
     * В случае удачной авторизации выдаем два токена - основной и refresh
     * @return array
     * @throws UnauthorizedHttpException
     */
    public function Login()
    {
        $res = [];
        $user = User::findByUsername(Yii::$app->request->post('username'));
        if (!$user) {
            throw new UnauthorizedHttpException('Пользователь не найден');
        }
        if (!$user->validatePassword(Yii::$app->request->post('password'))) {
            throw new UnauthorizedHttpException('Не найден пользователь'); // хакерам ни к чему знать, что имя
            // пользователя они подобрали правильно и теперь только вопрос в подборе пароля )))
        }
        $time = time();
        $res['token'] = $this->GenerateNewToken($time, Yii::$app->params['jwt_auth_token_time'], $user);
        $res['refresh-token'] = $this->GenerateNewToken($time, Yii::$app->params['jwt_refresh_token_time'], $user);

        $ut = new Usertoken();
        $ut->user_id = $user->id;
        $ut->auth_token = $res['token'];
        $ut->refresh_token = $res['refresh-token'];
        $ut->valid_atoken = 1;
        $ut->valid_rtoken = 1;
        $ut->user_agent = Yii::$app->request->headers['User-Agent'];
        $ut->expires = date('Y-m-d H:i:s', $time + Yii::$app->params['jwt_auth_token_time']);
        $ut->issued_time = date('Y-m-d H:i:s', $time);
        $ut->save();
        return $res;
    }


    public function RegisterUser()
    {
        $this->CheckEmailForRegister();
        $this->CheckUsernameForRegister();
        $username = Yii::$app->request->post('username');
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $users = User::find()->all();
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        // для разработки не требуем верификации email, в продакшене возможно будет нужна верификация
        $user->status = Yii::$app->params['need_email_verification'] ? 9 : 10;
        $user->save();
        if (count($users) === 0) {
            // не было еще ни одного пользователя, первый зареганный юзер автоматом получает роль admin
            $am = Yii::$app->authManager;
            $role = $am->getRole('admin');
            if (!$role) {
                $role = $am->createRole('admin');
                $role->description = "Администратор";
                $am->add($role);
            }
            $am->assign($role, $user->id);
        }
        $res = [];
        $res['message'] = 'Пользователь зарегистрирован.';
        $res['message'] .= Yii::$app->params['need_email_verification'] ? ' Для входа необходимо подтвердить email' : '';
        $res['need_email_verification'] = Yii::$app->params['need_email_verification'];
        return $res;
    }

    /**
     * Проверка жизнеспособности токена
     * Токен может быть валидным и верифицированным с точки зрения даты истечения и удостоверения принадлежности
     * к сайту, но может быть отозванным или просто отсутствующим в базе. Вот это мы и проверяем
     * @param $token
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function TokenIsLive($token)
    {
        $jwt = Yii::$app->jwt;
        $otoken = $jwt->loadToken($token);
        if ($otoken === null) {
            return false;
        } else {
            $t = Yii::$app->db->createCommand('SELECT * FROM {{%usertoken}} WHERE auth_token=:token AND valid_atoken=1')
                ->bindValue(':token', $token)
                ->queryOne();
            if (!$t) {
                return false;
            }
        }
        return true;
    }

    public function Logout()
    {

    }
}