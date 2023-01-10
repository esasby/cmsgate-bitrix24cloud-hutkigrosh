<?php
namespace esas\cmsgate\hutkigrosh;

use esas\cmsgate\CmsConnectorBitrix24Cloud;
use esas\cmsgate\hutkigrosh\protocol\HutkigroshBillInfoRs;
use esas\cmsgate\hutkigrosh\protocol\HutkigroshBillNewRs;
use esas\cmsgate\OrderStatus;
use esas\cmsgate\OrderStatusBridge;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\OrderWrapper;

class HooksHutkigroshBitrix24Cloud extends HooksHutkigrosh
{


    public function onAddBillSuccess(OrderWrapper $orderWrapper, HutkigroshBillNewRs $resp) {
        $orderWrapper->saveExtId($resp->getBillId());
        $this->updateStatues($orderWrapper, OrderStatusBridge::pending(), Registry::getRegistry()->getConfigWrapper()->getOrderStatusPending(), $resp->getBillId());
    }

    public function onAddBillFailed(OrderWrapper $orderWrapper, HutkigroshBillNewRs $resp) {
        $this->updateStatues($orderWrapper, OrderStatusBridge::failed(), Registry::getRegistry()->getConfigWrapper()->getOrderStatusFailed());
    }


    private function updateStatues(OrderWrapper $orderWrapper, OrderStatus $newBridgeStatus, $newCmsStatus, $extId = null) {
        $setPayed = $newBridgeStatus->getOrderStatus() == OrderStatusBridge::payed()->getOrderStatus();
        $orderWrapper->updateStatusWithLogging($newBridgeStatus);
        CmsConnectorBitrix24Cloud::fromRegistry()->getBitrix24Api(true)->salePayment()->updateStatus(
            $orderWrapper->getPaymentId(),
            $newCmsStatus,
            $setPayed);
        CmsConnectorBitrix24Cloud::fromRegistry()->getBitrix24Api(true)->saleOrder()->updateStatus(
            $orderWrapper->getOrderId(),
            $newCmsStatus,
            $setPayed);
        if (!empty($extId))
            CmsConnectorBitrix24Cloud::fromRegistry()->getBitrix24Api(true)->salePayment()->saveExtId(
                $orderWrapper->getPaymentId(),
                $orderWrapper->getExtId()
            );
    }

    public function onNotifyStatusPending(OrderWrapper $orderWrapper, HutkigroshBillInfoRs $resp) {

    }

    public function onNotifyStatusPayed(OrderWrapper $orderWrapper, HutkigroshBillInfoRs $resp) {
        $this->updateStatues($orderWrapper, OrderStatusBridge::payed(), Registry::getRegistry()->getConfigWrapper()->getOrderStatusPayed());
    }

    public function onNotifyStatusCanceled(OrderWrapper $orderWrapper, HutkigroshBillInfoRs $resp) {
        $this->updateStatues($orderWrapper, OrderStatusBridge::canceled(), Registry::getRegistry()->getConfigWrapper()->getOrderStatusCanceled());
    }
}