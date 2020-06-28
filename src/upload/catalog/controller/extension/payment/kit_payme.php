<?php
class ControllerExtensionPaymentKiTPayme extends Controller {
	
	public function index() {
		
		if(isset($this->request->get['route']) and $this->request->get['route']=='checkout/confirm') {
			
			return $this->CheckoutConfirm(); // for Checkout
		
		} elseif(isset($this->request->get['route']) and $this->request->get['route']=='extension/payment/kit_payme' And isset($this->request->get['code']) and $this->request->get['code']=='kit_payme_pay'){
			
			$this->PayKit_Payme();
			
		} elseif(isset($this->request->get['f']) and $this->request->get['f']=='Callback')
		
			$this->Callback();
			
		elseif(isset($this->request->get['f']) and $this->request->get['f']=='OrderReturn') 
		
			$this->OrderReturn($this->request->get['order_id']);
			
		else {
			
			return $this->CheckoutConfirm();
		}
		//exit( $this->config->get('payment_kit_payme_merchant_id').'');
	}

	public function CheckoutConfirm() {
		
		$data['button_confirm'] = $this->language->get('button_confirm');
	
		$this->load->model('checkout/order');
	
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']); 
	
		//$out_trade_no = trim($order_info['order_id']);
		$out_trade_no =$this->session->data['order_id'] ;
		
		$subject = trim($this->config->get('config_name'));
		
		// Fixed by Me
		$redirect = str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme&f=OrderReturn";
		//$redirect = str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme_cart&f=OrderReturn";
        //$redirect = str_replace('http://','', $redirect);
		
		$config = array (
				'merchant_id' 			=> $this->config->get('payment_kit_payme_merchant_id'),
				'order_id'				=> $out_trade_no,
				'total'					=> round($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false)*100), /*(int) $order_info['total']*100,*/
				'Redirect'				=> $redirect, /*str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme_cart&f=OrderReturn",*/
				//'merchant_key'   		=> $this->config->get('payment_kit_payme_enabled')=='Y'?$this->config->get('payment_kit_payme_merchant_private_key_test'):$this->config->get('payment_kit_payme_merchant_private_key'),
				'gateway_url'           => $this->config->get('payment_kit_payme_enabled')=='Y'?$this->config->get('payment_kit_payme_checkout_url_test'):$this->config->get('payment_kit_payme_checkout_url'),
				'pay_time'				=> $this->config->get('payment_kit_payme_callback_pay_time'),
				'status_tovar'			=> $this->config->get('payment_kit_payme_status_tovar')
		);
		
		//$total_amount = trim($this->currency->format($order_info['total'], 'CNY', '', false));
		$body = '';//trim($_POST['WIDbody']);
	
		$payRequestBuilder = array(
				'body'         => $body,
				'subject'      => $subject,
				'total_amount' => '0',
				'out_trade_no' => $out_trade_no,
				'product_code' => 'FAST_INSTANT_TRADE_PAY'
		);
	
		$this->load->model('extension/payment/kit_payme');
	
		$response = $this->model_extension_payment_kit_payme->pagePay($payRequestBuilder,$config);
		$data['action'] = $this->model_extension_payment_kit_payme->getPostCharset($config);
		$data['form_params'] = $response;
		return $this->load->view('extension/payment/kit_payme', $data);
	}
	
	private function PayKit_Payme(){
		$Get = array(
				'Amount'=>$this->request->get['total'],
				'OrderId'=>$this->request->get['order_id'],
				'CmsOrderId'=>$this->request->get['order_id'],
				'IsFlagTest'=>$this->config->get('payment_kit_payme_enabled')
		);

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "payme_transactions WHERE order_id = '" . (int)$this->request->get['order_id'] . "'");
		/*if(count($query->rows)>=1){
			$Url = "{$this->request->get['Redirect']}&order_id={$query->row['order_id']}&f=OrderReturn";
			header('Location: '.$Url);
			exit($Url);
		}*/
			
		
		$Dir_App = DIR_APPLICATION.'model/extension/payment/kit_payme/';
		
		define('TABLE_PREFIX', DB_PREFIX);
		define('DB_CHARSET', 'utf8mb4');
		$db_group = array(
				'DB_HOST'=>DB_HOSTNAME,
				'DB_PORT'=>DB_PORT,
				'DB_NAME'=>DB_DATABASE,
				'DB_USER'=>DB_USERNAME,
				'DB_PASS'=>DB_PASSWORD,
				'CHARSET'=>DB_CHARSET,
				'CHARSETCOLAT'=>DB_CHARSET
		);
		$return = include $Dir_App.'IndexInsertOrder.php';
		
		//$Url = "{$config['gateway_url']}&m={$config['merchant_id']}&ac.order_id={$config['order_id']}&a={$config['total']}&l=ru&c={$config['Redirect']}&order_id={$config['order_id']}&ct={$config['pay_time']}";		
		
		//$Url = "{$_GET['merchantUrl']}/".base64_encode("m={$_GET['merchantId']};ac.order_id={$return['id']};a={$_GET['Amount']};l=ru;c={$_GET['Redirect']}?order_id={$return['id']};ct={$_GET['callback_timeout']}");
		/*$gateway_url = $this->config->get('payment_kit_payme_enabled')=='Y'?$this->config->get('payment_kit_payme_checkout_url_test'):$this->config->get('payment_kit_payme_checkout_url');
		
		$Url = "{$gateway_url}/".base64_encode("m={$this->config->get('payment_kit_payme_merchant_id')};ac.order_id={$return['id']};a={$this->request->get['total']};l=ru;ct={$this->request->get['pay_time']};c={$this->request->get['Redirect']}&order_id={$return['id']}&f=OrderReturn");
						
		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory($this->request->get['order_id'], 1 );
		
		header('Location: '.$Url);
		exit();*/
		
		$currency_code = $this->db->query("SELECT t.currency_code FROM `" . DB_PREFIX . "order` t WHERE order_id = '" . (int)$this->request->get['order_id'] . "'");
		$Url = $this->config->get('payment_kit_payme_enabled')=='Y'?$this->config->get('payment_kit_payme_checkout_url_test'):$this->config->get('payment_kit_payme_checkout_url');
		
		$currency = $currency_code->row['currency_code'];
		
		     if($currency == 'UZS') $currency = 860;
		else if($currency == 'USD') $currency = 840;
		else if($currency == 'RUB') $currency = 643;
		else if($currency == 'EUR') $currency = 978;
		else                        $currency = 860;
		
		$fields = array(
				'merchant'       	=> $this->config->get('payment_kit_payme_merchant_id'),  // Идентификатор WEB Кассы
				'amount'            => $this->request->get['total'],						 // Сумма платежа в тиинах
				'account[order_id]' => $this->request->get['order_id'],						 // Поля Объекта Account
				// НЕ ОБЯЗАТЕЛЬНЫЕ ПОЛЯ
				'lang'				=> 'ru',												 //Язык. Доступные значения: ru|uz|en Другие значения игнорируются Значение по умолчанию ru
				'currency'			=> $currency, 											 // Валюта. Доступные значения: 643|840|860|978 Другие значения игнорируются Значение по умолчанию 860 Коды валют в ISO формате
				'callback'			=> $this->request->get['Redirect']."&order_id={$this->request->get['order_id']}&f=OrderReturn",  // URL возврата после оплаты или отмены платежа. :transaction - id транзакции или "null" если транзакцию не удалось создадь :account.{field} - поля объекта Account
				'callback_timeout'	=> $this->config->get('payment_kit_payme_callback_pay_time')					// Таймаут после успешного платежа в милисекундах.
		);
		
		if($this->config->get('payment_kit_payme_status_tovar')=='Y')
			$fields['detail'] = base64_encode(json_encode(array(
					'order_id'=>$this->request->get['order_id'],
					'summa'=>$this->request->get['total']
			)));	// Объект детализации платежа
		$form = '';
		foreach ($fields as $key=>$value){
			$form .=  '<input type="hidden" name="'.$key.'" value="'.$value.'">';
		}
		
		
		// print_r($return); exit($Url);
		$form =
