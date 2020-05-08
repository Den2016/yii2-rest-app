<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Шаблон проекта RESTful API на Yii2</h1>
    <br>
</p>

Этот шаблон RESTful API на [Yii 2](http://www.yiiframework.com/), 
основанный на шаблоне приложения advanced. 

В шаблоне frontend оставлен практически без изменений, переделке подверглась
backend часть, которая теперь заточена под rest api. Аутентификация пользователей
делается с помощью JSON Web Token.

---
### Требования

Системные требования те же, что и для basic или advanced приложения на Yii2

---
## Установка

~~~ 
git clone https://github.com/Den2016/yii2-rest-app.git 
~~~

TODO много чего


### пример файла .htaccess для OpenServer

~~~
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
~~~
