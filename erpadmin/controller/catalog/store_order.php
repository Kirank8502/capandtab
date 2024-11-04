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

		$moulder = $this->model_catalog_store_order->getMoulders($order_info);

		$data = array(
			'name' => isset($moulder['name']) ? $moulder['name'] : '',
			'order_id' => isset($order_info['order_id']) ? $order_info['order_id'] : 0,
			'store_order_id' => isset($order_info['store_order_id']) ? $order_info['store_order_id'] : 0,
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
				$this->model_catalog_store_order->addOrderDetails($this->request->post);
			}else{
				$this->model_catalog_store_order->editOrderDetails($this->request->post['store_order_id'], $this->request->post);
			}

			// if($val){
			// 	$this->session->data['success'] = 'Successfully Saved';
			// 	$json['success'] = 'Successfully Saved';
			// 	$json['redirect'] = $this->url->link('catalog/store_order', 'user_token=' . $this->session->data['user_token'], true);
			// }else{
			// 	$this->session->data['error'] = 'Quantity Issue';
			// 	$json['error'] = 'Quantity Issue';
			// }
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}