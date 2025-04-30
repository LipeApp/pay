<?php

return [
    'default' => env('PAYMENT_GATEWAY_DEFAULT', 'payme'),

    'gateways' => [
        'payme' => [
            'enabled' => env('PAYME_ENABLED', true),
            'merchant_id' => env('PAYME_MERCHANT_ID'),
            'merchant_key' => env('PAYME_MERCHANT_KEY'),
            'merchant_login' => env('PAYME_MERCHANT_LOGIN'),
            'endpoint' => env('PAYME_ENDPOINT', 'https://checkout.paycom.uz/api'),
            'card_number_salt' => env('PAYME_CARD_NUMBER_SALT'),
            'card_token_salt' => env('PAYME_CARD_TOKEN_SALT'),
            'test_mode' => env('PAYME_TEST_MODE', true),
            'callback_url' => env('PAYME_CALLBACK_URL'),
        ],
        'click' => [
            'enabled' => env('CLICK_ENABLED', true),
            'merchant_id' => env('CLICK_MERCHANT_ID'),
            'merchant_user_id' => env('CLICK_MERCHANT_USER_ID'),
            'service_id' => env('CLICK_SERVICE_ID'),
            'secret_key' => env('CLICK_SECRET_KEY'),
            'endpoint' => env('CLICK_ENDPOINT', 'https://api.click.uz/v2/merchant/'),
            'endpoint_mini' => env('CLICK_ENDPOINT_MINI', 'https://my.click.uz/services/'),
            'return_url' => env('CLICK_RETURN_URL'),
            'test_mode' => env('CLICK_TEST_MODE', true),
            'callback_url' => env('CLICK_CALLBACK_URL'),
        ],
        'ipak_yuli' => [
            'enabled' => env('IPAK_YULI_ENABLED', true),
            'auth_url' => env('NEW_IPAKYULI_AUTH_URL'),
            'transfer_url' => env('NEW_IPAKYULI_TRANSFER_URL'),
            'success_url' => env('NEW_IPAKYULI_SUCCESS'),
            'fail_url' => env('NEW_IPAKYULI_FAIL'),
            'login' => env('NEW_IPAKYULI_LOGIN'),
            'password' => env('NEW_IPAKYULI_PASSWORD'),
            'cashbox_id' => env('NEW_IPAKYULI_CASHBOX_ID'),
            'callback_login' => env('NEW_IPAK_YULI_CALLBACK_LOGIN'),
            'callback_password' => env('NEW_IPAK_YULI_CALLBACK_PASSWORD'),
            'test_mode' => env('IPAK_YULI_TEST_MODE', true),
        ],
    ],

    'routes' => [
        'prefix' => 'api/payments',
        'middleware' => ['api'],
    ],

    'models' => [
        'transaction' => Lipe\Payment\Models\PaymentTransaction::class,
    ],
];
