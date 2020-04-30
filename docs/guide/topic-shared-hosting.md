Using advanced project template at shared hosting
=================================================

Deploying an advanced project template to shared hosting is a bit trickier than a basic one because it has two webroots,
which shared hosting webservers don't support. We will need to adjust the directory structure so frontend URL will be
`http://site.test` and api URL will be `http://site.test/admin`.

### Move entry scripts into single webroot

First of all we need a webroot directory. Create a new directory and name it to match your hosting webroot name,
e.g., `www` or `public_html` or the like. Then create the
following structure where `www` is the hosting webroot directory you just created:

```
www
    admin
api
common
console
environments
frontend
...
```

`www` will be our frontend directory so move the contents of `frontend/web` into it. Move the contents of `api/web`
into `www/admin`. In each case you will need to adjust the paths in `index.php` and `index-test.php`.

### Adjust sessions and cookies

Originally the api and frontend are intended to run at different domains. When we’re moving it all to the same domain
the frontend and api will be sharing the same cookies, creating a clash. In order to fix it, adjust api application config
`api/config/main.php` as follows:

```php
'components' => [
    'request' => [
        'csrfParam' => '_csrf-api',
        'csrfCookie' => [
            'httpOnly' => true,
            'path' => '/admin',
        ],
    ],
    'user' => [
        'identityClass' => 'common\models\User',
        'enableAutoLogin' => true,
        'identityCookie' => [
            'name' => '_identity-api',
            'path' => '/admin',
            'httpOnly' => true,
        ],
    ],
    'session' => [
        // this is the name of the session cookie used for login on the api
        'name' => 'advanced-api',
        'cookieParams' => [
            'path' => '/admin',
        ],
    ],
],
```

### Alternative setup

If the way to set up template provided above doesn't work for you, try
[configs and docs by Oleg Belostotskiy](https://github.com/mickgeek/yii2-advanced-one-domain-config).
