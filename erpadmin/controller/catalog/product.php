<?php
namespace Opencart\Admin\Controller\Catalog;
class Product extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('catalog/product');
		$this->document->setTitle('Product');
		
		$this->load->model('catalog/product');

		$data['add'] = $this->url->link('catalog/product|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/product', $data));
	}

	public function save() {
		$this->load->language('catalog/product');
		$json = [];
		$this->document->setTitle('Product');

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['product_id']){
				$this->model_catalog_product->addProduct($this->request->post);
			}else{
				$this->model_catalog_product->editProduct($this->request->post['product_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('catalog/product');

		$this->document->setTitle('Delete Product');

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			// $this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}
	
	public function import() {
		$this->language->load('catalog/product');

    	$this->document->setTitle('Import Product');
		
		$this->load->model('catalog/product');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			
			$this->model_catalog_product->importSupplier($this->request->files);

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
			
			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		// $data['add'] = $this->url->link('catalog/product|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('catalog/product|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('catalog/product|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$product_total = $this->model_catalog_product->getTotalProducts();
		
		$results = $this->model_catalog_product->getProducts($supplier_data);
		
		$this->load->model('tool/image');
		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . htmlspecialchars($result['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
				$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($result['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
			} else {
				$data['thumb'] = '';
			}

			$data['products'][] = array(
				'product_id'		=> $result['product_id'],
				'name'				=> $result['name'],
				'image'				=> $data['thumb'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('catalog/product|form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
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
		// $pagination->url = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		// $data['pagination'] = $pagination->render();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('catalog/product|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('catalog/product_list', $data);
	}

	public function form() {
		$this->load->model('catalog/product');
		$data['text_form'] = !isset($this->request->get['product_id']) ? 'Add' : 'Edit';

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = array();
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
			'text' => 'Product',
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('catalog/product|save', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// if (!isset($this->request->get['product_id'])) {
		// } else {
		// 	$data['action'] = $this->url->link('catalog/product|edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
		// }

		$data['back'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($product_info)) {
			$data['name'] = $product_info['name'];
		} else {	
      		$data['name'] = '';
    	}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
	  	} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
	  	} else {	
			$data['image'] = '';
	  	}

		$data['categories'] = $this->model_catalog_product->getCategories();

		if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
	  	} elseif (!empty($product_info)) {
			$data['category_id'] = $product_info['category_id'];
	  	} else {	
			$data['category_id'] = '';
	  	}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $data['image'];
		}

		if(isset($this->request->get['product_id'])){
			$data['product_id'] = $this->request->get['product_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_form', $data));
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
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	if ((strlen($this->request->post['product_name']) < 2) || (strlen($this->request->post['product_name']) > 64)) {
      		$this->error['name'] = 'Supplier name should be greater than 2 characters';
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function export(){
	
		$this->load->model('mastesr/product');

		$data = array(
			'sort'  => 'type',
			'order' => 'ASC',
		);
			
		$product_range = "type,purity,weight,price\n";
		$results = $this->model_catalog_product->getProducts($data); 
    	foreach ($results as $result) {
						
			$product_range .=	$result['type'] ."," . 
								$result['purity']  ."," . 
								$result['weight'] ."," . 
								$result['price']  ."\n"; 
		}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($product_range));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=product.csv");
		echo $product_range;
		exit;

	}

	public function showImg(){
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);

		if (is_file(DIR_IMAGE . htmlspecialchars($product_info['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($product_info['image'] ?? '', ENT_QUOTES, 'UTF-8'), 400, 400);
		} else {
			$data['thumb'] = '';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data['thumb']));
	}
}