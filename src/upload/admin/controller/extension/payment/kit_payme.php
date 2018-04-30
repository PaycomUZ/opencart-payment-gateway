<?php
class ControllerExtensionPaymentKiTPayme extends Controller {
	private $error = array();

	public function index() {
		
		$data['payment_kit_payme_mob_payme'] = str_replace('admin/', '', HTTPS_SERVER)."?payme=pay";
		$data['payment_kit_payme_checkout_url'] = "https://checkout.paycom.uz";
		$data['payment_kit_payme_checkout_url_test'] = "https://test.paycom.uz";
		$data['payment_kit_payme_callback_pay_time'] = 0;
		$data['payment_kit_payme_enabled'] = 'Y';
		
		$this->load->language('extension/payment/kit_payme');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_kit_payme', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->save($data);
			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant_id'])) {
			$data['error_kit_payme_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_kit_payme_merchant_id'] = '';
		}

		if (isset($this->error['merchant_private_key'])) {
			$data['error_merchant_private_key'] = $this->error['merchant_private_key'];
		} else {
			$data['error_merchant_private_key'] = '';
		}
		
		if (isset($this->error['merchant_private_key_test'])) {
			$data['error_merchant_private_key_test'] = $this->error['merchant_private_key_test'];
		} else {
			$data['error_merchant_private_key_test'] = '';
		}
		if (isset($this->error['kit_payme_checkout_url'])) {
			$data['error_kit_payme_checkout_url'] = $this->error['kit_payme_checkout_url'];
		} else {
			$data['error_kit_payme_checkout_url'] = '';
		}
		if (isset($this->error['kit_payme_checkout_url_test'])) {
			$data['error_kit_payme_checkout_url_test'] = $this->error['kit_payme_checkout_url_test'];
		} else {
			$data['error_kit_payme_checkout_url_test'] = '';
		}
		if (isset($this->error['kit_payme_mob_payme'])) {
			$data['kit_payme_mob_payme'] = $this->error['kit_payme_mob_payme'];
		} else {
			$data['kit_payme_mob_payme'] = '';
		}		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/kit_payme', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/kit_payme', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_kit_payme_merchant_id'])) {
			$data['payment_kit_payme_merchant_id'] = $this->request->post['payment_kit_payme_merchant_id'];
		} else {
			$data['payment_kit_payme_merchant_id'] = $this->config->get('payment_kit_payme_merchant_id');
		}

		if (isset($this->request->post['payment_kit_payme_merchant_private_key'])) {
			$data['payment_kit_payme_merchant_private_key'] = $this->request->post['payment_kit_payme_merchant_private_key'];
		} else {
			$data['payment_kit_payme_merchant_private_key'] = $this->config->get('payment_kit_payme_merchant_private_key');
		}


		if (isset($this->request->post['payment_kit_payme_merchant_private_key_test'])) {
			$data['payment_kit_payme_merchant_private_key_test'] = $this->request->post['payment_kit_payme_merchant_private_key_test'];
		} else {
			$data['payment_kit_payme_merchant_private_key_test'] = $this->config->get('payment_kit_payme_merchant_private_key_test');
		}		
	
		if (isset($this->request->post['payment_kit_payme_checkout_url'])) {
			$data['payment_kit_payme_checkout_url'] = $this->request->post['payment_kit_payme_checkout_url'];
		} else if(empty($this->config->get('payment_kit_payme_checkout_url'))) {
			$data['payment_kit_payme_checkout_url'] = $this->config->get('payment_kit_payme_checkout_url');
		}
		
		if (isset($this->request->post['payment_kit_payme_checkout_url_test'])) {
			$data['payment_kit_payme_checkout_url_test'] = $this->request->post['payment_kit_payme_checkout_url_test'];
		} else {
			$data['payment_kit_payme_checkout_url_test'] = $this->config->get('payment_kit_payme_checkout_url_test');
		}

		if (isset($this->request->post['payment_kit_payme_enabled'])) {
			$data['payment_kit_payme_enabled'] = $this->request->post['payment_kit_payme_enabled'];
		} else {
			$data['payment_kit_payme_enabled'] = $this->config->get('payment_kit_payme_enabled');
		}
		
		if (isset($this->request->post['payment_kit_payme_mob_payme'])) {
			$data['payment_kit_payme_mob_payme'] = $this->request->post['payment_kit_payme_mob_payme'];
		} else {
			$data['payment_kit_payme_mob_payme'] = $this->config->get('payment_kit_payme_mob_payme');
		}
		
		$data['kit_payme_callback_pay_time'] = array(
				array('value'=>0,	'name'=>'Моментально'),
				array('value'=>15000,	'name'=>'15 секунд'),
				array('value'=>30000,	'name'=>'30 секунд'),
				array('value'=>60000,	'name'=>'60 секунд')
				);

		if (isset($this->request->post['payment_kit_payme_callback_pay_time'])) {
			$data['payment_kit_payme_callback_pay_time'] = $this->request->post['payment_kit_payme_callback_pay_time'];
		} else {
			$data['payment_kit_payme_callback_pay_time'] = $this->config->get('payment_kit_payme_callback_pay_time');
		}

		if (isset($this->request->post['payment_kit_payme_status'])) {
			$data['payment_kit_payme_status'] = $this->request->post['payment_kit_payme_status'];
		} else {
			$data['payment_kit_payme_status'] = $this->config->get('payment_kit_payme_status');
		}
			
		
		if (isset($this->request->post['payment_kit_payme_status_tovar'])) {
			$data['payment_kit_payme_status_tovar'] = $this->request->post['payment_kit_payme_status_tovar'];
		} else {
			$data['payment_kit_payme_status_tovar'] = $this->config->get('payment_kit_payme_status_tovar');
		}
				
		if(empty($data['payment_kit_payme_mob_payme'] ))
			$data['payment_kit_payme_mob_payme'] = str_replace('admin/', '', HTTPS_SERVER)."?payme=pay";
		if(empty($data['payment_kit_payme_checkout_url'] ))
			$data['payment_kit_payme_checkout_url'] = "https://checkout.paycom.uz";
		if(empty($data['payment_kit_payme_checkout_url_test'] ))
			$data['payment_kit_payme_checkout_url_test'] = "https://test.paycom.uz";
		if(empty($data['payment_kit_payme_callback_pay_time'] ))
			$data['payment_kit_payme_callback_pay_time'] = 0;
		if(empty($data['payment_kit_payme_enabled'] ))
			$data['payment_kit_payme_enabled'] = 'Y';
				
		$data['payment_kit_payme_callbak_url'] = str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme&f=Callback";
		
		$data['order_return'] = str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme&f=OrderReturn";
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/kit_payme', $data));
	}
	private function save($data){
		$Dir_App = str_replace('admin/', 'catalog/', DIR_APPLICATION).'model/extension/payment/kit_payme/';
			
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
		if(isset($this->request->post['payment_kit_payme_status'])) {
			
			$Get['merchant_id'] 		= $this->request->post['payment_kit_payme_merchant_id'];
			$Get['merchant_key_test']	= $this->request->post['payment_kit_payme_merchant_private_key_test'];
			$Get['merchant_key']		= $this->request->post['payment_kit_payme_merchant_private_key'];
			$Get['checkout_url']		= $this->request->post['payment_kit_payme_checkout_url'];
			$Get['endpoint_url']		= str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme&f=Callback";
			$Get['status_test']			= $this->request->post['payment_kit_payme_enabled'];
			$Get['status_tovar']		= $this->request->post['payment_kit_payme_status_tovar'];
			$Get['callback_pay']		= $this->request->post['payment_kit_payme_callback_pay_time'];
			$Get['redirect']			= str_replace('admin/', '', HTTPS_SERVER)."?route=extension/payment/kit_payme&f=OrderReturn";
			$Return = include_once $Dir_App.'IndexConfigCreate.php';
		}	
		
	}
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/kit_payme')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_kit_payme_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_kit_payme_merchant_id');
		}

		if (!$this->request->post['payment_kit_payme_merchant_private_key']) {
			$this->error['merchant_private_key'] = $this->language->get('error_merchant_private_key');
		}

		if (!$this->request->post['payment_kit_payme_merchant_private_key_test']) {
			$this->error['merchant_private_key_test'] = $this->language->get('error_merchant_private_key_test');
		}

		if (!$this->request->post['payment_kit_payme_checkout_url_test']) {
			$this->error['kit_payme_checkout_url_test'] = $this->language->get('error_kit_payme_checkout_url_test');
		}
		if (!$this->request->post['payment_kit_payme_checkout_url']) {
			$this->error['kit_payme_checkout_url'] = $this->language->get('error_kit_payme_checkout_url');
		}
		if (!$this->request->post['payment_kit_payme_mob_payme']) {
			$this->error['kit_payme_mob_payme'] = $this->language->get('kit_payme_mob_payme');
		}
		return !$this->error;
	}
}