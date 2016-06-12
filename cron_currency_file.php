<?php
 /**
 * @package mod_currency_cbrf
 * @author Rybalko Igor
 * @version 1.0
 * @copyright (C) 2016 http://wolfweb.com.ua
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
*/

define( '_JEXEC', 1 );

require_once dirname(__FILE__) . '/helper.php';

$date  = getdate();
$ratesCbrf = RatesCbrf::getInstance();

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
$doc_date = $ratesCbrf->xml_attribute($currency, 'Date');

if ($doc_date) {

    foreach($currency as $v){
        switch ((string) $v->CharCode){
            case 'USD':
                $rateUSD = $ratesCbrf->roundRate(str_replace(',', '.', (string) $v->Value));
                break;
            case 'EUR':
                $rateEUR = $ratesCbrf->roundRate(str_replace(',', '.', (string) $v->Value));
                break;
        }
    }

    $result = array(
        'usd'   => $rateUSD,
        'eur'   => $rateEUR,
        'date'  => $doc_date
    );

    if($result) {
        file_put_contents(dirname(__FILE__) . '/data.json', json_encode($result));
    }
}