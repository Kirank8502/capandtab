<?php
namespace Opencart\Admin\Controller\Masters;
class Shape extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/shape');
		$this->document->setTitle('Shape');
		
		$this->load->model('masters/shape');

		$data['add'] = $this->url->link('masters/shape|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/shape|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/shape', $data));
	}

	public function save() {
		$this->load->language('masters/shape');
		$json = [];
		$this->document->setTitle('Shape');

		$this->load->model('masters/shape');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['shape_id']){
				$this->model_masters_shape->addShape($this->request->post);
			}else{
				$this->model_masters_shape->editShape($this->request->post['shape_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/shape');

		$this->document->setTitle('Delete Shape');

		$this->load->model('masters/shape');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $shape_id) {
				$this->model_masters_shape->deleteShape($shape_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'], true);
			
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
			'href' => $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['shapes'] = array();

		$shape_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$shape_total = $this->model_masters_shape->getTotalShapes();
		
		$results = $this->model_masters_shape->getShapes($shape_data);
		
		foreach ($results as $result) {
			$data['shapes'][] = array(
				'shape_id'		=> $result['shape_id'],
				'name'				=> $result['name'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['shape_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/shape|form', 'user_token=' . $this->session->data['user_token'] . '&shape_id=' . $result['shape_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $shape_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/shape|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($shape_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($shape_total - $this->config->get('config_pagination_admin'))) ? $shape_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $shape_total, ceil($shape_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/shape_list', $data);
	}

	public function form() {
		$this->load->model('masters/shape');
		$data['text_form'] = !isset($this->request->get['shape_id']) ? 'Add' : 'Edit';

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['shape'])) {
			$data['error_shape'] = $this->error['shape'];
		} else {
			$data['error_shape'] = array();
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
			'text' => 'shape',
			'href' => $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/shape|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('masters/shape', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['shape_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$shape_info = $this->model_masters_shape->getShape($this->request->get['shape_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($shape_info)) {
			$data['name'] = $shape_info['name'];
		} else {	
      		$data['name'] = '';
    	}

		if(isset($this->request->get['shape_id'])){
			$data['shape_id'] = $this->request->get['shape_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($shape_info)) {
			$data['status'] = $shape_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/shape_form', $data));
	}
}