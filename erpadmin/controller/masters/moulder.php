<?php
namespace Opencart\Admin\Controller\Masters;
class Moulder extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/moulder');
		$this->document->setTitle('Moulder');
		
		$this->load->model('masters/moulder');

		$data['add'] = $this->url->link('masters/moulder|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/moulder|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/moulder', $data));
	}

	public function save() {
		$this->load->language('masters/moulder');
		$json = [];
		$this->document->setTitle('Moulder');

		$this->load->model('masters/moulder');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['moulder_id']){
				$this->model_masters_moulder->addMoulder($this->request->post);
			}else{
				$this->model_masters_moulder->editMoulder($this->request->post['moulder_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/moulder');

		$this->document->setTitle('Delete Moulder');

		$this->load->model('masters/moulder');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $moulder_id) {
				$this->model_masters_moulder->deleteMoulder($moulder_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			// $this->response->redirect($this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}
	
	public function import() {
		$this->language->load('masters/moulder');

    	$this->document->setTitle('Import Moulder');
		
		$this->load->model('masters/moulder');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			
			$this->model_masters_moulder->importSupplier($this->request->files);

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
			
			$this->response->redirect($this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		// $data['add'] = $this->url->link('masters/moulder|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('masters/moulder|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('masters/moulder|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['moulders'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$moulder_total = $this->model_masters_moulder->getTotalMoulders();
		
		$results = $this->model_masters_moulder->getMoulders($supplier_data);
		
		foreach ($results as $result) {
			$data['moulders'][] = array(
				'moulder_id'		=> $result['moulder_id'],
				'name'				=> $result['name'],
				'email'				=> $result['email'],
				'address'			=> $result['address'],
				'number'			=> $result['number'],
				'bank_name'			=> $result['bank_name'],
				'account_no'		=> $result['account_no'],
				'ifsc_code'			=> $result['ifsc_code'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['moulder_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/moulder|form', 'user_token=' . $this->session->data['user_token'] . '&moulder_id=' . $result['moulder_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
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
		// $pagination->url = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		// $data['pagination'] = $pagination->render();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $moulder_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/moulder|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($moulder_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($moulder_total - $this->config->get('config_pagination_admin'))) ? $moulder_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $moulder_total, ceil($moulder_total / $this->config->get('config_pagination_admin')));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/moulder_list', $data);
	}

	public function form() {
		$this->load->model('masters/moulder');
		$data['text_form'] = !isset($this->request->get['moulder_id']) ? 'Add' : 'Edit';

		$this->document->setTitle('Moulder');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['moulder'])) {
			$data['error_moulder'] = $this->error['moulder'];
		} else {
			$data['error_moulder'] = array();
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
			'text' => 'Moulder',
			'href' => $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/moulder|save', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// if (!isset($this->request->get['moulder_id'])) {
		// } else {
		// 	$data['action'] = $this->url->link('masters/moulder|edit', 'user_token=' . $this->session->data['user_token'] . '&moulder_id=' . $this->request->get['moulder_id'] . $url, true);
		// }

		$data['back'] = $this->url->link('masters/moulder', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['moulder_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$moulder_info = $this->model_masters_moulder->getMoulder($this->request->get['moulder_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($moulder_info)) {
			$data['name'] = $moulder_info['name'];
		} else {
      		$data['name'] = '';
    	}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
	  	} elseif (!empty($moulder_info)) {
			$data['email'] = $moulder_info['email'];
	  	} else {
			$data['email'] = '';
	  	}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
	  	} elseif (!empty($moulder_info)) {
			$data['address'] = $moulder_info['address'];
	  	} else {
			$data['address'] = '';
	  	}

		if (isset($this->request->post['number'])) {
			$data['number'] = $this->request->post['number'];
	  	} elseif (!empty($moulder_info)) {
			$data['number'] = $moulder_info['number'];
	  	} else {
			$data['number'] = '';
	  	}

		if (isset($this->request->post['bank_name'])) {
			$data['bank_name'] = $this->request->post['bank_name'];
	  	} elseif (!empty($moulder_info)) {
			$data['bank_name'] = $moulder_info['bank_name'];
	  	} else {
			$data['bank_name'] = '';
	  	}

		if (isset($this->request->post['account_no'])) {
			$data['account_no'] = $this->request->post['account_no'];
	  	} elseif (!empty($moulder_info)) {
			$data['account_no'] = $moulder_info['account_no'];
	  	} else {
			$data['account_no'] = '';
	  	}

		if (isset($this->request->post['ifsc_code'])) {
			$data['ifsc_code'] = $this->request->post['ifsc_code'];
	  	} elseif (!empty($moulder_info)) {
			$data['ifsc_code'] = $moulder_info['ifsc_code'];
	  	} else {
			$data['ifsc_code'] = '';
	  	}

		if(isset($this->request->get['moulder_id'])){
			$data['moulder_id'] = $this->request->get['moulder_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($moulder_info)) {
			$data['status'] = $moulder_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/moulder_form', $data));
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
    	if (!$this->user->hasPermission('modify', 'masters/moulder')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	if ((strlen($this->request->post['moulder_name']) < 2) || (strlen($this->request->post['moulder_name']) > 64)) {
      		$this->error['name'] = 'Supplier name should be greater than 2 characters';
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function export(){
	
		$this->load->model('mastesr/moulder');

		$data = array(
			'sort'  => 'type',
			'order' => 'ASC',
		);
			
		$product_range = "type,purity,weight,price\n";
		$results = $this->model_masters_moulder->getMoulders($data); 
    	foreach ($results as $result) {
						
			$product_range .=	$result['type'] ."," . 
								$result['purity']  ."," . 
								$result['weight'] ."," . 
								$result['price']  ."\n"; 
		}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($product_range));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=moulder.csv");
		echo $product_range;
		exit;

	}
}