<<<FORM
	<form action="{$Url}" method="POST" id="paymentform">
		{$form}
	</form>
	<script type="text/javascript">
    	document.getElementById("paymentform").submit();
    </script>
FORM;
	
		exit($form);		
	}
	
	private function Callback(){
		
		$Dir_App = DIR_APPLICATION.'model/extension/payment/kit_payme/';
		
		define('TABLE_PREFIX', DB_PREFIX);
		define('DB_CHARSET', 'utf8mb4');
		$db_group = array(
				'DB_HOST'=>DB_HOSTNAME,
				'DB_PORT'=>DB_PORT,
				'DB_NAME'=>DB_DATABASE,
				'DB_USER'=>DB_USERNAME,
				'DB_PASS'=>DB_PASSWORD,
				'CHARSET'=>DB_CHARSET,
				'CHARSETCOLAT'=>DB_CHARSET
		);
		$IndexCallback = include_once $Dir_App.'IndexCallback.php';
		if(isset($IndexCallback['return']['result'])){
			if(isset($IndexCallback['return']['result']['transaction']) and $IndexCallback['status'] and $IndexCallback['return']['result']['state'] == '2'){
				$json = array();
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory($IndexCallback['return']['result']['transaction'],5/* $this->config->get('payment_cod_order_status_id')*/);
				$json['redirect'] = $this->url->link('checkout/success');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
		else
		{
			exit(json_encode($IndexCallback['return']));
		}
		//print_r($IndexCallback['return']);exit($this->config->get('payment_cod_order_status_id').' ');
		exit(json_encode($IndexCallback['return']));
	}
	private function OrderReturn(){
		$Dir_App = DIR_APPLICATION.'model/extension/payment/kit_payme/';
		
		$StatusTest =  $this->config->get('payment_kit_payme_enabled');
		if($StatusTest == 'Y'){
			$merchantKey 		= $this->config->get('payment_kit_payme_merchant_private_key');
			$merchantUrl 	= $this->config->get('payment_kit_payme_checkout_url_test');
		}
		else{
			$merchantKey 		= $this->config->get('payment_kit_payme_merchant_private_key_test');
			$merchantUrl = $this->config->get('payment_kit_payme_checkout_url');
		}
		
		$merchantId 		= $this->config->get('payment_kit_payme_merchant_id');
		$checkoutUrl 		= $this->config->get('payment_kit_payme_checkout_url');
		$callback_timeout 	= $this->config->get('payment_kit_payme_callback_pay_time');
		$Redirect		  	= '';
		
		define('TABLE_PREFIX', DB_PREFIX);
		define('DB_CHARSET', 'utf8mb4');
		$db_group = array(
				'DB_HOST'=>DB_HOSTNAME,
				'DB_PORT'=>DB_PORT,
				'DB_NAME'=>DB_DATABASE,
				'DB_USER'=>DB_USERNAME,
				'DB_PASS'=>DB_PASSWORD,
				'CHARSET'=>DB_CHARSET,
				'CHARSETCOLAT'=>DB_CHARSET
		);
		
		$paymeform = include $Dir_App.'IndexOrderReturn.php';
		exit($paymeform);
	}
}
