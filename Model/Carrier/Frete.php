<?php
namespace UpperSoftwares\Frete\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use \Psr\Log\LoggerInterface;
use \Magento\Shipping\Model\Rate\ResultFactory;
use \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

/**
 * Classe de Frete da Upper Softwares
 * @author Felippi Augusto de Souza Gomes
 * @category UpperSoftwares
 * @package UpperSoftwares_Frete
 */
class Frete extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string Carrier's Code
     */
    protected $_code = 'freteupper';

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig       [description]
     * @param ErrorFactory         $rateErrorFactory  [description]
     * @param LoggerInterface      $logger            [description]
     * @param ResultFactory        $rateResultFactory [description]
     * @param MethodFactory        $rateMethodFactory [description]
     * @param array                $data              [description]
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->getCarrierCode() => __($this->getConfigData('name'))];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        // $arquivoFrete = readfile('pub/media/freteup/arquivofrete.json');
        // $arquivoTransp = readfile('pub/media/transpup/arquivotransp.json');
        //
        // $arquivoJsonFrete  = json_decode($arquivoFrete, true);
        // $arquivoJsonTransp = json_decode($arquivoTransp, true);

        // foreach ($arquivoJsonTransp->{"0"} as $value) {
          /**
           * Set carrier's method data
           */
          $method->setCarrier($this->getCarrierCode());
          // $method->setCarrierTitle($this->getConfigData('title'));
          $method->setCarrierTitle($value);

          /*you can fetch shipping price from different sources over some APIs, we used price from config.xml - xml node price*/
          $amount = $this->getConfigData('price');

          /**
           * Displayed as shipping method under Carrier
           */
          $method->setMethod($this->getCarrierCode());
          $method->setMethodTitle($this->getConfigData('name'));

          $method->setPrice($amount);
          $method->setCost($amount);

          $result->append($method);
        // }

        return $result;
    }
}
