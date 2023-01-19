<?php
namespace esas\cmsgate\hutkigrosh;

use esas\cmsgate\BridgeConnectorBitrix24;
use esas\cmsgate\security\CryptServiceImpl;
use Exception;
use PDO;

class BridgeConnectorHutkigroshBitrix24 extends BridgeConnectorBitrix24
{
    const PATH_BILL_ADD = '/api/bill/add';
    const PATH_BILL_VIEW = '/api/bill/view';
    const PATH_BILL_NOTIFY = '/api/bill/notify';
    const PATH_BILL_ALFACLICK = '/api/bill/alfaclick';

    /**
     * @var BridgeConfigHutkigroshBitrix24Cloud
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getPDO()
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO(
            $this->config->getPDO_DSN(),
            $this->config->getPDOUsername(),
            $this->config->getPDOPassword(),
            $opt);
    }


    public function isSandbox()
    {
        throw new Exception('Not implemented. Bitrix24Cloud bridge is working in mixed mode');
    }

    protected function createCryptService()
    {
        return new CryptServiceImpl('/opt/cmsgate/storage');
    }

    public function getHandlerActionUrl()
    {
        return $this->config->getBridgeHost() . self::PATH_BILL_ADD;
    }
}