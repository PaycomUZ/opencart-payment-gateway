<?php
class ModelExtensionPaymentKiTPayme extends Model {

	public function getMethod($address, $total) {
		$this->load->language('extension/payment/kit_payme');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_alipay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_alipay_total') > 0 && $this->config->get('payment_alipay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_alipay_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'kit_payme',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_alipay_sort_order')
			);
		}
		return $method_data;
	}
	function getPostCharset($config){
		//$Url = "{$config['gateway_url']}/".base64_encode("m={$config['merchant_id']};ac.order_id={$config['order_id']};a={$config['total']};l=ru;c={$config['Redirect']}&order_id={$config['order_id']};ct={$config['pay_time']}");
		$config['pay_time'] = empty($config['pay_time'])?0:$config['pay_time'];
		$Url = "index.php?code=kit_payme_pay&route=extension/payment/kit_payme&gateway_url={$config['gateway_url']}&merchant_id={$config['merchant_id']}&order_id={$config['order_id']}&total={$config['total']}&pay_time={$config['pay_time']}&Redirect={$config['Redirect']}";		
		return $Url;
	}
	function pagePay($builder,$config) {
		$response = array('key'=>'name');
		return $response;
	}

}
