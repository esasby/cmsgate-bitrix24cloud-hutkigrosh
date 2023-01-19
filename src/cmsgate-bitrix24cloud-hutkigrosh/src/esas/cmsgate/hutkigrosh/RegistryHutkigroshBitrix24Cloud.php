<?php
namespace esas\cmsgate\hutkigrosh;

use esas\cmsgate\protocol\RequestParamsBitrix24Cloud;
use esas\cmsgate\BridgeConnector;
use esas\cmsgate\CmsConnectorBitrix24Cloud;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hutkigrosh\view\client\CompletionPageHutkigrosh;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelHutkigroshBitrix24Cloud;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\SessionUtilsBridge;
use esas\cmsgate\utils\URLUtils;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormBitrix24Cloud;
use Exception;

class RegistryHutkigroshBitrix24Cloud extends RegistryHutkigrosh
{
    public function __construct()
    {
        $config = new BridgeConfigHutkigroshBitrix24Cloud();

        $this->cmsConnector = new CmsConnectorBitrix24Cloud($config);
        $this->paysystemConnector = new PaysystemConnectorHutkigrosh();
        $this->registerService(BridgeConnector::BRIDGE_CONNECTOR_SERVICE_NAME, new BridgeConnectorHutkigroshBitrix24($config));
    }

    /**
     * Переопределение для упрощения типизации
     * @return $this
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @throws \Exception
     */
    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsExcept(AdminViewFields::CONFIG_FORM_COMMON, [
            ConfigFieldsHutkigrosh::paymentMethodName(),
            ConfigFieldsHutkigrosh::paymentMethodNameWebpay(),
            ConfigFieldsHutkigrosh::paymentMethodDetails(),
            ConfigFieldsHutkigrosh::paymentMethodDetailsWebpay(),
            ConfigFieldsHutkigrosh::useOrderNumber(),
            ConfigFieldsHutkigrosh::shopName()]);
        $configForm = new ConfigFormBitrix24Cloud(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields);
        return $configForm;
    }


    function getUrlAlfaclick($orderWrapper)
    {
        return "";
    }

    function getUrlWebpay($orderWrapper)
    {
        $currentURL = URLUtils::getCurrentURLNoParams();
        $currentURL = str_replace(BridgeConnectorHutkigroshBitrix24::PATH_BILL_ADD, BridgeConnectorHutkigroshBitrix24::PATH_BILL_VIEW, $currentURL);
        if (strpos($currentURL, BridgeConnectorHutkigroshBitrix24::PATH_BILL_VIEW) !== false) {
            return $currentURL
                . '?' . RequestParamsBitrix24Cloud::ORDER_ID . '=' . SessionUtilsBridge::getOrderCacheUUID();
        }
        else
            throw new CMSGateException('Incorrect URL generation');
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "bitrix24cloud-hutkigrosh",
            new VersionDescriptor("1.17.1", "2023-01-19"),
            "Bitrix24 Cloud Hutkigrosh",
            "https://github.com/esasby/cmsgate-bitrix24cloud-hutkigrosh/src/master/",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelHutkigroshBitrix24Cloud($orderWrapper);
    }

    /**
     * @param $orderWrapper
     * @param $completionPanel
     * @return CompletionPageHutkigrosh
     */
    public function getCompletionPage($orderWrapper, $completionPanel)
    {
        return new CompletionPageHutkigrosh($orderWrapper, $completionPanel);
    }

    public function createHooks()
    {
        return new HooksHutkigroshBitrix24Cloud();
    }
}