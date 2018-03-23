<?php

class Loewenstark_CurrencyEcb_Model_Currency_Import_Ecb
extends Mage_Directory_Model_Currency_Import_Abstract
{

    protected $_url = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    protected $_currencyRates = array();
    protected $_messages = array();

    /**
     * get Data from ecb.europa.eu
     */
    public function __construct()
    {
        $_httpClient = new Varien_Http_Client();
        $response = $_httpClient
                ->setUri($this->_url)
                ->setConfig(array('timeout' => 10))
                ->request('GET')
                ->getBody();
        $xml = simplexml_load_string($response);
        if (isset($xml->Cube))
        {
            foreach ($xml->Cube->Cube->Cube as $_cube)
            {
                $attr = $_cube->attributes();
                if (isset($attr['currency']) && isset($attr['rate']))
                {
                    $currency = (string) $attr['currency'];
                    $rate = (float) $attr['rate'];
                    $this->_currencyRates[$currency] = $rate;
                }
            }
        }
        if (empty($this->_currencyRates))
        {
            $this->_messages[] = Mage::helper('directory')->__('Could not load data from <a target="_blank" href="https://www.ecb.europa.eu/stats/policy_and_exchange_rates/euro_reference_exchange_rates/html/index.en.html">ecb.europa.eu</a>');
        }
    }

    /**
     * 
     * @param float $currencyFrom
     * @param float $currencyTo
     * @return float
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        if ($currencyFrom != 'EUR')
        {
            $this->_messages[] = Mage::helper('directory')->__('Your Currency is not EUR');
            return;
        }
        if (isset($this->_currencyRates[$currencyTo]))
        {
            return $this->_currencyRates[$currencyTo];
        }
        $this->_messages[] = Mage::helper('directory')->__('Your currency "%s" is not on <a target="_blank" href="https://www.ecb.europa.eu/stats/policy_and_exchange_rates/euro_reference_exchange_rates/html/index.en.html">ecb.europa.eu</a>', $currencyTo);
        return;
    }
}