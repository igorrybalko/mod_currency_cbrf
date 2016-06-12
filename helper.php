<?php
 /**
 * @package mod_currency_cbrf
 * @author Rybalko Igor
 * @version 1.0
 * @copyright (C) 2016 http://wolfweb.com.ua
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
*/

defined('_JEXEC') or die('Restricted access');

class RatesCbrf{

	static private $_instance;
	private function __conctract(){}
	private function __clone(){}

	static function getInstance(){
		if(!self::$_instance){
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	function getCurrencyArray(){
   		return json_decode(file_get_contents(__DIR__.'/data.json'), true);
	}

	function roundRate($rate){
	    $result = ceil( (float) $rate * 100) / 100;
	     return $result;
	}

	function xml_attribute($object, $attribute){
	    if(isset($object[$attribute])){
	        return (string) $object[$attribute];
	    }
	}
}