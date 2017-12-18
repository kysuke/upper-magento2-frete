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
    if (!$this->getConfigFlag('active'))
    {
      return false;
    }

    /** @var \Magento\Shipping\Model\Rate\Result $result */
    $result = $this->_rateResultFactory->create();

    if ($this->getConfigData('transp_file_upload') != "")
    {
      $transpFileUpload = BP."/pub/media/transpup/".$this->getConfigData('transp_file_upload');
      $fileTransp = fopen($transpFileUpload, "r");
      $jsonTransp = fread($fileTransp, filesize ($transpFileUpload));
      fclose($fileTransp);
    }

    $jsonTransp = json_decode($jsonTransp, true);

    $cep = str_replace("-","",$request->getDestPostcode());

    foreach ($jsonTransp as $key => $value)
    {
      $cepJson = explode("~",$key);
      if ($cep >= $cepJson[0] && $cep <= $cepJson[1])
      {
        foreach ($value as $keyT => $transportadora)
        {
          /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
          $method = $this->_rateMethodFactory->create();
          $valorTotal = 0;
          foreach ($request->getAllItems() as $item)
          {
            /**
            * Set carrier's method data
            */
            $method->setCarrier($this->getCarrierCode());
            $method->setCarrierTitle($this->getConfigData("title"));

            $tipo = $item->getProductType();
            if ($tipo == 'configurable')
            {
              $product = $item->getProduct();
              $json = $product->getJsonFrete();
              $qty = $item->getQty();

              $limiteFrete = count($transportadora[0]);
              $calc = ceil($qty/$limiteFrete);
              $arrayCalc = range(0,$calc - 1);
              foreach ($arrayCalc as $arrayIn)
              {
                $qtyAux = 1;
                if ($qty > ($limiteFrete * ($arrayIn + 1)))
                {
                  $qtyAux = $limiteFrete;
                } else {
                  $qtyAux = $qty - ($limiteFrete * $arrayIn);
                }
                $qtdUso = $qtyAux;
                $valorTotal = $valorTotal + $transportadora[0][$qtdUso];
              }
            }
          }

          $method->setMethod($this->getCarrierCode());
          $method->setMethodTitle($keyT);

          $method->setPrice($valorTotal);
          $method->setCost($valorTotal);

          $result->append($method);
        }
      }
    }

    return $result;
  }
}
