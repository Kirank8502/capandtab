<?php
namespace Opencart\Admin\Controller\Catalog;
class StoreOrder extends \Opencart\System\Engine\Controller {
    public function index(){
        $data = [];
		$url = '';
		$this->load->language('catalog/store_order');
		$this->document->setTitle('Order');
		
		$this->load->model('catalog/store_order');

		// $data['add'] = $this->url->link('catalog/store_order|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['delete'] = $this->url->link('catalog/store_order|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['send'] = $this->url->link('catalog/store_order|send', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();

		$data['save'] = $this->url->link('catalog/store_order|save', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/store_order', $data));
    }

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['import'] = $this->url->link('catalog/store_order|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('catalog/store_order|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['orders'] = array();
		$client = array();
		$moulder = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		$order_total = $this->model_catalog_store_order->getTotalOrders();
		
		$results = $this->model_catalog_store_order->getOrders($supplier_data);
		// $products = $this->model_catalog_store_order->getProducts();
		// $accessories = $this->model_catalog_store_order->getAccessories();
		$clients = $this->model_catalog_store_order->getClients();
		$moulders = $this->model_catalog_store_order->getMoulders();
		
		foreach($clients as $key => $value) {
			$client[$value['client_id']] = $value['name'];
		}

		foreach($moulders as $key_1 => $value_1) {
			$moulder[$value_1['moulder_id']] = $value_1['name'];
		}
		
		foreach ($results as $result) {
			$data['orders'][] = array(
				'orders_id'			=> $result['orders_id'],
				'name'       		=> ((!empty($result['client_id']) && $result['client_id'] > 0) ? $client[$result['client_id']] : ((!empty($result['moulder_id']) && $result['moulder_id'] > 0) ? $moulder[$result['moulder_id']] : 0)),
				'po_no'		 		=> $result['po_no'],
				'order_type'		=> $result['order_type'],
				'qty'				=> $result['qty'],
				'image'				=> $result['image'],
				'store_order_id'    => $result['store_order_id'],
				'edit'				=> $this->url->link('catalog/store_order|form', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $result['orders_id'] .'&store_order_id='. $result['store_order_id'] . $url, true),
			);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $order_total,
			'page'  => $page,
			'limit' => 10,
			'url'   => $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('catalog/store_order_list', $data);
	}

    public function getOrder(){
		$this->load->model('catalog/store_order');

        $order_info = $this->model_catalog_store_order->getOrder($this->request->get['po_no']);

		$moulder = $this->model_catalog_store_order->getMoulder($order_info['moulder_id']);
		$client = $this->model_catalog_store_order->getClient($order_info['client_id']);

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $order_info['image'];
		}

		$data = array(
			'name' => (isset($moulder['name']) ? $moulder['name'] : ''),
			'client_name' => (isset($client['name']) ? $client['name'] : ''),
			'orders_id' => isset($order_info['o_orders_id']) ? $order_info['o_orders_id'] : 0,
			'store_order_id' => isset($order_info['store_order_id']) ? $order_info['store_order_id'] : 0,
			'thumb' => isset($data['thumb']) ? $data['thumb'] : '',
			'image' => isset($order_info['image']) ? $order_info['image'] : '',
			'qty' => isset($order_info['qty_rev']) ? $order_info['qty_rev'] : 0,
		);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
    }

	public function save() {
		$this->load->language('catalog/store_order');
		$json = [];
		// $val = 0;
		$this->document->setTitle('Order');

		$this->load->model('catalog/store_order');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['store_order_id']){
				$val = $this->model_catalog_store_order->addOrderDetails($this->request->post);
			}else{
				$val = $this->model_catalog_store_order->editOrderDetails($this->request->post['store_order_id'], $this->request->post);
			}
			
			if($val){
				$this->session->data['success'] = 'Successfully Saved';
				$json['success'] = 'Successfully Saved';
				$json['redirect'] = $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'], true);
			}else{
				$this->session->data['error'] = 'Something went wrong';
				$json['error'] = 'Something went wrong';
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}


	public function form() {
		$this->load->model('catalog/store_order');
		$data['text_form'] = !isset($this->request->get['orders_id']) ? 'Add' : 'Edit';
		$data['edit_data'] = !isset($this->request->get['orders_id']) ? False : True;
		$this->document->setTitle('Order');
		// if (isset($this->error['warning'])) {
		// 	$data['error_warning'] = $this->error['warning'];
		// } else {
		// 	$data['error_warning'] = '';
		// }
		
		// if (isset($this->error['order'])) {
		// 	$data['error_order'] = $this->error['order'];
		// } else {
		// 	$data['error_order'] = array();
		// }

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => 'Order',
			'href' => $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['save'] = $this->url->link('catalog/store_order|save', 'user_token=' . $this->session->data['user_token'], true);

		$data['back'] = $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['products'] = $this->model_catalog_store_order->getProducts();
		$data['clients'] = $this->model_catalog_store_order->getClients();
		$data['moulders'] = $this->model_catalog_store_order->getMoulders();

		if (isset($this->request->get['orders_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$order_info = $this->model_catalog_store_order->getOrder($this->request->get['store_order_id'],$this->request->get['orders_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if(isset($this->request->get['orders_id'])){
			$data['orders_id'] = $this->request->get['orders_id'];
		}

		if(isset($this->request->get['store_order_id'])){
			$data['store_order_id'] = $this->request->get['store_order_id'];
		}

		if (isset($this->request->post['qty_rev'])) {
			$data['qty_rev'] = $this->request->post['qty_rev'];
	  	} elseif (!empty($order_info)) {
			$data['qty_rev'] = $order_info['qty_rev'];
	  	} else {	
			$data['qty_rev'] = 0;
	  	}

		// if (isset($this->request->post['no_qty'])) {
		// 	$data['no_qty'] = $this->request->post['no_qty'];
	  	// } elseif (!empty($order_info)) {
		// 	$data['no_qty'] = $order_info['no_qty'];
	  	// } else {	
		// 	$data['no_qty'] = 0;
	  	// }

		foreach($data['clients'] as $key => $value) {
			$client[$value['client_id']] = $value['name'];
		}

		foreach($data['moulders'] as $key_1 => $value_1) {
			$moulder[$value_1['moulder_id']] = $value_1['name'];
		}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $order_info['image'];
		}

		if (isset($this->request->post['moulder_id'])) {
			$data['moulder_name'] = $this->request->post['moulder_id'];
	  	} elseif (!empty($order_info)) {
			$data['moulder_name'] = isset($moulder[$order_info['moulder_id']]) ? $moulder[$order_info['moulder_id']] : '';
	  	} else {	
			$data['moulder_name'] = 0;
	  	}

		if (isset($this->request->post['client_id'])) {
			$data['client_name'] = $this->request->post['client_id'];
	  	} elseif (!empty($order_info)) {
			$data['client_name'] = isset($client[$order_info['client_id']]) ? $client[$order_info['client_id']] : '';
	  	} else {	
			$data['client_name'] = 0;
	  	}

		if (isset($this->request->post['order_type'])) {
			$data['order_type'] = $this->request->post['order_type'];
	  	} elseif (!empty($order_info)) {
			$data['order_type'] = $order_info['order_type'];
	  	} else {	
			$data['order_type'] = 0;
	  	}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
	  	} elseif (!empty($order_info)) {
			$data['image'] = $order_info['image'];
	  	} else {	
			$data['image'] = '';
	  	}

		// if (isset($this->request->post['targeted_date'])) {
		// 	$data['targeted_date'] = $this->request->post['targeted_date'];
	  	// } elseif (!empty($order_info)) {
		// 	$data['targeted_date'] = $order_info['targeted_date'];
	  	// } else {	
		// 	$data['targeted_date'] = '';
	  	// }

		if (isset($this->request->post['order_po_no'])) {
			$data['po_no'] = $this->request->post['order_po_no'];
	  	} elseif (!empty($order_info)) {
			$data['po_no'] = $order_info['order_po_no'];
	  	} else {	
			$data['po_no'] = '';
	  	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/store_order_form', $data));
	}

	public function send(){
		$data = [];
		$this->load->model('catalog/store_order');
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $orders_id) {
				$order_data = $this->model_catalog_store_order->getOrderDetail($orders_id);
				if($order_data['order_type'] == '0'){
					$email = $this->model_catalog_store_order->getMoulder($order_data['moulder_id']);
				}else{
					$email = $this->model_catalog_store_order->getClient($order_data['client_id']);
				}

				if ($this->config->get('config_mail_engine') && !empty($email['email'])) {
					$mail_option = [
						'parameter'     => $this->config->get('config_mail_parameter'),
						'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
						'smtp_username' => $this->config->get('config_mail_smtp_username'),
						'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
						'smtp_port'     => $this->config->get('config_mail_smtp_port'),
						'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
					];
					$data['name'] = !empty($email['name']) ? $email['name'] : "Sir/Ma'am";
					$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
					$mail->setTo($email['email']);
					$mail->setFrom($this->config->get('config_mail_smtp_username'));
					$mail->setSender('CAPANDTAB');
					$mail->setSubject('Testing');
					$mail->setHtml($this->load->view('catalog/lr_copy_mail', $data));
					// if (!empty($order_data)) {
					// $mail->addAttachment(DIR_IMAGE.'/catalog/demo/apple_logo.jpg');
					// }
					$mail->send();
				}
			}

			$this->session->data['success'] = 'Mail Sent Successfully';

			$json['success'] = 'Mail Sent Successfully';
			$json['redirect'] = $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

}