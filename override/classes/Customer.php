<?php
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\CoreException;
/***
 * Class CustomerCore
 */
class Customer extends CustomerCore
{
	/*
    * module: ps_customerfields
    * date: 2021-08-24 15:01:05
    * version: 1.0.1
    */
	
    public $sdi;
    public function __construct($id = null)
    {
        self::$definition['fields']['sdi'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName');
        parent::__construct($id);
    }
}
