<?php
namespace Opencart\Admin\Controller\Catalog;
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
		$this->document->setTitle('Order');

		$this->load->model('catalog/order');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['orders_id']){
				$this->model_catalog_order->addOrder($this->request->post);
			}else{
				$this->model_catalog_order->editOrder($this->request->post['orders_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'], true);
			
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

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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

		// $data['add'] = $this->url->link('catalog/order|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('catalog/order|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('catalog/order|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['orders'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$order_total = $this->model_catalog_order->getTotalOrders();
		
		$results = $this->model_catalog_order->getOrders($supplier_data);
		$products = $this->model_catalog_order->getProducts();

		foreach($products as $key => $value) {
			$product[$value['product_id']] = $value['name'];
		}
		
		foreach ($results as $result) {
			$data['orders'][] = array(
				'orders_id'			=> $result['orders_id'],
				'product_name'      => $product[$result['product_id']],
				'qty'				=> $result['qty'],
				'order_type'		=> $result['order_type'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['orders_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('catalog/order|form', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $result['orders_id'] . $url, true)
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
		$data['sort_status'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		// $pagination = new Pagination();
		// $pagination->total = $stone_pnc_range_total;
		// $pagination->page = $page;
		// $pagination->limit = $this->config->get('config_limit_admin');
		// $pagination->url = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		// $data['pagination'] = $pagination->render();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $order_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('catalog/order|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($order_total - $this->config->get('config_pagination_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $order_total, ceil($order_total / $this->config->get('config_pagination_admin')));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('catalog/order_list', $data);
	}

	public function form() {
		$this->load->model('catalog/order');
		$data['text_form'] = !isset($this->request->get['orders_id']) ? 'Add' : 'Edit';

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
		// if (!isset($this->request->get['orders_id'])) {
		// } else {
		// 	$data['action'] = $this->url->link('catalog/order|edit', 'user_token=' . $this->session->data['user_token'] . '&orders_id=' . $this->request->get['orders_id'] . $url, true);
		// }

		$data['back'] = $this->url->link('catalog/order', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = $this->model_catalog_order->getProducts();
		$data['clients'] = $this->model_catalog_order->getClients();
		$data['powders'] = $this->model_catalog_order->getPowders();
		$data['colours'] = $this->model_catalog_order->getColours();

		if (isset($this->request->get['orders_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$order_info = $this->model_catalog_order->getOrder($this->request->get['orders_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		// if (isset($this->request->post['name'])) {
      	// 	$data['name'] = $this->request->post['name'];
    	// } elseif (!empty($order_info)) {
		// 	$data['name'] = $order_info['name'];
		// } else {	
      	// 	$data['name'] = '';
    	// }

		if(isset($this->request->get['orders_id'])){
			$data['orders_id'] = $this->request->get['orders_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($order_info)) {
			$data['status'] = $order_info['status'];
		} else {	
      		$data['status'] = '';
    	}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
	  	} elseif (!empty($order_info)) {
			$data['qty'] = $order_info['qty'];
	  	} else {	
			$data['qty'] = 0;
	  	}

		if (isset($this->request->post['colour_id'])) {
			$data['colour_id'] = $this->request->post['colour_id'];
	  	} elseif (!empty($order_info)) {
			$data['colour_id'] = $order_info['colour_id'];
	  	} else {	
			$data['colour_id'] = 0;
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

		if (isset($this->request->post['remark'])) {
			$data['remark'] = $this->request->post['remark'];
	  	} elseif (!empty($order_info)) {
			$data['remark'] = $order_info['remark'];
	  	} else {	
			$data['remark'] = '';
	  	}

		if (isset($this->request->post['order_type'])) {
			$data['order_type'] = $this->request->post['order_type'];
	  	} elseif (!empty($order_info)) {
			$data['order_type'] = $order_info['order_type'];
	  	} else {	
			$data['order_type'] = 0;
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
}