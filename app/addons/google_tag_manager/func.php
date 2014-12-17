<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_settings_variants_addons_google_tag_manager_brand_id()
{
	$params = $_REQUEST;
	$params['plain'] = true;
	list($features, $search, $has_ungroupped) = fn_get_product_features($params, 0, DESCR_SL);

	$variants = array('' => __('empty'));
	foreach ($features as $data) {
		$variants[$data['feature_id']] = $data['description'];
	}

	return $variants;
}

function fn_google_tag_manager_get_product_brand($product_id)
{
	$brand = '';
    
	if (!empty($product_id)) {
        
		$params['product_id'] = $product_id;
		$params['feature_id'] = Registry::get('addons.google_tag_manager.brand_id');
		$params['selected_only'] = true;
        
		list($data, $search) = fn_get_product_feature_variants($params);
        
		$data = array_shift($data);
		$brand = $data['variant'];
        
	}

	return $brand;
}

function fn_google_tag_manager_init_checkout($x)
{
	$_SESSION['init_checkout'] = $x;
}

function fn_google_tag_manager_get_main_category($product_id, $lang_code = DESCR_SL)
{
    $category = '';
    
    if (!empty($product_id)) {
        $join = db_quote('LEFT JOIN ?:category_descriptions AS cd ON cd.category_id = pc.category_id AND cd.lang_code = ?s', $lang_code);
        $category = db_get_field('SELECT cd.category FROM ?:products_categories AS pc ?p WHERE pc.product_id = ?i AND pc.link_type = ?s', $join, $product_id, 'M');
    }

    return $category;
}

function fn_google_tag_manager_get_payment_name($payment_id, $lang_code = DESCR_SL)
{
	$payment = '';
	if (!empty($payment_id)) {
		$payment = db_get_field('SELECT payment FROM ?:payment_descriptions WHERE payment_id = ?i AND lang_code = ?s', $payment_id, $lang_code);
	}
	return $payment;
}

function fn_google_tag_manager_get_order_status_sign($order_status)
{
    $sign = '-';
    if (!empty($order_status)) {
        $paid_statuses = fn_get_order_paid_statuses();
        if (in_array($order_status, $paid_statuses)) {
            $sign = '';
        }
    }

    return $sign;
}

/**
 * Gets Google Analytics tracking code
 *
 * @param mixed $company_id Company identifier to get code for
 * @return string Google Analytics tracking code
 */
function fn_google_tag_manager_get_tracking_code($company_id = null)
{
    if (!fn_allowed_for('ULTIMATE')) {
        $company_id = null;
    }

    return Settings::instance()->getValue('tracking_code', 'google_tag_manager', $company_id);
}

function fn_google_tag_manager_anaylitics_send($account, $order_info, $refuse = false)
{
    $url = 'http://www.google-analytics.com/collect';
    $sign = ($refuse == true) ? '-' : '';

    //Common data which should be sent with any request
    $required_data = array(
        'v' => '1',
        'tid' => $account,
        'cid' => md5($order_info['email']),
        'ti' => $order_info['order_id'],
        'cu' => $order_info['secondary_currency']
    );

    $transaction = array(
        't' => 'transaction',
        'tr' => $sign . $order_info['total'],
        'ts' => $sign . $order_info['shipping_cost'],
        'tt' => $sign . $order_info['tax_subtotal'],
        'ta' => fn_get_company_name($order_info['company_id'])
    );

    $result = Http::get($url, fn_array_merge($required_data, $transaction));
    foreach ($order_info['products'] as $item) {
        $item = array(
            't' => 'item',
            'in' => $item['product'],
            'ip' => fn_format_price($item['subtotal'] / $item['amount']),
            'iq' => $sign . $item['amount'],
            'ic' => $item['product_code'],
            'iv' => fn_google_tag_manager_get_main_category($item['product_id'], $order_info['lang_code']),
        );
        $result = Http::get($url, fn_array_merge($required_data, $item));
    }
}

function fn_google_tag_manager_anaylitics_refund($account, $order_info)
{
    $url = 'http://www.google-analytics.com/collect';

    //Common data which should be sent with any request
    $required_data = array(
        'v' => '1',
        'tid' => $account,
        'cid' => md5($order_info['email']),
        't' => 'event',
        'ec' => 'Ecommerce',
        'ea' => 'Refund',
        'ni' => true,
        'ti' => $order_info['order_id'],
        'pa' => 'refund',
        'dh' => Registry::get('config.http_host')
    );

    $result = Http::get($url, $required_data);
}

