<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options
// more details here: https://captcha.com/doc/php/captcha-options.html
// ----------------------------------------------------------------------------

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for example page
    |--------------------------------------------------------------------------
    */
    'NdeCaptcha' => [
        'UserInputID' => 'CaptchaCode',
        'CodeLength' => 4,
        'ImageWidth' => 250,
        'ImageHeight' => 50,
    ]
];