<?php
/**
 * Created by PhpStorm.
 * User: vanroyevictorien
 * Date: 04/09/15
 * Time: 09:55
 */

$app['skyflow.config'] = [
    'security' => [
        'key'     => 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3',
        'crypt'   => null,
        'uncrypt' => null,
        'createSalt' => null,
    ]
];

if($app['dev'] === false) {
    $app['skyflow.config'] = [
        'security' => [
            'crypt'      => function ($dataPlain, $salt,$app) {
                $key_str           = substr_replace($app['skyflow.config']['security']['key'], $salt, 0, strlen($salt));
                $key               = pack('H*', $key_str);
                $iv_size           = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
                $iv                = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                $ciphertext        = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key,
                                                    $dataPlain, MCRYPT_MODE_CBC, $iv);
                $ciphertext        = $iv . $ciphertext;
                $ciphertext_base64 = base64_encode($ciphertext);

                return $ciphertext_base64;
            },
            'uncrypt'    => function ($dataCrypt, $salt,$app) {
                $ciphertext_dec = base64_decode($dataCrypt);
                $iv_size        = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
                $iv_dec         = substr($ciphertext_dec, 0, $iv_size);
                $ciphertext_dec = substr($ciphertext_dec, $iv_size);
                $key_str        = substr_replace($app['skyflow.config']['security']['key'], $salt, 0, strlen($salt));
                $key            = pack('H*', $key_str);
                $plaintext_dec  = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key,
                                                 $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

                return $plaintext_dec;
            },
            'createSalt' => function () {
                $randString   = "";
                $charUniverse = "abcdef0123456789";
                for ($i = 0; $i < 64; $i++) {
                    $randInt    = rand(0, 15);
                    $randChar   = $charUniverse[$randInt];
                    $randString = $randString . $randChar;
                }

                return $randString;
            }
        ]
    ];
}else{
    $app['skyflow.config'] = [
        'security' => [
            'crypt'      => function ($dataPlain, $salt) use ($app) {
                return $dataPlain;
            },
            'uncrypt'    => function ($dataCrypt, $salt) use ($app) {
                return $dataCrypt;
            },
            'createSalt' => function () {
                $randString   = "";
                $charUniverse = "abcdef0123456789";
                for ($i = 0; $i < 64; $i++) {
                    $randInt    = rand(0, 15);
                    $randChar   = $charUniverse[$randInt];
                    $randString = $randString . $randChar;
                }

                return $randString;
            }
        ]
    ];
}