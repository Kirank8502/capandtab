<?php
namespace Opencart\Admin\Controller\Masters;
class Dies extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/dies');
		$this->document->setTitle('Die');
		
		$this->load->model('masters/dies');

		$data['add'] = $this->url->link('masters/dies|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/dies|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/die', $data));
	}

	public function save() {
		$this->load->language('masters/dies');
		$json = [];
		$this->document->setTitle('Die');

		$this->load->model('masters/dies');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['die_id']){
				$this->model_masters_dies->addDie($this->request->post);
			}else{
				$this->model_masters_dies->editDie($this->request->post['die_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/dies');

		$this->document->setTitle('Delete Die');

		$this->load->model('masters/dies');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $die_id) {
				$this->model_masters_dies->deleteDie($die_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'], true);
			
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
			'text' => 'Die',
			'href' => $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['dies'] = array();

		$die_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * (int)$this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$die_total = $this->model_masters_dies->getTotalDies($die_data);
		
		$results = $this->model_masters_dies->getDies($die_data);
		
		foreach ($results as $result) {
			$moulder_id = $this->model_masters_dies->getMoulders($result['moulder_id']);
			$data['dies'][] = array(
				'die_id'		=> $result['die_id'],
				'name'				=> $result['name'],
				'type'				=> $result['type'],
				'location'			=> $result['location'],
				'moulder_id'		=> $moulder_id['name'],
				'weight'			=> $result['weight'],		
				'edit'				=> $this->url->link('masters/dies|form', 'user_token=' . $this->session->data['user_token'] . '&die_id=' . $result['die_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $die_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('masters/dies|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($die_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($die_total - $this->config->get('config_pagination_admin'))) ? $die_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $die_total, ceil($die_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/die_list', $data);
	}

	public function form() {
		$this->load->model('masters/dies');
		$data['text_form'] = !isset($this->request->get['die_id']) ? 'Add' : 'Edit';

		$this->document->setTitle('Die');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['die'])) {
			$data['error_die'] = $this->error['die'];
		} else {
			$data['error_die'] = array();
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
			'text' => 'Die',
			'href' => $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/dies|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('masters/dies', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['die_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$die_info = $this->model_masters_dies->getDie($this->request->get['die_id']);
		}

		$data['colours'] = $this->model_masters_dies->getColors();
		$data['moulders'] = $this->model_masters_dies->getAllMoulders();

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($die_info)) {
			$data['name'] = $die_info['name'];
		} else {	
      		$data['name'] = '';
    	}

		if (isset($this->request->post['moulder_id'])) {
			$data['moulder_id'] = $this->request->post['moulder_id'];
	  	} elseif (!empty($die_info)) {
			$data['moulder_id'] = $die_info['moulder_id'];
	  	} else {	
			$data['moulder_id'] = 0;
	  	}

		if(isset($this->request->get['die_id'])){
			$data['die_id'] = $this->request->get['die_id'];
		}
		
    	if (isset($this->request->post['date'])) {
      		$data['date'] = $this->request->post['date'];
    	} elseif (!empty($die_info)) {
			$data['date'] = $die_info['date'];
		} else {	
      		$data['date'] = '';
    	}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
	  	} elseif (!empty($die_info)) {
			$data['location'] = $die_info['location'];
	  	} else {	
			$data['location'] = '';
	  	}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($die_info)) {
			$data['height'] = $die_info['height'];
	  	} else {	
			$data['height'] = 0.00;
	  	}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($die_info)) {
			$data['width'] = $die_info['width'];
	  	} else {	
			$data['width'] = 0.00;
	  	}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($die_info)) {
			$data['weight'] = $die_info['weight'];
	  	} else {	
			$data['weight'] = 0;
	  	}

		if (isset($this->request->post['cavity'])) {
			$data['cavity'] = $this->request->post['cavity'];
		} elseif (!empty($die_info)) {
			$data['cavity'] = $die_info['cavity'];
	  	} else {	
			$data['cavity'] = 0;
	  	}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($die_info)) {
			$data['type'] = $die_info['type'];
	  	} else {	
			$data['type'] = 0;
	  	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/die_form', $data));
	}
}