<?php
namespace Opencart\Admin\Controller\Masters;
class Assembler extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/assembler');
		$this->document->setTitle('Assembler');
		
		$this->load->model('masters/assembler');

		$data['add'] = $this->url->link('masters/assembler|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/assembler|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/assembler', $data));
	}

	public function save() {
		$this->load->language('masters/assembler');
		$json = [];
		$this->document->setTitle('Assembler');

		$this->load->model('masters/assembler');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['assembler_id']){
				$this->model_masters_assembler->addAssembler($this->request->post);
			}else{
				$this->model_masters_assembler->editAssembler($this->request->post['assembler_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/assembler');

		$this->document->setTitle('Delete Assembler');

		$this->load->model('masters/assembler');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $assembler_id) {
				$this->model_masters_assembler->deleteAssembler($assembler_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			// $this->response->redirect($this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}
	
	public function import() {
		$this->language->load('masters/assembler');

    	$this->document->setTitle('Import Assembler');
		
		$this->load->model('masters/assembler');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			
			$this->model_masters_assembler->importSupplier($this->request->files);

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
			
			$this->response->redirect($this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		// $data['add'] = $this->url->link('masters/assembler|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('masters/assembler|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('masters/assembler|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['assemblers'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$assembler_total = $this->model_masters_assembler->getTotalAssemblers();
		
		$results = $this->model_masters_assembler->getAssemblers($supplier_data);
		
		foreach ($results as $result) {
			$data['assemblers'][] = array(
				'assembler_id'		=> $result['assembler_id'],
				'name'				=> $result['name'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['assembler_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/assembler|form', 'user_token=' . $this->session->data['user_token'] . '&assembler_id=' . $result['assembler_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
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
		// $pagination->url = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		// $data['pagination'] = $pagination->render();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $assembler_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/assembler|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($assembler_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($assembler_total - $this->config->get('config_pagination_admin'))) ? $assembler_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $assembler_total, ceil($assembler_total / $this->config->get('config_pagination_admin')));

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($stone_pnc_range_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stone_pnc_range_total - $this->config->get('config_limit_admin'))) ? $stone_pnc_range_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stone_pnc_range_total, ceil($stone_pnc_range_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/assembler_list', $data);
	}

	public function form() {
		$this->load->model('masters/assembler');
		$data['text_form'] = !isset($this->request->get['assembler_id']) ? 'Add' : 'Edit';

		$this->document->setTitle('Assembler');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['assembler'])) {
			$data['error_assembler'] = $this->error['assembler'];
		} else {
			$data['error_assembler'] = array();
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
			'text' => 'Assembler',
			'href' => $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/assembler|save', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// if (!isset($this->request->get['assembler_id'])) {
		// } else {
		// 	$data['action'] = $this->url->link('masters/assembler|edit', 'user_token=' . $this->session->data['user_token'] . '&assembler_id=' . $this->request->get['assembler_id'] . $url, true);
		// }

		$data['back'] = $this->url->link('masters/assembler', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['assembler_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$assembler_info = $this->model_masters_assembler->getAssembler($this->request->get['assembler_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($assembler_info)) {
			$data['name'] = $assembler_info['name'];
		} else {
      		$data['name'] = '';
    	}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
	  	} elseif (!empty($assembler_info)) {
			$data['email'] = $assembler_info['email'];
	  	} else {
			$data['email'] = '';
	  	}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
	  	} elseif (!empty($assembler_info)) {
			$data['address'] = $assembler_info['address'];
	  	} else {
			$data['address'] = '';
	  	}

		if (isset($this->request->post['number'])) {
			$data['number'] = $this->request->post['number'];
	  	} elseif (!empty($assembler_info)) {
			$data['number'] = $assembler_info['number'];
	  	} else {
			$data['number'] = '';
	  	}

		if (isset($this->request->post['bank_name'])) {
			$data['bank_name'] = $this->request->post['bank_name'];
	  	} elseif (!empty($assembler_info)) {
			$data['bank_name'] = $assembler_info['bank_name'];
	  	} else {
			$data['bank_name'] = '';
	  	}

		if (isset($this->request->post['account_no'])) {
			$data['account_no'] = $this->request->post['account_no'];
	  	} elseif (!empty($assembler_info)) {
			$data['account_no'] = $assembler_info['account_no'];
	  	} else {
			$data['account_no'] = '';
	  	}

		if (isset($this->request->post['ifsc_code'])) {
			$data['ifsc_code'] = $this->request->post['ifsc_code'];
	  	} elseif (!empty($assembler_info)) {
			$data['ifsc_code'] = $assembler_info['ifsc_code'];
	  	} else {
			$data['ifsc_code'] = '';
	  	}

		if(isset($this->request->get['assembler_id'])){
			$data['assembler_id'] = $this->request->get['assembler_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($assembler_info)) {
			$data['status'] = $assembler_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/assembler_form', $data));
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
    	if (!$this->user->hasPermission('modify', 'masters/assembler')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	if ((strlen($this->request->post['assembler_name']) < 2) || (strlen($this->request->post['assembler_name']) > 64)) {
      		$this->error['name'] = 'Supplier name should be greater than 2 characters';
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function export(){
	
		$this->load->model('mastesr/assembler');

		$data = array(
			'sort'  => 'type',
			'order' => 'ASC',
		);
			
		$product_range = "type,purity,weight,price\n";
		$results = $this->model_masters_assembler->getAssemblers($data); 
    	foreach ($results as $result) {
						
			$product_range .=	$result['type'] ."," . 
								$result['purity']  ."," . 
								$result['weight'] ."," . 
								$result['price']  ."\n"; 
		}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($product_range));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=assembler.csv");
		echo $product_range;
		exit;

	}
}