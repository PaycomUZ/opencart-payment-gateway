<?php 
//if(is_file(__DIR_.'/Error.php'))
if(!defined('TABLE_PREFIX')) define('TABLE_PREFIX', '');

include_once __DIR__.'/Core/Error.php';
include_once __DIR__.'/Core/MySql.php';
include_once __DIR__.'/Core/MySqli.php';
include_once __DIR__.'/Core/Security.php';
include_once __DIR__.'/Core/Format.php';
include_once __DIR__.'/Core/Payme.php';
include_once __DIR__.'/Core/PaymeCallback.php';

class ExePaymeCallback {

	static function Construct($db_group){

		define('LANG', 'ru');
		date_default_timezone_set('Asia/Tashkent');

		$check=false;

		if(isset($_SERVER['PHP_AUTH_USER'])) {

			if(isset($_SERVER['PHP_AUTH_PW'])){

				define('PHP_AUTH_USER', $_SERVER['PHP_AUTH_USER']);
				define('PHP_AUTH_PW', $_SERVER['PHP_AUTH_PW']);

			} else {

				$a = html_entity_decode(base64_decode( substr($_SERVER["PHP_AUTH_USER"],6)));
				list($name, $password) = explode(':', $a);

				define('PHP_AUTH_USER', $name);
				define('PHP_AUTH_PW',   $password);
			}

			$check=true;

		} else if(isset($_SERVER['REMOTE_USER'])) {

		    $a = html_entity_decode(base64_decode( substr($_SERVER["REMOTE_USER"],6)));
			list($name, $password) = explode(':', $a);

			define('PHP_AUTH_USER', $name);
			define('PHP_AUTH_PW',   $password);

            $check=true;
		}		
		
		if($check) {
			
			$PaymeCallback = new PaymeCallback($db_group);
			
			$Sql['PerformTransaction'][] = array(
					'Sql' => "
					 INSERT INTO {TABLE_PREFIX}order_history(order_history_id, order_id, order_status_id, notify, comment, date_added) 
					 SELECT NULL order_history_id, 
					        t.order_id order_id, 
					        :p_state order_status_id, 
					        0 notify, 
					        '' `comment`, 
					        NOW() date_added
					   FROM {TABLE_PREFIX}payme_transactions t
					  WHERE t.transaction_id = :p_transaction_id",
					'Param'=>array(
							':p_state' => '5'
					)
			);
			
			$Sql['PerformTransaction'][] = array(
					'Sql' => 'UPDATE {TABLE_PREFIX}order
					             Set order_status_id=:p_state
							   Where order_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => '5'
					)
			);
			
			
			$Sql['CancelTransaction'][] = array(
					'Sql' => "
					 INSERT INTO {TABLE_PREFIX}order_history(order_history_id, order_id, order_status_id, notify, comment, date_added) 
					 SELECT NULL order_history_id, 
					        t.order_id order_id, 
					        :p_state order_status_id, 
					        0 notify, 
					        '' `comment`, 
					        NOW() date_added
					   FROM {TABLE_PREFIX}payme_transactions t
					  WHERE t.transaction_id = :p_transaction_id",
					'Param'=>array(
							':p_state' => '8'
					)
			);
			$Sql['CancelTransaction'][] = array(
					'Sql' => 'UPDATE {TABLE_PREFIX}order
					             Set order_status_id=:p_state
							   Where order_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => '8'
					)
			);
			
			$rezult = $PaymeCallback->Execute(null);
			return array('return'=>$rezult, 'status'=>true);
			//exit(json_encode($rezult));
			
		} else {
		
			if(!ini_get('register_globals'))
			{
				if(function_exists('apache_response_headers')){
					$headers = apache_request_headers();
					$headers['Authorization'] = null;
				}
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>"Скрипты требует включения параметр register_globals данной директивы. Для этого в папке скрипта или в папке домена создайте файл .htaccess и поместите в него следующую директиву: php_flag register_globals on Если файл .htaccess в нужной папке уже существует, то просто добавьте эту строку в конец <IfModule mod_rewrite.c>RewriteEngine onRewriteRule .* - [E=PHP_AUTH_USER:%{HTTP:Authorization},L]</IfModule>. Действие этой директивы распространяется и на все подпапки.",
						"data" => array(function_exists('apache_response_headers')?$headers:headers_list()),
						"time"=>Format::timestamp(true)
				);
				return array('return'=>$Param, 'status'=>false);
			}
			$Security = new Security();
			$Get = $Security->_json(true);
			if(isset($Get['id']))
			{
				$Param['id'] = $Get['id'];
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>array("ru"=>'Недостаточно привилегий для выполнения метода.',"uz"=>'Недостаточно привилегий для выполнения метода.',"en"=>'Недостаточно привилегий для выполнения метода.'),
						"data" => __METHOD__,
						"time"=>Format::timestamp(true)
				);
			}
			else
			{
				$Param['id'] = $Get['id'];
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>array("ru"=>'Недостаточно привилегий для выполнения метода.',"uz"=>'Недостаточно привилегий для выполнения метода.',"en"=>'Недостаточно привилегий для выполнения метода.'),
						"data" => __METHOD__,
						"time"=>Format::timestamp(true)
				);				
			}
			return array('return'=>$Param, 'status'=>false);
			//exit(json_encode($Param));
		}		
	}
}
//print_r($db_group);
if(isset($db_group))
	return ExePaymeCallback::Construct($db_group);
?>