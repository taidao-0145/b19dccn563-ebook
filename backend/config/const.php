<?php

return [
    'validator_rules' => [
        'regex_time' => 'regex:/^((([0-1][0-9]|2[0-3]):([0-5][0-9])))$/',
        'regex_time_seconds' => 'regex:/^((([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])))$/',
    ],
    'httpStatusCode' => [
        'http_200' => 200,
        'http_400' => 400,
        'http_401' => 401,
        'http_403' => 403,
        'http_404' => 404,
        'http_409' => 409,
        'http_422' => 422,
        'http_500' => 500,
    ],
];
