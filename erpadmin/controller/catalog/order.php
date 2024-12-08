<?php
namespace Opencart\Admin\Controller\Catalog;
use Dompdf\Dompdf;
use Dompdf\Options;
class Order extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('catalog/order');
		$this->document->setTitle('Order');
		
		$this->load->model('catalog/order');

		$data['add'] = $this->url->link('catalog/order|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/order|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['send'] = $this->url->link('catalog/order|send', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/order', $data));
	}

	public function save() {
		$this->load->language('catalog/order');
		$json = [];
		$val = 0;
		$this->document->setTitle('Order');

		$this->load->model('catalog/order');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['orders_id']){
				$val = $this->model_catalog_order->addOrder($this->request->post);
			}else{
				$val = $this->model_catalog_order->editOrder($this->request->post['orders_id'], $this->request->post);
			}

			if($val){
				$json['success'] = 'Successfully Saved';
				$json['redirect'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'], true);
			}else{
				$json['error'] = 'Quantity Issue';
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('catalog/order');

		$this->document->setTitle('Delete Order');

		$this->load->model('catalog/order');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $orders_id) {
				$this->model_catalog_order->deleteOrder($orders_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			// $this->response->redirect($this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}
	
	public function import() {
		$this->language->load('catalog/order');

    	$this->document->setTitle('Import Order');
		
		$this->load->model('catalog/order');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			
			$this->model_catalog_order->importSupplier($this->request->files);

			$this->session->data['success'] = $this->language->get('text_success');
			
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
			
			$this->response->redirect($this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

 		if (isset($this->error['import_file'])) {
			$this->error['warning'] = $this->error['import_file'];
		} 

    	$this->getList();
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
			'href' => $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['import'] = $this->url->link('catalog/order|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('catalog/order|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['orders'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		$order_total = $this->model_catalog_order->getTotalOrders();
		
		$results = $this->model_catalog_order->getOrders($supplier_data);
		$products = $this->model_catalog_order->getProducts();
		$accessories = $this->model_catalog_order->getAccessories();
		$fittings = $this->model_catalog_order->getFittings();

		$clients = $this->model_catalog_order->getClients();
		$moulders = $this->model_catalog_order->getMoulders();

		foreach($products as $key => $value) {
			$product[$value['product_id']] = $value['name'];
		}

		foreach($accessories as $key_1 => $value_1) {
			$accessory[$value_1['accessories_id']] = $value_1['name'];
		}

		foreach($fittings as $key_2 => $value_2) {
			$fitts[$value_2['fittings_id']] = $value_2['name'];
		}

		foreach($moulders as $key_3 => $value_3) {
			$mol[$value_3['moulder_id']] = $value_3['name'];
		}
		foreach($clients as $key_4 => $value_4) {
			$cli[$value_4['client_id']] = $value_4['name'];
		}
		
		foreach ($results as $result) {
			$date = strtotime($result['targeted_date']);
			$po_date = strtotime($result['date_added']);
			$data['orders'][] = array(
				'orders_id'			=> $result['orders_id'],
				'po_no'				=> $result['po_no'],
				'targeted_date'		=> date("d-m-Y", $date),
				'product_name'      => ((!empty($result['product_id']) && $result['product_id'] > 0) ? $product[$result['product_id']] : ((!empty($result['acc_fitts_id']) && $result['acc_fitts_id'] > 0 && str_starts_with($result['acc_fitts_id'],'acc_')) ? $accessory[str_replace("acc_","",$result['acc_fitts_id'])] : ((!empty($result['acc_fitts_id']) && $result['acc_fitts_id'] > 0 && str_starts_with($result['acc_fitts_id'],'fitts_')) ? $fitts[str_replace("fitts_","",$result['acc_fitts_id'])] : ((!empty($result['fittings_id']) && $result['fittings_id'] > 0) ? $fitts[$result['fittings_id']] : 0) ))),
				'vendor_name'		=> ($result['order_type'] == 1 ? $cli[$result['client_id']] : $mol[$result['moulder_id']] ),
				'qty'				=> $result['qty'],
				'order_type'		=> $result['order_type'],
				'date_added'		=> date("d-m-Y", $po_date),
				'selected'			=> isset($this->request->post['selected']) && in_array($result['orders_id'], $this->request->post['selected']),				
				'view'				=> $this->url->link('catalog/order|form', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $result['orders_id'] .'&view_mode=1'. $url, true),
				'edit'				=> $this->url->link('catalog/order|form', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $result['orders_id'] . $url, true),
				'export'            => $this->url->link('catalog/order|exportData', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $result['orders_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		
		$data['sort_name'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		
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
			'url'   => $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('catalog/order_list', $data);
	}

	public function form() {
		$this->load->model('catalog/order');
		$data['text_form'] = !isset($this->request->get['orders_id']) ? 'Add' : 'Edit';
		$data['edit_data'] = !isset($this->request->get['orders_id']) ? False : True;
		$data['view_mode'] = !isset($this->request->get['view_mode']) ? False : True;
		$this->document->setTitle('Order');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['order'])) {
			$data['error_order'] = $this->error['order'];
		} else {
			$data['error_order'] = array();
		}

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
			'href' => $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('catalog/order|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$latest = $this->model_catalog_order->getOrderId();
		$latest_id = explode('-',$latest['latest_order_id']);
		
		$data['latest_id'] = !isset($this->request->get['orders_id']) ? str_pad($latest_id[2] + 1, 3, '0', STR_PAD_LEFT) : 000;
		$data['products'] = $this->model_catalog_order->getProducts();
		$data['clients'] = $this->model_catalog_order->getClients();
		$data['powders'] = $this->model_catalog_order->getPowders();
		$data['colours'] = $this->model_catalog_order->getColours();
		$data['moulders'] = $this->model_catalog_order->getMoulders();
		$data['master_batchs'] = $this->model_catalog_order->getMasterBatchs();
		$data['pigments'] = $this->model_catalog_order->getPigments();
		$data['dies'] = $this->model_catalog_order->getDies();
		$data['accessories'] = $this->model_catalog_order->getAccessories();
		$data['fittings'] = $this->model_catalog_order->getFittings();
		$data['datas'] = array_merge($data['accessories'],$data['fittings']);
		foreach ($data['datas'] as &$item) {
			if(isset($item['accessories_id'])){
				$item['acc_fitts_id'] = 'acc_' . $item['accessories_id'];
			}elseif(isset($item['fittings_id'])){
				$item['acc_fitts_id'] = 'fitts_' . $item['fittings_id'];
			}
		}

		if (isset($this->request->get['orders_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$order_info = $this->model_catalog_order->getOrder($this->request->get['orders_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if(isset($this->request->get['orders_id'])){
			$data['orders_id'] = $this->request->get['orders_id'];
		}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
	  	} elseif (!empty($order_info)) {
			$data['qty'] = $order_info['qty'];
	  	} else {	
			$data['qty'] = 0;
	  	}

		if (isset($this->request->post['no_qty'])) {
			$data['no_qty'] = $this->request->post['no_qty'];
	  	} elseif (!empty($order_info)) {
			$data['no_qty'] = $order_info['no_qty'];
	  	} else {	
			$data['no_qty'] = 0;
	  	}

		if (isset($this->request->post['acc_fitts_id'])) {
			$data['acc_fitts_id'] = $this->request->post['acc_fitts_id'];
	  	} elseif (!empty($order_info)) {
			$data['acc_fitts_id'] = $order_info['acc_fitts_id'];
	  	} else {	
			$data['acc_fitts_id'] = '';
	  	}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
	  	} elseif (!empty($order_info)) {
			$data['weight'] = $order_info['total_weight'];
	  	} else {	
			$data['weight'] = 0;
	  	}

		if (isset($this->request->post['bags'])) {
			$data['bags'] = $this->request->post['bags'];
	  	} elseif (!empty($order_info)) {
			$data['bags'] = $order_info['bags'];
	  	} else {
			$data['bags'] = 0;
	  	}

		if (isset($this->request->post['check_color'])) {
			$data['check_color'] = $this->request->post['check_color'];
	  	} elseif (!empty($order_info)) {
			$data['check_color'] = $order_info['check_color'];
	  	} else {
			$data['check_color'] = 0;
	  	}

		if (isset($this->request->post['colour_id'])) {
			$data['colour_id'] = $this->request->post['colour_id'];
	  	} elseif (!empty($order_info)) {
			$data['colour_id'] = $order_info['colour_id'];
	  	} else {	
			$data['colour_id'] = 0;
	  	}

		if (isset($this->request->post['moulder_id'])) {
			$data['moulder_id'] = $this->request->post['moulder_id'];
	  	} elseif (!empty($order_info)) {
			$data['moulder_id'] = $order_info['moulder_id'];
	  	} else {	
			$data['moulder_id'] = 0;
	  	}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
	  	} elseif (!empty($order_info)) {
			$data['address'] = $order_info['address'];
	  	} else {	
			$data['address'] = '';
	  	}

		if (isset($this->request->post['powder_id'])) {
			$data['powder_id'] = $this->request->post['powder_id'];
	  	} elseif (!empty($order_info)) {
			$data['powder_id'] = $order_info['powder_id'];
	  	} else {	
			$data['powder_id'] = 0;
	  	}

		if (isset($this->request->post['client_id'])) {
			$data['client_id'] = $this->request->post['client_id'];
	  	} elseif (!empty($order_info)) {
			$data['client_id'] = $order_info['client_id'];
	  	} else {	
			$data['client_id'] = 0;
	  	}

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
	  	} elseif (!empty($order_info)) {
			$data['product_id'] = $order_info['product_id'];
	  	} else {	
			$data['product_id'] = 0;
	  	}

		if (isset($this->request->post['targeted_date'])) {
			$data['targeted_date'] = $this->request->post['targeted_date'];
	  	} elseif (!empty($order_info)) {
			$data['targeted_date'] = $order_info['targeted_date'];
	  	} else {	
			$data['targeted_date'] = '';
	  	}

		if (isset($this->request->post['po_no'])) {
			$data['po_no'] = $this->request->post['po_no'];
	  	} elseif (!empty($order_info)) {
			$data['po_no'] = $order_info['po_no'];
	  	} else {	
			$data['po_no'] = '';
	  	}

		if (isset($this->request->post['accessories_id'])) {
			$data['accessories_id'] = $this->request->post['accessories_id'];
	  	} elseif (!empty($order_info)) {
			$data['accessories_id'] = $order_info['accessories_id'];
	  	} else {	
			$data['accessories_id'] = '';
	  	}

		if (isset($this->request->post['master_batch_id'])) {
			$data['master_batch_id'] = $this->request->post['master_batch_id'];
	  	} elseif (!empty($order_info)) {
			$data['master_batch_id'] = $order_info['master_batch_id'];
	  	} else {	
			$data['master_batch_id'] = '';
	  	}

		if (isset($this->request->post['pigment_id'])) {
			$data['pigment_id'] = $this->request->post['pigment_id'];
	  	} elseif (!empty($order_info)) {
			$data['pigment_id'] = $order_info['pigment_id'];
	  	} else {	
			$data['pigment_id'] = '';
	  	}

		if (isset($this->request->post['die_id'])) {
			$data['die_id'] = $this->request->post['die_id'];
	  	} elseif (!empty($order_info)) {
			$data['die_id'] = $order_info['die_id'];
	  	} else {	
			$data['die_id'] = 0;
	  	}

		if (isset($this->request->post['fittings_id'])) {
			$data['fittings_id'] = $this->request->post['fittings_id'];
	  	} elseif (!empty($order_info)) {
			$data['fittings_id'] = $order_info['fittings_id'];
	  	} else {	
			$data['fittings_id'] = '';
	  	}

		if (isset($this->request->post['order_type'])) {
			$data['order_type'] = $this->request->post['order_type'];
	  	} elseif (!empty($order_info)) {
			$data['order_type'] = $order_info['order_type'];
	  	} else {	
			$data['order_type'] = 0;
	  	}

		if (isset($this->request->post['req_qty'])) {
			$data['req_qty'] = $this->request->post['req_qty'];
	  	} elseif (!empty($order_info)) {
			$data['req_qty'] = $order_info['req_qty'];
	  	} else {	
			$data['req_qty'] = 0;
	  	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/order_form', $data));
	}

	protected function validateImport(){

		$ext = pathinfo($this->request->files['import_file']['name'], PATHINFO_EXTENSION);

		if(empty($this->request->files['import_file']['name']) || $ext != 'csv') {

			$this->error['import_file'] = $this->language->get('error_import_file');
		
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	
	}
	
	protected function validateForm() {
    	if (!$this->user->hasPermission('modify', 'catalog/order')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	if ((strlen($this->request->post['order_name']) < 2) || (strlen($this->request->post['order_name']) > 64)) {
      		$this->error['name'] = 'Supplier name should be greater than 2 characters';
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function export(){
	
		$this->load->model('mastesr/order');

		$data = array(
			'sort'  => 'type',
			'order' => 'ASC',
		);
			
		$order_range = "type,purity,weight,price\n";
		$results = $this->model_catalog_order->getOrders($data); 
    	foreach ($results as $result) {
						
			$order_range .=	$result['type'] ."," . 
								$result['purity']  ."," . 
								$result['weight'] ."," . 
								$result['price']  ."\n"; 
		}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($order_range));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=order.csv");
		echo $order_range;
		exit;

	}

	public function calculateQty(){
		$this->load->model('catalog/order');

		$accessories = [];

		$accessories = $this->model_catalog_order->getAccessoriesDetails($this->request->get);

		$calculate = !empty($accessories['weight']) && $accessories['weight'] > 0 ? (($this->request->get['qty'] * $accessories['weight']) / 1000)/25 : 0;

		$ceil_val = ceil($calculate);

		$final_val = (int)($ceil_val * 25 * 1000) / $accessories['weight'];

		$data = [
			'req_qty' => (int)$final_val,
			'bags' => (int)$ceil_val
		];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function clientAddress(){
		$this->load->model('catalog/order');

		$clientdata = $this->model_catalog_order->getClients($this->request->get);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($clientdata[0]['address']));
	}

	public function moulderAddress(){
		$this->load->model('catalog/order');

		$clientdata = $this->model_catalog_order->getMoulders($this->request->get);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($clientdata[0]['address']));
	}

	public function checkDie(){
		$this->load->model('catalog/order');

		$json = [];
		$json['success'] = "0";

		$dieData = $this->model_catalog_order->checkDie($this->request->get['die_id']);
		$moulderData = $this->model_catalog_order->getMoulders($dieData);

		if ($dieData['location'] == 1){
			$json['message'] = "Store";
			$json['success'] = "1";
		}else{
			$json['message'] = "Die is at ".$moulderData[0]['name']. " Moulder";
			if($this->request->get['moulder_id'] == $dieData['moulder_id']){
				$json['success'] = "1";
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function productWeight(){
		$this->load->model('catalog/order');

		$fittings = [];

		$fittings = $this->model_catalog_order->getFittingsDetails($this->request->get);

		// $calculate = ;

		// $ceil_val = ceil($calculate);

		$final_val = ceil($fittings['total_weight']);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode((int)$final_val));
	}

	public function checkAvaiability(){
		$this->load->model('catalog/order');
		// if(strpos($this->request->get['fittings_id'],'fitts_') != false){
		// 	$id = explode('_',$this->request->get['fittings_id']);
		// 	$fittings_id = $id[1];
		// }else{
		// $fittings_id = $this->request->get['fittings_id'];
		// }
		$qty = [];
		$json = [];

		$qty = $this->model_catalog_order->getFittings();

		foreach($qty as $value){
			if($value['qty'] <= 10000){
				$json['message'] = 'Fittings Quantity are less than 10,000';
			}elseif($value['qty'] >= 10000 && $this->request->get['qty'] <= $value['qty']){
				$json['message'] = 'Fittings Quantity are less than '.$this->request->get['qty'];
			}else{
				$json['message'] = 'Success';
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function exportData(){
		require_once(DIR_SYSTEM . 'library/dompdf/vendor/autoload.php');
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', true);
		$options->set('isRemoteEnabled', true);
		$options->set('debug', true);
		$dompdf = new Dompdf($options);
		$this->load->model('catalog/order');
		$accessory = '';
		$orders = $this->model_catalog_order->getOrder($this->request->get['orders_id']);
		$this->load->model('tool/image');
		if($orders['moulder_id'] != 0){
			$client_moulder_data = $this->model_catalog_order->getMoulder($orders['moulder_id']);
			$powder = $this->model_catalog_order->getPowder($orders['powder_id']);
			$acc_id = !empty($orders['acc_fitts_id']) && $orders['acc_fitts_id'] > 0 && str_starts_with($orders['acc_fitts_id'],'acc_') ? str_replace("acc_","",$orders['acc_fitts_id']) : ((!empty($orders['acc_fitts_id']) && $orders['acc_fitts_id'] > 0 && str_starts_with($orders['acc_fitts_id'],'fitts_')) ? str_replace("fitts_","",$orders['acc_fitts_id']) : 0);
			$accessory = $this->model_catalog_order->getAccessoryFittings($orders['acc_fitts_id']);
			$die = $this->model_catalog_order->getDietype($orders['die_id']);
			$colour = $this->model_catalog_order->getColour($orders['colour_id']);
			$product = $this->model_catalog_order->getProduct($orders['product_id']);
			if (is_file(DIR_IMAGE . htmlspecialchars($accessory['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
				$accessory['thumb'] = $this->model_tool_image->resize(htmlspecialchars($accessory['image'] ?? '', ENT_QUOTES, 'UTF-8'), 200, 200);
			} else {
				$accessory['thumb'] = $accessory['image'];
			}
			$po_date = strtotime($orders['date_added']);
			$targeted_date = strtotime($orders['targeted_date']);
			$html = '<html>
			<head>
				<style>
        	        /* Inline or local Bootstrap styles */
        	        body { font-family: Arial, sans-serif; }
					.detail_box p{font-size:16px;}
					table{border-collapse:collapse;}
					tr,td,th{border-style:solid;border-color:#000;}
					table > :not(caption) > * > *{border-width: 0 1px;}
					table > :not(caption) > *{border-width: 1px 0;}
        	    </style>
			</head>
			<body>';
			$html .= '<div style="display:flex;flex-direction:column;">';
			$html .= '<div style="max-width: 1320px;width:100%;border-width: 2px !important; border-color: #212529 !important;border-style:solid;margin-left:auto;margin-right:auto;margin-bottom:20px;margin-top:20px;display:block;" class="container">';
			$html .= '<h1 class="text-center mt-4" style="text-align:center;margin-top:10px;">Noel Enterprises</h1>';
			$html .= '<p style="font-size:15px;text-align:center;margin-bottom:10px;margin-top:0;">Store Office: - Sr. No. 132, Gala No. 02, Balaji Chawl, Waghralpada Main Road, Bhoidapada, Rajwal, Vasai East-401208</p>';
			$html .= '</div>';
			$html .= '<div style="max-width: 1320px;width:100%;border-width: 2px !important; border-color: #212529 !important;margin-left:auto;margin-right:auto;border:2px solid;display:block;">';
			$html .= '<div style="margin-left:30px;margin-right:30px;" class="mx-5 my-2 d-flex justify-content-between">';
			$html .= '<div style="display: inline-block; width: 65%; vertical-align: top;padding:0px 5px;" class="detail_box">';
			$html .= '<p>Vendor Name:- '.$client_moulder_data['name'].'</p>';
			$html .= '<p>'.$client_moulder_data['address'].'</p>';
			$html .= '</div>';
			$html .= '<div style="display: inline-block; width: 30%; vertical-align: top;" class="detail_box">';
			$html .= '<p>Vendor Code:- AE'.$orders['moulder_id'].'</p>';
			$html .= '<p>PO NO:- '.$orders['po_no'].'</p>';
			$html .= '<p>PO Date:- '.date("d-m-Y", $po_date).'</p>';
			$html .= '<p>Delivery Date:- '.date("d-m-Y", $targeted_date).'</p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<h4 style="text-align:center;margin-top:0.5rem;">Job Work Order</h4>';
			$html .= '<div style="max-width: 1320px;border:2px solid;border-width: 2px !important; border-color: #212529 !important;margin-left:auto;margin-right:auto;" class="container border border-dark border-2 mt-4">';
			$html .= '<p style="margin-bottom:0.5rem;margin-top:0.5rem;margin-left:20px;" class="mb-1">Please supply following materials as per the terms and conditions mentioned below.</p>';
			$html .= '</div>';
			$html .= '<table style="max-width: 1320px;margin:20px auto;width:100%;">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th rowspan="2" style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2"><img src="https://capandtab.com/image/'.(!empty($accessory) ? $accessory["image"] : "" ).'"" width="100" height="100" /></th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Parts Name / Fittings Name</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Powder Type</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Powder KG</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Powder Bags</th>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.$accessory['name'].'</td>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.(!empty($powder['name']) ? $powder['name'] : 'None').'</td>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.($orders['bags'] ? $orders['bags']*25 : 0).'</td>';
			if(!empty($powder['qty']) && $powder['qty'] > 0){
				$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.$orders['bags'].'</td>';
			}elseif(!empty($powder['qty'])){
				$html .= '<td style="padding:0rem;text-align:center;" class="text-center p-2">';
				
				$html .= '<table style="max-width: 1320px;margin:20px auto;width:100%;">';
				$html .= '<tr>';
				$html .= '<th>Total</th>';
				$html .= '<th>Delivered</th>';
				$html .= '<th>Balance</th>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td>'.($orders['bags']).'</td>';
				$html .= '<td>'.($orders['bags'] - abs($powder['qty'])).'</td>';
				$html .= '<td>'.(abs($powder['qty'])).'</td>';
				$html .= '</tr>';
				$html .= '</table>';
				
				$html .= '</td>';
			}else{
				$html .= '<td style="padding:0rem;text-align:center;" class="text-center p-2">0</td>';
			}
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '</table>';
			$html .= '<table style="max-width: 1320px;margin:20px auto;width:100%;" class="container table-bordered my-5">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Product Weight</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Pigment Quantity</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Master Batch Grams</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;" class="text-center p-2">Targeted Quantity</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$html .= '<tr>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.$accessory['weight'].'</td>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.(($orders['check_color'] == 1 || $orders['check_color'] == 0) ? $orders['bags'] : 0).'</td>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.(($orders['check_color'] == 1 || $orders['check_color'] == 0) ? 0 : ($orders['bags']*500)).'</td>';
			$html .= '<td style="padding:0.5rem;text-align:center;" class="text-center p-2">'.(!empty($orders['req_qty']) ? $orders['req_qty'] : $orders['qty']).'</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '<div style="max-width: 1320px;margin:20px auto;width:100%;border-width: 2px !important; border-color: #212529 !important;border-style:solid;">';
			$html .= '<div style="padding:10px;">';
			$html .= '<p style="font-size:12px;" class="mb-3">Mentioned above product’s Die is available with mentioned above moulder. This Die is Asha Enterprises Property. Asha Enterprises have the rights to take back the die without any prior notice.</p>';
			$html .= '<p style="font-size:12px;" class="mb-3">All invoices should be sent with full particulars such as purchase order No;, Date with proper instructions, together with E Way bill & E Invoice wherever  applicable. Failure to comply with this will delay the settlement of payments.</p>';
			$html .= '<p style="font-size:12px;" class="mb-1">Vendor shall issue GST compliant tax invoices as envisaged under GST Law containing details such as our GSTINs (as communicated), HSNs, tax etc. as required under rule 46 of CGST Act, 2017. Further, such invoice should be captured by the vendor in his outward supplies statements i.e. GSTR1 in the month when the supply was made. Further, relevant tax on such invoice should be duly deposited with the government exchequer by the vendor so as to enable us to claim input tax credit.</p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<p style="max-width: 1320px;margin:20px auto;width:100%;" class="container mt-3"><strong>Note:-</strong> This is a <strong>computer-generated</strong> document. No <strong>signature</strong> is <strong>required</strong>.</p>';
			$html .= '</body></html>';

		}elseif($orders['client_id'] != 0){
			$fittings = $this->model_catalog_order->getFittingss($orders['fittings_id']);
			$client_moulder_data = $this->model_catalog_order->getClient($orders['client_id']);
			$i = 0;
			$product_data = $this->model_catalog_order->getProduct($orders['product_id']);
			array_unshift($fittings,['name' => $product_data['name'], 'image' => $product_data['image']]);

			$payment_date = !empty($this->request->get['date']) ? strtotime($this->request->get['date']) : 0;
			$targeted_date = strtotime($orders['targeted_date']);

			$html = '<html>
			<head>
				<style>
        	        /* Inline or local Bootstrap styles */
        	        body { font-family: Arial, sans-serif; }
					.detail_box p{font-size:16px;}
					table{border-collapse:collapse;}
					tr,td,th{border-style:solid;border-color:#000;}
					table > :not(caption) > * > *{border-width: 0 1px;}
					table > :not(caption) > *{border-width: 1px 0;}
        	    </style>
			</head>
			<body>';
			$html .= '<div style="display:flex;flex-direction:column;">';
			$html .= '<div style="max-width: 1320px;width:100%;border-width: 2px !important; border-color: #212529 !important;margin-left:auto;margin-right:auto;border:2px solid;display:block;">';
			$html .= '<div style="margin-left:30px;margin-right:30px;" class="mx-5 my-2 d-flex justify-content-between">';
			$html .= '<div style="display: inline-block; width: 65%; vertical-align: top;padding:0px 5px;" class="detail_box">';
			$html .= '<p>Client Name:- '.$client_moulder_data['name'].'</p>';
			$html .= '<p>'.$client_moulder_data['address'].'</p>';
			$html .= '</div>';
			$html .= '<div style="display: inline-block; width: 30%; vertical-align: top;" class="detail_box">';
			$html .= '<p>Client Code:- AEC'.$orders['client_id'].'</p>';
			$html .= '<p>PO NO:- '.$orders['po_no'].'</p>';
			$html .= '<p>Delivery Date:- '.date("d-m-Y", $targeted_date).'</p>';
			$html .= '<p>Payment Terms:- '.($this->request->get['term'] == 1 ? 'Advance' : ($this->request->get['term'] == 2 && !empty($payment_date) ? date("d-m-Y", $payment_date) : '' ) ).'</p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div style="max-width: 1320px;width:100%;border-width: 2px !important; border-color: #212529 !important;border-style:solid;margin-left:auto;margin-right:auto;margin-bottom:20px;margin-top:20px;display:block;" class="container">';
			$html .= '<p style="font-size:15px;margin-bottom:0;margin-top:0;margin-left:10px;">Noel Enterprises, Sr. No. 132, Gala No. 02, Balaji Chawl, Waghralpada Main Road, Bhoidapada, Rajwal, Vasai East-401208</p>';
			// $html .= '<p style="font-size:15px;margin-bottom:0;margin-top:0;margin-left:10px;">Store Office: - </p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<h4 style="text-align:center;margin-top:0.5rem;">Purchase Order Received</h4>';
			$html .= '<div style="max-width: 1320px;margin-left:auto;margin-right:auto;" class="container border border-dark border-2 mt-4">';
			$html .= '<p style="margin-bottom:0.5rem;margin-top:0.5rem;" class="mb-1">Dear Sir” Kindly acknowledge mentioned below order received from you.</p>';
			$html .= '</div>';
			$html .= '<table style="max-width: 1320px;margin:20px auto;width:100%;">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;">Sr No</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;">Product / Fittings</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;">Name</th>';
			// $html .= '<th style="width:20%;padding:0.5rem;text-align:center;">Colour</th>';
			$html .= '<th style="width:20%;padding:0.5rem;text-align:center;">Quantity</th>';
			$html .= '</tr>';
			foreach($fittings as $key => $value){
				$i = $i + 1;
				$html .= '<tr>';
				$html .= '<td style="padding:0.5rem;text-align:center;">'.$i.'</td>';
				$html .= '<th style="width:20%;padding:0.5rem;text-align:center;"><img src="https://capandtab.com/image/'.$value["image"].'"" width="100" height="100" /></th>';
				$html .= '<td style="padding:0.5rem;text-align:center;">'.$value['name'].'</td>';
				// $html .= '<td style="padding:0.5rem;text-align:center;">'.$colour['name'].'</td>';
				$html .= '<td style="padding:0.5rem;text-align:center;">'.$orders['qty'].'</td>';
				$html .= '</tr>';
			}
			$html .= '</thead>';
			$html .= '</table>';
			$html .= '<p style="max-width: 1320px;margin:20px auto;width:100%;" class="container mt-3"><strong>Note:-</strong> This is a <strong>computer-generated</strong> document. No <strong>signature</strong> is <strong>required</strong>.</p>';
			$html .= '</body></html>';

		}

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
		$pdfOutput = $dompdf->output();

		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $client_moulder_data['name'] . " PO " . $orders['po_no'] . '.pdf"');
		echo $pdfOutput;
		exit;
        // $dompdf->stream($client_moulder_data['name']." PO ".$orders['po_no'].".pdf", array("Attachment" => 1));
	}


	public function send(){
		$data = [];
		$this->load->model('catalog/order');
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $orders_id) {
				$order_data = $this->model_catalog_order->getOrderDetail($orders_id);
				if($order_data['order_type'] == '0'){
					$email = $this->model_catalog_order->getMoulder($order_data['moulder_id']);
				}else{
					$email = $this->model_catalog_order->getClient($order_data['client_id']);
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
					// $data['phone'] = $this->config->get('config_telephone');
					$mail->setHtml($this->load->view('catalog/lr_copy_mail', $data));
					if (!empty($order_data)) {
						$mail->addAttachment(!empty($order_data['image']) ? $order_data['image'] : '');
					}
					$mail->send();
				}
			}

			$this->session->data['success'] = 'Mail Sent Successfully';

			$json['success'] = 'Mail Sent Successfully';
			$json['redirect'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}