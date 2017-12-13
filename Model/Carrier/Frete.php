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
  * [__construct description]
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

    // $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    // $arquivoFrete = file('');
    // $arquivoTransp = readfile('pub/media/transpup/arquivotransp.json');
    // $arquivoJsonFrete  = json_decode($arquivoFrete, true);
    // $arquivoJsonTransp = json_decode($arquivoTransp, true);
    //

    foreach ($request->getAllItems() as $item) {
      /**
      * Set carrier's method data
      */
      $method->setCarrier($this->getCarrierCode());
      $method->setCarrierTitle($this->getConfigData('title'));
      // $method->setCarrierTitle($value);

      $tipo = $item->getProductType();
      if ($tipo == 'configurable') {
        $product = $item->getProduct();
        $json = $product->getJsonFrete();
        $qty = $item->getQty();
        $cep = str_replace("-","",$request->getDestPostcode());
        $arrayJson = json_decode($json, true);
        foreach ($arrayJson as $key => $value) {
          $cepJson = explode("~",$key);
          if ($cep >= $cepJson[0] && $cep <= $cepJson[1]) {
            foreach ($value as $chave) {
              $arrayTeste = $chave;
            }
          }
        }

        $limiteFrete = count($arrayTeste);
        $calc = ceil($qty/$limiteFrete);
        $arrayCalc = range(0,$calc - 1);
        $valorTotal = 0;
        foreach ($arrayCalc as $arrayIn) {
          $qtyAux = 1;
          if ($qty > ($limiteFrete * ($arrayIn + 1))) {
            $qtyAux = $limiteFrete;
          } else {
            $qtyAux = $qty - ($limiteFrete * $arrayIn);
          }
          $qtdUso = $qtyAux;
          $valorTotal = $valorTotal + $arrayTeste[$qtdUso];
        }
        /*you can fetch shipping price from different sources over some APIs, we used price from config.xml - xml node price*/
        // $amount = $this->getConfigData('price');
        /**
        * Displayed as shipping method under Carrier
        */
        $method->setMethod($this->getCarrierCode());
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($valorTotal);
        $method->setCost($valorTotal);
      }
    }

    $result->append($method);

    return $result;
  }
}
