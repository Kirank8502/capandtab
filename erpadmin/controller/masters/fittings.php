<?php
namespace Opencart\Admin\Controller\Masters;
class Fittings extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('masters/fittings');
		$this->document->setTitle('Extra Fittings');
		
		$this->load->model('masters/fittings');

		$data['add'] = $this->url->link('masters/fittings|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('masters/fittings|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('masters/fittings', $data));
	}

	public function save() {
		$this->load->language('masters/fittings');
		$json = [];
		$this->document->setTitle('Extra Fittings');

		$this->load->model('masters/fittings');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['fittings_id']){
				$this->model_masters_fittings->addFitting($this->request->post);
			}else{
				$this->model_masters_fittings->editFitting($this->request->post['fittings_id'], $this->request->post);
			}

			$this->session->data['success'] = 'Successfully Saved';
			$json['success'] = 'Successfully Saved';
			$json['redirect'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('masters/fittings');

		$this->document->setTitle('Delete Extra Fittings');

		$this->load->model('masters/fittings');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $fittings_id) {
				$this->model_masters_fittings->deleteFitting($fittings_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'], true);
			
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
			'text' => 'Extra Fittings',
			'href' => $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['fittings'] = array();

		$fittings_data = array(
			'filter_name'  => $filter_name,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$fittings_total = $this->model_masters_fittings->getTotalFittings();
		
		$results = $this->model_masters_fittings->getFittings($fittings_data);
		
		$this->load->model('tool/image');
		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . htmlspecialchars($result['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
				$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($result['image'] ?? '', ENT_QUOTES, 'UTF-8'), 50, 50);
			} else {
				$data['thumb'] = '';
			}

			$data['fittings'][] = array(
				'fittings_id'		=> $result['fittings_id'],
				'image'				=> $data['thumb'],
				'name'				=> $result['name'],
				'weight'			=> $result['weight'],
				'price'				=> $result['price'],
				'qty'				=> $result['qty'],
				'status'			=> $result['status'],
				'selected'			=> isset($this->request->post['selected']) && in_array($result['fittings_id'], $this->request->post['selected']),				
				'edit'				=> $this->url->link('masters/fittings|form', 'user_token=' . $this->session->data['user_token'] . '&fittings_id=' . $result['fittings_id'] . $url, true)
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
		
		
		$data['sort_name'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_status'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $fittings_total,
			'page'  => $page,
			'limit' => 10,
			'url'   => $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($fittings_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($fittings_total - 10)) ? $fittings_total : ((($page - 1) * 10) + 10), $fittings_total, ceil($fittings_total / 10));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('masters/fittings_list', $data);
	}

	public function form() {
		$this->load->model('masters/fittings');
		$data['text_form'] = !isset($this->request->get['fittings_id']) ? 'Add' : 'Edit';
		$this->document->setTitle('Extra Fittings');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['fittings'])) {
			$data['error_fittings'] = $this->error['fittings'];
		} else {
			$data['error_fittings'] = array();
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
			'text' => 'Extra Fittings',
			'href' => $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('masters/fittings|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('masters/fittings', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['fittings_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$fittings_info = $this->model_masters_fittings->getFitting($this->request->get['fittings_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
      		$data['name'] = $this->request->post['name'];
    	} elseif (!empty($fittings_info)) {
			$data['name'] = $fittings_info['name'];
		} else {	
      		$data['name'] = '';
    	}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
	  	} elseif (!empty($fittings_info)) {
			$data['image'] = $fittings_info['image'];
	  	} else {	
			$data['image'] = '';
	  	}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
	  	} elseif (!empty($fittings_info)) {
			$data['qty'] = $fittings_info['qty'];
	  	} else {	
			$data['qty'] = '';
	  	}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
	  	} elseif (!empty($fittings_info)) {
			$data['price'] = $fittings_info['price'];
	  	} else {	
			$data['price'] = 0;
	  	}

		if(isset($this->request->get['fittings_id'])){
			$data['fittings_id'] = $this->request->get['fittings_id'];
		}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $data['image'];
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
	  	} elseif (!empty($fittings_info)) {
			$data['height'] = $fittings_info['height'];
	  	} else {	
			$data['height'] = 0.0;
	  	}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
	  	} elseif (!empty($fittings_info)) {
			$data['weight'] = $fittings_info['weight'];
	  	} else {	
			$data['weight'] = 0.0;
	  	}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
	  	} elseif (!empty($fittings_info)) {
			$data['width'] = $fittings_info['width'];
	  	} else {	
			$data['width'] = 0.0;
	  	}

		if (isset($this->request->post['inner_circle'])) {
			$data['inner_circle'] = $this->request->post['inner_circle'];
	  	} elseif (!empty($fittings_info)) {
			$data['inner_circle'] = $fittings_info['inner_circle'];
	  	} else {	
			$data['inner_circle'] = 0.0;
	  	}

		if (isset($this->request->post['outer_circle'])) {
			$data['outer_circle'] = $this->request->post['outer_circle'];
	  	} elseif (!empty($fittings_info)) {
			$data['outer_circle'] = $fittings_info['outer_circle'];
	  	} else {	
			$data['outer_circle'] = 0.0;
	  	}

		if (isset($this->request->post['thickness'])) {
			$data['thickness'] = $this->request->post['thickness'];
	  	} elseif (!empty($fittings_info)) {
			$data['thickness'] = $fittings_info['thickness'];
	  	} else {	
			$data['thickness'] = 0.0;
	  	}
		
    	if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($fittings_info)) {
			$data['status'] = $fittings_info['status'];
		} else {	
      		$data['status'] = '';
    	}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('masters/fittings_form', $data));
	}
}