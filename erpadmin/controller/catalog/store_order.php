<?php
namespace Opencart\Admin\Controller\Catalog;
class StoreOrder extends \Opencart\System\Engine\Controller {
    public function index(){
        $data = [];
		$url = '';
		$this->load->language('catalog/store_order');
		$this->document->setTitle('Order');
		
		$this->load->model('catalog/store_order');

		// $data['add'] = $this->url->link('catalog/store_order|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['delete'] = $this->url->link('catalog/store_order|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['list'] = $this->getList();

		$data['save'] = $this->url->link('catalog/store_order|save', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/store_order', $data));
    }

    public function getOrder(){
		$this->load->model('catalog/store_order');

        $order_info = $this->model_catalog_store_order->getOrder($this->request->get['po_no']);

		$moulder = $this->model_catalog_store_order->getMoulder($order_info['moulder_id']);
		$client = $this->model_catalog_store_order->getClient($order_info['client_id']);

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(htmlspecialchars($order_info['image'] ?? '', ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $order_info['image'];
		}

		$data = array(
			'name' => (isset($moulder['name']) ? $moulder['name'] : ''),
			'client_name' => (isset($client['name']) ? $client['name'] : ''),
			'orders_id' => isset($order_info['o_orders_id']) ? $order_info['o_orders_id'] : 0,
			'store_order_id' => isset($order_info['store_order_id']) ? $order_info['store_order_id'] : 0,
			'thumb' => isset($data['thumb']) ? $data['thumb'] : '',
			'image' => isset($order_info['image']) ? $order_info['image'] : '',
			'qty' => isset($order_info['qty_rev']) ? $order_info['qty_rev'] : 0,
		);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
    }

	public function save() {
		$this->load->language('catalog/store_order');
		$json = [];
		// $val = 0;
		$this->document->setTitle('Order');

		$this->load->model('catalog/store_order');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['store_order_id']){
				$val = $this->model_catalog_store_order->addOrderDetails($this->request->post);
			}else{
				$val = $this->model_catalog_store_order->editOrderDetails($this->request->post['store_order_id'], $this->request->post);
			}
			
			if($val){
				$this->session->data['success'] = 'Successfully Saved';
				$json['success'] = 'Successfully Saved';
				$json['redirect'] = $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'], true);
			}else{
				$this->session->data['error'] = 'Something went wrong';
				$json['error'] = 'Something went wrong';
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}