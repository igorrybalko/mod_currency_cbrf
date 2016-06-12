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

require_once dirname(__FILE__) . '/helper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$ratesCbrf = RatesCbrf::getInstance();
$data = $ratesCbrf->getCurrencyArray();

require JModuleHelper::getLayoutPath('mod_currency_cbrf', $params->get('layout', 'default')); 