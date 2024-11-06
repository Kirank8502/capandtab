<?php
namespace Opencart\Admin\Controller\Masters;
class Pigment extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/pigment');
		$this->document->setTitle('Pigment');
		
		$this->load->model('masters/pigment');

		$data['add'] = $this->url->link('masters/pigment|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/pigment|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/pigment', $data));
	}

	public function save() {
		$this->load->language('masters/pigment');
		$json = [];
		$this->document->setTitle('Pigment');

		$this->load->model('masters/pigment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['pigment_id']){
				$this->model_masters_pigment->addPigment($this->request->post);
			}else{
				$this->model_masters_pigment->editPigment($this->request->post['pigment_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/pigment');

		$this->document->setTitle('Delete Pigment');

		$this->load->model('masters/pigment');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $pigment_id) {
				$this->model_masters_pigment->deletePigment($pigment_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'], true);
			
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

		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = '';
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

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
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
			'text' => 'Pigment',
			'href' => $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['pigments'] = array();

		$pigment_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$pigment_total = $this->model_masters_pigment->getTotalPigments();
		
		$results = $this->model_masters_pigment->getPigments($pigment_data);
		foreach ($results as $result) {
			$colour_name = $this->model_masters_pigment->getColour($result['colour_id']);
			$data['pigments'][] = array(
				'pigment_id'		=> $result['pigment_id'],
				'name'				=> $colour_name['name'],
				'qty'				=> $result['qty'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['pigment_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/pigment|form', 'user_token=' . $this->session->data['user_token'] . '&pigment_id=' . $result['pigment_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $pigment_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/pigment|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pigment_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($pigment_total - $this->config->get('config_pagination_admin'))) ? $pigment_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $pigment_total, ceil($pigment_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/pigment_list', $data);
	}

	public function form() {
		$this->load->model('masters/pigment');
		$data['text_form'] = !isset($this->request->get['pigment_id']) ? 'Add' : 'Edit';

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['pigment'])) {
			$data['error_pigment'] = $this->error['pigment'];
		} else {
			$data['error_pigment'] = array();
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
			'text' => 'Pigment',
			'href' => $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/pigment|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('masters/pigment', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['pigment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$pigment_info = $this->model_masters_pigment->getPigment($this->request->get['pigment_id']);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
	  	} elseif (!empty($pigment_info)) {
			$data['image'] = $pigment_info['image'];
	  	} else {	
			$data['image'] = '';
	  	}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $data['image'];
		}

		$data['colours'] = $this->model_masters_pigment->getColors();

		$data['user_token'] = $this->session->data['user_token'];

		if(isset($this->request->get['pigment_id'])){
			$data['pigment_id'] = $this->request->get['pigment_id'];
		}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($pigment_info)) {
			$data['status'] = $pigment_info['status'];
		} else {	
      		$data['status'] = '';
    	}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
		} elseif (!empty($pigment_info)) {
			$data['qty'] = $pigment_info['qty'];
	  	} else {	
			$data['qty'] = 0;
	  	}

		if (isset($this->request->post['colour_id'])) {
			$data['colour_id'] = $this->request->post['colour_id'];
		} elseif (!empty($pigment_info)) {
			$data['colour_id'] = $pigment_info['colour_id'];
	  	} else {	
			$data['colour_id'] = 0;
	  	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/pigment_form', $data));
	}
}