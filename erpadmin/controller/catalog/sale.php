<?php
namespace Opencart\Admin\Controller\Catalog;
// use Dompdf\Dompdf;
// use Dompdf\Options;
class Sale extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('catalog/sale');
		$this->document->setTitle('Sale');
		
		$this->load->model('catalog/sale');

		$data['add'] = $this->url->link('catalog/sale|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/sale|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['send'] = $this->url->link('catalog/sale|send', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/sale', $data));
	}

	public function save() {
		$this->load->language('catalog/sale');
		$json = [];
		$val = 0;
		$this->document->setTitle('Sale');

		$this->load->model('catalog/sale');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(!$this->request->post['sale_id']){
				$val = $this->model_catalog_sale->addSale($this->request->post);
			}else{
				$val = $this->model_catalog_sale->editSale($this->request->post['sale_id'], $this->request->post);
			}

			if($val){
				$json['success'] = 'Successfully Saved';
				$json['redirect'] = $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'], true);
			}else{
				$json['error'] = 'Quantity Issue';
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function delete() {
		$this->load->language('catalog/sale');

		$this->document->setTitle('Delete Sale');

		$this->load->model('catalog/sale');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $sale_id) {
				$this->model_catalog_sale->deleteSale($sale_id);
			}

			$this->session->data['success'] = 'Successfully Deleted';
			
			$json['success'] = 'Successfully Deleted';
			$json['redirect'] = $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'], true);
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			// $this->response->redirect($this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}
	
	public function import() {
		$this->language->load('catalog/sale');

    	$this->document->setTitle('Import Order');
		
		$this->load->model('catalog/sale');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			
			$this->model_catalog_sale->importSupplier($this->request->files);

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
			
			$this->response->redirect($this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		// $data['import'] = $this->url->link('catalog/sale|import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['export'] = $this->url->link('catalog/sale|export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['orders'] = array();

		$supplier_data = array(
			'filter_name'  => $filter_name,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		$sale_total = $this->model_catalog_sale->getTotalSales();
		
		$results = $this->model_catalog_sale->getSales($supplier_data);
		$products = $this->model_catalog_sale->getProducts();
		$accessories = $this->model_catalog_sale->getAccessories();
		$fittings = $this->model_catalog_sale->getFittings();
		$powders = $this->model_catalog_sale->getPowders();
		$master_batchs = $this->model_catalog_sale->getMasterBatchs();
		$pigments = $this->model_catalog_sale->getPigments();
		$dies = $this->model_catalog_sale->getDies();
		
		$moulders = $this->model_catalog_sale->getMoulders();
		$clients = $this->model_catalog_sale->getClients();

		foreach($products as $key => $value) {
			$product[$value['product_id']] = $value['name'];
		}
		foreach($accessories as $key_1 => $value_1) {
			$accessory[$value_1['accessories_id']] = $value_1['name'];
		}
		foreach($fittings as $key_2 => $value_2) {
			$fitts[$value_2['fittings_id']] = $value_2['name'];
		}
        foreach($powders as $key_3 => $value_3) {
			$pow[$value_3['powder_id']] = $value_3['name'];
		}
		foreach($master_batchs as $key_4 => $value_4) {
			$mb[$value_4['master_batch_id']] = $value_4['name'];
		}
		foreach($pigments as $key_5 => $value_5) {
			$pig[$value_5['pigment_id']] = $value_5['name'];
		}
        foreach($dies as $key_6 => $value_6) {
			$di[$value_6['die_id']] = $value_6['name'];
		}

		foreach($moulders as $key_7 => $value_7) {
			$mol[$value_7['moulder_id']] = $value_7['name'];
		}
		foreach($clients as $key_8 => $value_8) {
			$cli[$value_8['client_id']] = $value_8['name'];
		}
		
		foreach ($results as $result) {
			$all_prod = [];
			$date = strtotime($result['sale_date']);
			$po_date = strtotime($result['date_added']);
            $prod_ids = explode(",",$result['product_id']);
            foreach($prod_ids as $value){
                if(str_starts_with($value,'acc_')){
                    $all_prod[] = $accessory[str_replace("acc_","",$value)];
                }elseif(str_starts_with($value,'fitts_')){
                    $all_prod[] = $fitts[str_replace("fitts_","",$value)];
                }elseif(str_starts_with($value,'prod_')){
                    $all_prod[] = $product[str_replace("prod_","",$value)];
                }elseif(str_starts_with($value,'pow_')){
                    $all_prod[] = $pow[str_replace("pow_","",$value)];
                }elseif(str_starts_with($value,'mb_')){
                    $all_prod[] = $mb[str_replace("mb_","",$value)];
                }elseif(str_starts_with($value,'pig_')){
                    $all_prod[] = $pig[str_replace("pig_","",$value)];
                }elseif(str_starts_with($value,'dies_')){
                    $all_prod[] = $di[str_replace("dies_","",$value)];
                }
            }

			$data['sales'][] = array(
				'sale_id'		=> $result['sale_id'],
				'sale_date'		=> date("d-m-Y", $date),
				'product_id'        => implode(',',$all_prod),
				'vendor_id'         => !empty($result['vendor_id']) && str_starts_with($result['vendor_id'],'cli_') ? $cli[str_replace("cli_","",$result['vendor_id'])] : (str_starts_with($result['vendor_id'],'cli_') ? $mol[str_replace("mol_","",$result['vendor_id'])] : 'None'),
				'qty'				=> $result['qty'],
				'rate'				=> $result['rate'],
				'amount'		    => $result['amount'],
				'file'		        => HTTP_CATALOG .'crm_storage/download/'. $result['file'],
				'edit'				=> $this->url->link('catalog/sale|form', 'user_token=' . $this->session->data['user_token'] . '&sale_id=' . $result['sale_id'] . $url, true),
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
		
		
		$data['sort_name'] = $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_code'] = $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $sale_total,
			'page'  => $page,
			'limit' => 10,
			'url'   => $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($sale_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($sale_total - 10)) ? $sale_total : ((($page - 1) * 10) + 10), $sale_total, ceil($sale_total / 10));

		$data['filter_name'] = $filter_name;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('catalog/sale_list', $data);
	}

	public function form() {
		$this->load->model('catalog/sale');
		$data['text_form'] = !isset($this->request->get['sale_id']) ? 'Add' : 'Edit';
		$data['edit_data'] = !isset($this->request->get['sale_id']) ? False : True;
		$data['view_mode'] = !isset($this->request->get['view_mode']) ? False : True;
		$this->document->setTitle('Sale');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['order'])) {
			$data['error_order'] = $this->error['order'];
		} else {
			$data['error_order'] = array();
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
			'text' => 'Sale',
			'href' => $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('catalog/sale|save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['back'] = $this->url->link('catalog/sale', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['orders'] = $this->model_catalog_sale->getOrders();
		$data['products'] = $this->model_catalog_sale->getProducts();
		$data['clients'] = $this->model_catalog_sale->getClients();
		$data['powders'] = $this->model_catalog_sale->getPowders();
		$data['colours'] = $this->model_catalog_sale->getColours();
		$data['moulders'] = $this->model_catalog_sale->getMoulders();
		$data['master_batchs'] = $this->model_catalog_sale->getMasterBatchs();
		$data['pigments'] = $this->model_catalog_sale->getPigments();
		$data['dies'] = $this->model_catalog_sale->getDies();
		$data['accessories'] = $this->model_catalog_sale->getAccessories();
		$data['fittings'] = $this->model_catalog_sale->getFittings();
		$data['datas'] = array_merge($data['products'],$data['powders'],$data['master_batchs'],$data['pigments'],$data['dies'],$data['accessories'],$data['fittings']);
		foreach ($data['datas'] as &$item) {
			if(isset($item['accessories_id'])){
				$item['product_ids'] = 'acc_' . $item['accessories_id'];
			}elseif(isset($item['fittings_id'])){
				$item['product_ids'] = 'fitts_' . $item['fittings_id'];
			}elseif(isset($item['product_id'])){
				$item['product_ids'] = 'prod_' . $item['product_id'];
            }elseif(isset($item['powder_id'])){
				$item['product_ids'] = 'pow_' . $item['powder_id'];
            }elseif(isset($item['master_batch_id'])){
				$item['product_ids'] = 'mb_' . $item['master_batch_id'];
            }elseif(isset($item['pigment_id'])){
				$item['product_ids'] = 'pig_' . $item['pigment_id'];
            }elseif(isset($item['die_id'])){
				$item['product_ids'] = 'dies_' . $item['die_id'];
            }
		}

		foreach($data['moulders'] as $key_7 => $value_7) {
			$mol[$value_7['moulder_id']] = $value_7['name'];
		}
		foreach($data['clients'] as $key_8 => $value_8) {
			$cli[$value_8['client_id']] = $value_8['name'];
		}

		foreach($data['products'] as $key => $value) {
			$product[$value['product_id']] = $value['name'];
		}

		foreach($data['accessories'] as $key_1 => $value_1) {
			$accessory[$value_1['accessories_id']] = $value_1['name'];
		}

		foreach($data['fittings'] as $key_2 => $value_2) {
			$fitts[$value_2['fittings_id']] = $value_2['name'];
		}

		foreach($data['orders'] as $key => $value){
			if(!empty($value['client_id'])){
				$data['orders'][$key]['po_no'] = $value['po_no'] .'| '. $cli[$value['client_id']] .''.(!empty($value['product_id']) ? '| '.$product[$value['product_id']] : '');
			}else{
				$data['orders'][$key]['po_no'] = $value['po_no'] .'| '. $mol[$value['moulder_id']].'| '.(((!empty($value['acc_fitts_id']) && $value['acc_fitts_id'] > 0 && str_starts_with($value['acc_fitts_id'],'acc_')) ? $accessory[str_replace("acc_","",$value['acc_fitts_id'])] : ((!empty($value['acc_fitts_id']) && $value['acc_fitts_id'] > 0 && str_starts_with($value['acc_fitts_id'],'fitts_')) ? $fitts[str_replace("fitts_","",$value['acc_fitts_id'])] : 0)));
			}
		}

        $data['vendors'] = array_merge($data['clients'],$data['moulders']);
		foreach ($data['vendors'] as &$item) {
			if(isset($item['client_id'])){
				$item['vendor_id'] = 'cli_' . $item['client_id'];
			}elseif(isset($item['moulder_id'])){
				$item['vendor_id'] = 'mol_' . $item['moulder_id'];
			}
		}

		if (isset($this->request->get['sale_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$sale_info = $this->model_catalog_sale->getSale($this->request->get['sale_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if(isset($this->request->get['sale_id'])){
			$data['sale_id'] = $this->request->get['sale_id'];
		}

		if (isset($this->request->post['qty'])) {
			$data['qty'] = $this->request->post['qty'];
	  	} elseif (!empty($sale_info)) {
			$data['qty'] = $sale_info['qty'];
	  	} else {
			$data['qty'] = 0;
	  	}

		if (isset($this->request->post['payment_date'])) {
			$data['payment_date'] = $this->request->post['payment_date'];
	  	} elseif (!empty($sale_info)) {
			$data['payment_date'] = $sale_info['payment_date'];
	  	} else {
			$data['payment_date'] = 0;
	  	}

        if (isset($this->request->post['file'])) {
			$data['file'] = $this->request->post['file'];
	  	} elseif (!empty($sale_info)) {
			$data['file'] = $sale_info['file'];
	  	} else {
			$data['file'] = '';
	  	}

        if (isset($this->request->post['rate'])) {
			$data['rate'] = $this->request->post['rate'];
	  	} elseif (!empty($sale_info)) {
			$data['rate'] = $sale_info['rate'];
	  	} else {
			$data['rate'] = 0.00;
	  	}

        if (isset($this->request->post['amount'])) {
			$data['amount'] = $this->request->post['amount'];
	  	} elseif (!empty($sale_info)) {
			$data['amount'] = $sale_info['amount'];
	  	} else {
			$data['amount'] = 0.00;
	  	}

        if (isset($this->request->post['gst'])) {
			$data['gst'] = $this->request->post['gst'];
	  	} elseif (!empty($sale_info)) {
			$data['gst'] = $sale_info['gst'];
	  	} else {
			$data['gst'] = 0;
	  	}

		if (isset($this->request->post['gst_status'])) {
			$data['gst_status'] = $this->request->post['gst_status'];
	  	} elseif (!empty($sale_info)) {
			$data['gst_status'] = $sale_info['gst_status'];
	  	} else {
			$data['gst_status'] = 0;
	  	}

		if (isset($this->request->post['orders_id'])) {
			$data['orders_id'] = $this->request->post['orders_id'];
	  	} elseif (!empty($sale_info)) {
			$data['orders_id'] = $sale_info['orders_id'];
	  	} else {
			$data['orders_id'] = 0;
	  	}

		if (isset($this->request->post['vendor_id'])) {
			$data['vendor_id'] = $this->request->post['vendor_id'];
	  	} elseif (!empty($sale_info)) {
			$data['vendor_id'] = $sale_info['vendor_id'];
	  	} else {
			$data['vendor_id'] = '';
	  	}

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
	  	} elseif (!empty($sale_info)) {
			$data['product_id'] = $sale_info['product_id'];
	  	} else {
			$data['product_id'] = '';
	  	}

		if (isset($this->request->post['sale_date'])) {
			$data['sale_date'] = $this->request->post['sale_date'];
	  	} elseif (!empty($sale_info)) {
			$data['sale_date'] = $sale_info['sale_date'];
	  	} else {
			$data['sale_date'] = '';
	  	}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/sale_form', $data));
	}

    public function upload(): void {
		$json = [];

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'catalog/download')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (empty($this->request->files['file']['name']) || !is_file($this->request->files['file']['tmp_name'])) {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
			// if ((oc_strlen($filename) < 3) || (oc_strlen($filename) > 128)) {
			// 	$json['error'] = $this->language->get('error_filename');
			// }

			// Allowed file extension types
			$allowed = [];

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_file_type');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		}

		if (!$json) {
			$file = $filename;

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);

			$json['filename'] = $file;

			$json['success'] = 'File uploaded successfully';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}