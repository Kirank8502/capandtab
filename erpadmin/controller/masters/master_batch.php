<?php
namespace Opencart\Admin\Controller\Masters;
class MasterBatch extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/master_batch');
		$this->document->setTitle('Master Batch');
		
		$this->load->model('masters/master_batch');

		$data['add'] = $this->url->link('masters/master_batch|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/master_batch|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/master_batch', $data));
	}

	public function save() {
		$this->load->language('masters/master_batch');
		$json = [];
		$this->document->setTitle('Master Batch');

		$this->load->model('masters/master_batch');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['master_batch_id']){
				$this->model_masters_master_batch->addMasterBatch($this->request->post);
			}else{
				$this->model_masters_master_batch->editMasterBatch($this->request->post['master_batch_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/master_batch');

		$this->document->setTitle('Delete Master Batch');

		$this->load->model('masters/master_batch');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $master_batch_id) {
				$this->model_masters_master_batch->deleteMasterBatch($master_batch_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
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
			'href' => $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['master_batchs'] = array();

		$master_batch_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$master_batch_total = $this->model_masters_master_batch->getTotalMasterBatchs();
		
		$results = $this->model_masters_master_batch->getMasterBatchs($master_batch_data);
		
		foreach ($results as $result) {
			$data['master_batchs'][] = array(
				'master_batch_id'		=> $result['master_batch_id'],
				'name'				=> $result['name'],
				'qty'				=> $result['qty'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['master_batch_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/master_batch|form', 'user_token=' . $this->session->data['user_token'] . '&master_batch_id=' . $result['master_batch_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $master_batch_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/master_batch|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($master_batch_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($master_batch_total - $this->config->get('config_pagination_admin'))) ? $master_batch_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $master_batch_total, ceil($master_batch_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/master_batch_list', $data);
	}

	public function form() {
		$this->load->model('masters/master_batch');
		$data['text_form'] = !isset($this->request->get['master_batch_id']) ? 'Add' : 'Edit';

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['master_batch'])) {
			$data['error_master_batch'] = $this->error['master_batch'];
		} else {
			$data['error_master_batch'] = array();
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
			'text' => 'master_batch',
			'href' => $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/master_batch|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('masters/master_batch', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['master_batch_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$master_batch_info = $this->model_masters_master_batch->getMasterBatch($this->request->get['master_batch_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($master_batch_info)) {
			$data['name'] = $master_batch_info['name'];
		} else {	
      		$data['name'] = '';
    	}

		if (isset($this->request->post['color'])) {
			$data['color'] = $this->request->post['color'];
	  	} elseif (!empty($master_batch_info)) {
			$data['color'] = $master_batch_info['color'];
	  	} else {	
			$data['color'] = '';
	  	}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
	  	} elseif (!empty($master_batch_info)) {
			$data['image'] = $master_batch_info['image'];
	  	} else {	
			$data['image'] = '';
	  	}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
	  	} elseif (!empty($master_batch_info)) {
			$data['qty'] = $master_batch_info['qty'];
	  	} else {	
			$data['qty'] = '';
	  	}

		if(isset($this->request->get['master_batch_id'])){
			$data['master_batch_id'] = $this->request->get['master_batch_id'];
		}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $data['image'];
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
	  	} elseif (!empty($master_batch_info)) {
			$data['weight'] = $master_batch_info['weight'];
	  	} else {	
			$data['weight'] = 0;
	  	}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($master_batch_info)) {
			$data['status'] = $master_batch_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/master_batch_form', $data));
	}
}