<?php
return [
    'jwt_issuer' => 'mylocalsite',
    'jwt_audience' => 'mylocalsite',
    'jwt_id' => '',
    'jwt_auth_token_time' => 3600 * 24 * 15, // время жизни выдаваемого токена 15 суток
    'jwt_refresh_token_time' => 3600 * 24 * 30, // время жизни рефреш-токена 30 суток
    'need_email_verification' => true, // для разработки не требуем верификации email, в продакшене, возможно, это будет нужно
];
