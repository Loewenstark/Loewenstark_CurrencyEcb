<?php

class Loewenstark_CurrencyEcb_Model_Observer
extends Mage_Core_Model_Abstract
{
    public function eventSetCurrencyRateService($event)
    {
        $origin = Mage::getSingleton('adminhtml/session')->setCurrencyRateService();
        if (!empty($origin))
        {
            $serivce = Mage::getStoreConfig('currency/import/service');
            Mage::getSingleton('adminhtml/session')->setCurrencyRateService($serivce);
        }
    }
}