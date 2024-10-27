<?php
/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */
namespace Lib\Framework\Core;

//SERVER CONFIG
define('RESOURCEPATH', './lib/framework/Resources/');
define('BASE_URI', $_ENV['BASE_URI']);
define('BASE_SITE', $_ENV['BASE_SITE']);
define('LANG', $_ENV['LANG']);
define('KEY_LENGTH', 255);
define('APP_PATH', $_ENV['APP_PATH']);
define('HEADER_SECRET_KEY', $_ENV['HEADER_SECRET_KEY']);
//DB CONFIG
define('host', $_ENV['DB_HOST']);
define('user', $_ENV['DB_USER']);
define('password', $_ENV['DB_PASS']);
define('dbname', $_ENV['DB_NAME']);
define('CONNECTION_STRING', array('host' => host, 'dbname' => dbname, 'user' => user, 'password' => password));
// MERCADOPAGO CONFIG
define('MP_APP_ID', $_ENV['MP_APP_ID']);
define('MP_PUBLIC_KEY', $_ENV['MP_PUBLIC_KEY']);
define('MP_ACCESS_TOKEN', $_ENV['MP_ACCESS_TOKEN']);
define('MP_CLIENT_ID', $_ENV['MP_CLIENT_ID']);
define('MP_CLIENT_SECRET', $_ENV['MP_CLIENT_SECRET']);
//MERCADO PAGO CONSTANTS
define('MP_STATUS_PENDING', 'pending');
define('MP_STATUS_APPROVED', 'approved');
define('MP_STATUS_AUTHORIZED', 'authorized');
define('MP_STATUS_IN_PROCESS', 'in_process');
define('MP_STATUS_IN_MEDIATION', 'in_mediation');
define('MP_STATUS_REJECTED', 'rejected');
define('MP_STATUS_CANCELLED', 'cancelled');
define('MP_STATUS_REFUNDED ', 'refunded');
define('MP_STATUS_CHARGED_BACK', 'charged_back');
define('MP_REDIRECT_URL_ONBOARDING', BASE_SITE . BASE_URI . 'URL');
define('MP_REDIRECT_URL_SUCCESS', BASE_SITE . BASE_URI . 'URL');
define('MP_REDIRECT_URL_CANCEL', BASE_SITE . BASE_URI . 'URL');
define('MP_REDIRECT_URL_ERROR', BASE_SITE . BASE_URI . 'URL');
//GOOGLE RECAPTCHA CONFIG
//Create your keys here: https://www.google.com/recaptcha/admin/create
define('GOOGLE_RECAPTCHA_SITE_KEY', $_ENV['GOOGLE_PUBLIC_KEY']);
define('GOOGLE_SECRET_KEY', '6LePIcMnAAAAAKCk6bOkQ3tCY6HAbl7c4EQWiw6v');
//SMTP SERVER CONFIG
define('_EMAIL_HOST', $_ENV['EMAIL_HOST']);
define('_EMAIL_SMTPAuth', $_ENV['EMAIL_SMTPAuth']);
define('_EMAIL_Username', $_ENV['EMAIL_Username']);
define('_EMAIL_Password', $_ENV['EMAIL_Password']);
define('_EMAIL_SMTPSecure', $_ENV['EMAIL_SMTPSecure']);
define('_EMAIL_Port', $_ENV['EMAIL_Port']);
define('APPDEFAULTCOOKIE', 'conexp');
define("MODE", "sandbox");