<?php

namespace UpperSoftwares\Frete\Model\Config\Backend;

class CustomFileType extends \Magento\Config\Model\Config\Backend\File
{
  // const UPLOAD_DIR = 'teste'; // Folder save image

  /**
  * Return path to directory for upload file
  *
  * @return string
  * @throw \Magento\Framework\Exception\LocalizedException
  */
  // protected function _getUploadDir()
  // {
  //   return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
  // }

  /**
  * Makes a decision about whether to add info about the scope.
  *
  * @return boolean
  */
  // protected function _addWhetherScopeInfo()
  // {
  //   return true;
  // }

  /**
  * @return string[]
  */
  public function getAllowedExtensions() {
    return ['csv', 'xls', 'txt', 'json'];
  }
}
