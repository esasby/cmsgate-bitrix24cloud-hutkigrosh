<?php

use esas\cmsgate\bitrix\InstallHelperBitrix24Cloud;

require_once((dirname(__FILE__)) . '/src/init.php');

try {
    $installHelper = new InstallHelperBitrix24Cloud();
    $installHelper->preinstall();
    $newHandler = $installHelper->addHandler();
    $installHelper->addPaysystem($newHandler->getCode(), dirname(__FILE__) . '/static/img/hutkigrosh_100x50.png');
    echo 'Installed!'; //todo
} catch (Exception $e) {
    echo 'Exception';
}
