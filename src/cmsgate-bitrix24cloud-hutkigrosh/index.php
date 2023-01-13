<?php

use esas\cmsgate\hutkigrosh\BridgeConnectorHutkigroshBitrix24;
use esas\cmsgate\protocol\RequestParamsBitrix24Cloud;
use esas\cmsgate\BridgeConnector;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshBitrix24Cloud;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\SessionUtilsBridge;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = $_SERVER['REDIRECT_URL'];
$logger = LoggerCms::getLogger('index');

if (strpos($request, 'api') !== false) {
    try {
        $logger->info('Got request from Bitrix24: ' . $_REQUEST);
        if (StringUtils::endsWith($request, BridgeConnectorHutkigroshBitrix24::PATH_BILL_ADD)) {
            BridgeConnector::fromRegistry()->getShopConfigService()->checkAuthAndLoadConfig($_REQUEST);
            BridgeConnector::fromRegistry()->getOrderCacheService()->addSessionOrderCache($_REQUEST);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerHutkigroshAddBill();
                $controller->process($orderWrapper);
            }
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (strpos($request, BridgeConnectorHutkigroshBitrix24::PATH_BILL_VIEW) !== false) {
            $uuid = RequestParamsBitrix24Cloud::getOrderId();
            SessionUtilsBridge::setOrderCacheUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (StringUtils::endsWith($request, BridgeConnectorHutkigroshBitrix24::PATH_BILL_ALFACLICK)) {
            $controller = new ControllerHutkigroshAlfaclick();
            $controller->process();
        } elseif (strpos($request, BridgeConnectorHutkigroshBitrix24::PATH_BILL_NOTIFY) !== false) {
            $extId = $_REQUEST[RequestParamsHutkigrosh::PURCHASE_ID];
            BridgeConnector::fromRegistry()->getOrderCacheService()->loadSessionOrderCacheByExtId($extId);
            $controller = new ControllerHutkigroshNotify();
            $controller->process($extId);
        } else {
            http_response_code(404);
            return;
        }
    } catch (Exception $e) {
        $logger->error("Exception", $e);
        $errorPage = RegistryHutkigroshBitrix24Cloud::getRegistry()->getCompletionPage(
            Registry::getRegistry()->getOrderWrapperForCurrentUser(),
            null
        );
        $errorPage->render();
    } catch (Throwable $e) {
        $logger->error("Exception", $e);
        $errorPage = RegistryHutkigroshBitrix24Cloud::getRegistry()->getCompletionPage(
            Registry::getRegistry()->getOrderWrapperForCurrentUser(),
            null
        );
        $errorPage->render();
    }
} else {
    http_response_code(200);
}

