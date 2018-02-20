<?php
 /**
 * @package mod_currency_cbrf
 * @author Rybalko Igor
 * @version 1.2.0
 * @copyright (C) 2018 http://wolfweb.com.ua
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
*/

defined('_JEXEC') or die('Restricted access');

class ModCurrencyCbrfHelper{

	static private $_instance;
	private function __conctract(){}
	private function __clone(){}

	static function getInstance(){
		if(!self::$_instance){
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function getCurrency($cache_time){
		$cache = JFactory::getCache('modCurrencyCbrf', '');
		$cache->setCaching(true);
		$cache->setLifeTime($cache_time);
		$rates = $cache->get('ratesCbrf');

		if(!$rates) {
			$rates = $this->_getCbrfRate();
			$cache->store($rates, 'ratesCbrf');
		}

		return $rates;
	}

	private function _roundRate($rate){
		$result = sprintf("%.2f", ceil( (float) str_replace(',', '.', (string) $rate) * 100) / 100);
		return $result;
	}

	private function _xmlAttribute($object, $attribute){
	    if(isset($object[$attribute])){
	        return (string) $object[$attribute];
	    }
	}

	private function _getCbrfRate(){
		$date  = getdate();

		if($date['mon'] < 10){
			$month =  '0' . $date['mon'];
		}else{
			$month = $date['mon'];
		}

		if($date['mday'] < 10){
			$mday =  '0' . $date['mday'];
		}else{
			$mday = $date['mday'];
		}

		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $mday . '/' .$month . '/' . $date['year']);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl_handle);
		curl_close($curl_handle);

		$currency = simplexml_load_string($xml);
		$doc_date = $this->_xmlAttribute($currency, 'Date');

		if ($doc_date) {

			foreach($currency as $v){
				switch ((string) $v->CharCode){
					case 'USD':
						$rateUSD = $this->_roundRate($v->Value);
						break;
					case 'EUR':
						$rateEUR = $this->_roundRate($v->Value);
						break;
				}
			}

			$result = array(
				'usd'   => $rateUSD,
				'eur'   => $rateEUR,
				'date'  => $doc_date
			);

			return $result;
		}
	}
}