function fn_google_analytics_checkout($account, $step)
{
	$url = 'http://www.google-analytics.com/collect';
	$steps = array(
		'step_one' => '1',
		'step_two' => '2',
		'step_three' => '3',
		'step_four' => '4'
	);

	$dp = stristr(fn_url("checkout.checkout?edit_step=$step"), Registry::get('config.http_path'));

	$cart = $_SESSION['cart'];
	if ($step == 'step_one') {
		$col = 'sign in page';
	} elseif ($step == 'step_two') {
		$col = 'billing - shipping information';
	} elseif ($step == 'step_three') {
		$shid = $cart['chosen_shipping']['0'];
		$col = $cart['shipping'][$shid]['shipping'];
	} elseif ($step == 'step_four') {
		$col = fn_google_tag_manager_get_payment_name($cart['payment_id']);
	}
	
    $required_data = array(
        'v' => '1',
        'tid' => $account,
        'cid' => md5($cart['user_data']['email']),
        't' => 'pageview',
        'dh' => Registry::get('config.http_host'),
        'dp' => $dp
    );
    $result = Http::get($url, $required_data);
	$products = array();
	$products['pa'] = 'checkout';
	$i = 1;
    foreach ($cart['products'] as $p) {
		$prefix = 'pr'.$i;
        $products[$prefix.'id'] = $p['product_id'];
        $products[$prefix.'nm'] = html_entity_decode($p['product']);
        $products[$prefix.'ca'] = fn_google_tag_manager_get_main_category($p['product_id']);
        $products[$prefix.'br'] = fn_google_tag_manager_get_product_brand($p['product_id']);
        $products[$prefix.'pr'] = $p['price'];
        $products[$prefix.'qt'] = $p['amount'];
        ++$i;
    }
	$products['cos'] = $steps[$step];
	$products['col'] = $col;
	$products['z'] = TIME;
	$post_data = fn_array_merge($required_data, $products);
	$result = Http::get($url, $post_data);
}

/*function fn_alt_fwrite($data, $x, $offset='')
{
	if (is_array($data)) {
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				fn_alt_fwrite($v, $x, $offset."$k....");
			} else {
				fwrite($x, $offset.$k." => ".$v."\n");
			}
		}
	} else {
		fwrite($x, $offset.$data."\n");
	}
}

function fn_ga_log($data)
{
	$x = fopen(DIR_ROOT . "/ga.log", "a+");
	fn_alt_fwrite($data, $x);
	fclose($x);
}*/
/**************************************
 *               Hooks                *
 **************************************/

/**
 * Hook - change_order_status
 * 
 * @param string $status_to
 * @param string $status_from
 * @param array $order_info
 * @param array $force_notification
 * @param array $order_statuses
 * @param boolean $place_order
 * @return boolean
 */
function fn_google_tag_manager_change_order_status(&$status_to, &$status_from, &$order_info, $force_notification, $order_statuses, $place_order)
{
	if (AREA != 'A' && !empty($place_order)) {
		$_SESSION['init_checkout'] = 0;
	}
    
    if (Registry::get('addons.google_tag_manager.track_ecommerce') == 'N' || AREA != 'A') {
        return false;
    }

    $order_statuses = fn_get_statuses(STATUSES_ORDER, array(), true, true);
    if ($order_statuses[$status_to]['params']['ga_refund'] == 'Y') {
        
		fn_google_tag_manager_anaylitics_refund(fn_google_tag_manager_get_tracking_code($order_info['company_id']), $order_info);
        
	} else {
        
		if ($order_statuses[$status_to]['params']['inventory'] == 'D' && $order_statuses[$status_from]['params']['inventory'] == 'I') { // decrease amount
            
			fn_google_tag_manager_anaylitics_send(fn_google_tag_manager_get_tracking_code($order_info['company_id']), $order_info, false);
            
		} elseif ($order_statuses[$status_to]['params']['inventory'] == 'I' && $order_statuses[$status_from]['params']['inventory'] == 'D') { // increase amount
            
			fn_google_tag_manager_anaylitics_send(fn_google_tag_manager_get_tracking_code($order_info['company_id']), $order_info, true);
            
		}
        
	}
}

/**
 * Hook - get_order_items_info_post
 * 
 * @param array $order
 * @param array $v
 * @param string $k
 */
function fn_google_tag_manager_get_order_items_info_post(&$order, &$v, &$k)
{
    $order['products'][$k]['ga_category_name'] = fn_google_tag_manager_get_main_category($v['product_id'], $order['lang_code']);
}

/**
 * Hook - get_status_params_definition
 * 
 * @param array $status_params
 * @param string $type
 * @return boolean
 */
function fn_google_tag_manager_get_status_params_definition(&$status_params, $type)
{
	if ($type == STATUSES_ORDER) {
		$status_params['ga_refund'] = array (
			'type' => 'checkbox',
			'label' => 'google_analytics_refund',
			'default_value' => 'N'
		);
	}
    
	return true;
}
/**************************************
 *              /Hooks                *
 **************************************/