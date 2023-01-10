<?php
if(!defined('read_config')) {
    die('Direct access not permitted');
}

const CONFIG_PDO_DSN = "pdo_dsn";
const CONFIG_PDO_USERNAME = 'pdo_username';
const CONFIG_PDO_PASSWORD = 'pdo_password';
const CONFIG_BRIDGE_HOST = 'bridge_host';
const CONFIG_APP_ID = 'config_app_id';
const CONFIG_APP_SECRET = 'config_app_secret';

return array(
    CONFIG_PDO_DSN => "mysql:host=127.0.0.1;dbname=cmsgate;charset=utf8",
    CONFIG_PDO_USERNAME => 'username',
    CONFIG_PDO_PASSWORD   => 'password',
    CONFIG_BRIDGE_HOST   => 'https://cmsgate-test.esas.by/cmsgate-bitrix24cloud-hutkigrosh',
    CONFIG_APP_ID   => 'APP_ID_FROM_BITRIX',
    CONFIG_APP_SECRET   => 'APP_SECRET_FROM_BITRIX'
);