<?php
namespace Opencart\Admin\Controller\Catalog;
use Dompdf\Dompdf;
use Dompdf\Options;
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
		$data['exportdata'] = $this->url->link('catalog/sale|exportData', 'user_token=' . $this->session->data['user_token'] . $url, true);
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
				// 'export'            => $this->url->link('catalog/sale|exportData', 'user_token=' . $this->session->data['user_token'] . '&sale_id=' . $result['sale_id'] . $url, true),
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

		// foreach($data['orders'] as $key => $value){
		// 	if($value['order_type'] == 1){
		// 		if(!empty($value['client_id'])){
		// 			$data['orders'][$key]['po_no'] = $value['po_no'] .'| '. $cli[$value['client_id']] .''.(!empty($value['product_id']) ? '| '.$product[$value['product_id']] : '');
		// 		}else{
		// 			$data['orders'][$key]['po_no'] = $value['po_no'] .'| '. $mol[$value['moulder_id']].'| '.(((!empty($value['acc_fitts_id']) && $value['acc_fitts_id'] > 0 && str_starts_with($value['acc_fitts_id'],'acc_')) ? $accessory[str_replace("acc_","",$value['acc_fitts_id'])] : ((!empty($value['acc_fitts_id']) && $value['acc_fitts_id'] > 0 && str_starts_with($value['acc_fitts_id'],'fitts_')) ? $fitts[str_replace("fitts_","",$value['acc_fitts_id'])] : 0)));
		// 		}
		// 	}
		// }

		$data['new_orders'] = [];  // Create a new array to store filtered orders

		foreach ($data['orders'] as $key => $value) {
		
			if ($value['order_type'] == 1) {
			
				$new_order = $value;  
			
			
				if (!empty($value['client_id']) && isset($cli[$value['client_id']])) {
					$new_order['po_no'] = $value['po_no'] 
						. ' | ' . $cli[$value['client_id']] 
						. (!empty($value['product_id']) && isset($product[$value['product_id']]) ? ' | ' . $product[$value['product_id']] : '');
				} 
			
				elseif (!empty($value['moulder_id']) && isset($mol[$value['moulder_id']])) {
					$accessory_name = 0;
				
					if (!empty($value['acc_fitts_id'])) {
						if (str_starts_with($value['acc_fitts_id'], 'acc_') && isset($accessory[str_replace('acc_', '', $value['acc_fitts_id'])])) {
							$accessory_name = $accessory[str_replace('acc_', '', $value['acc_fitts_id'])];
						} elseif (str_starts_with($value['acc_fitts_id'], 'fitts_') && isset($fitts[str_replace('fitts_', '', $value['acc_fitts_id'])])) {
							$accessory_name = $fitts[str_replace('fitts_', '', $value['acc_fitts_id'])];
						}
					}
					$new_order['po_no'] = $value['po_no'] 
						. ' | ' . $mol[$value['moulder_id']] 
						. ' | ' . $accessory_name;
				}
			
			
				$data['new_orders'][] = $new_order;
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

		if (isset($this->request->post['labor_cost'])) {
			$data['labor_cost'] = $this->request->post['labor_cost'];
	  	} elseif (!empty($sale_info)) {
			$data['labor_cost'] = $sale_info['labor_cost'];
	  	} else {
			$data['labor_cost'] = 0.00;
	  	}

		if (isset($this->request->post['packaging_cost'])) {
			$data['packaging_cost'] = $this->request->post['packaging_cost'];
	  	} elseif (!empty($sale_info)) {
			$data['packaging_cost'] = $sale_info['packaging_cost'];
	  	} else {
			$data['packaging_cost'] = 0.00;
	  	}

		if (isset($this->request->post['transportation_cost'])) {
			$data['transportation_cost'] = $this->request->post['transportation_cost'];
	  	} elseif (!empty($sale_info)) {
			$data['transportation_cost'] = $sale_info['transportation_cost'];
	  	} else {
			$data['transportation_cost'] = 0.00;
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

	public function getpodata() {
		$json = [];
		$fittingsArray = [];
		$this->load->model('catalog/sale');

		$data['products'] = $this->model_catalog_sale->getProducts();
		$data['powders'] = $this->model_catalog_sale->getPowders();
		$data['master_batchs'] = $this->model_catalog_sale->getMasterBatchs();
		$data['pigments'] = $this->model_catalog_sale->getPigments();
		$data['dies'] = $this->model_catalog_sale->getDies();
		$data['accessories'] = $this->model_catalog_sale->getAccessories();
		$data['fittings'] = $this->model_catalog_sale->getFittings();
		$data['clients'] = $this->model_catalog_sale->getClients();
		$data['colours'] = $this->model_catalog_sale->getColours();
		$data['moulders'] = $this->model_catalog_sale->getMoulders();
		
		if (isset($this->request->get['orders_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$json = $this->model_catalog_sale->getOrder($this->request->get['orders_id']);
		}

		if (!empty($json['fittings_id'])) {
			$fittings = explode(',', $json['fittings_id']);
			foreach ($fittings as $index => $fitting) {
				$fittingsArray[] = 'fitts_' . ($index + 1);
			}
		}		

		$json['datas'] = array(
			'prod_'.$json['product_id'],
			'pow_'.$json['powder_id'],
			'mb_'.$json['master_batch_id'],
			'pig_'.$json['pigment_id'],
			'dies_'.$json['die_id'],
			'acc_'.$json['accessories_id']
		);

		if(!empty($fittingsArray)){
			foreach ($fittingsArray as $fitting) {
				$json['datas'][] = $fitting;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function exportData($save_export = 0){
		require_once(DIR_SYSTEM . 'library/dompdf/vendor/autoload.php');
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', true);
		$options->set('isRemoteEnabled', true);
		$options->set('debug', true);
		$dompdf = new Dompdf($options);
		$this->load->model('catalog/sale');
		foreach ($this->request->post['selected'] as $sale_id) {
			$orders[] = $this->model_catalog_sale->getSale($sale_id);
		}
		$order_data = $this->model_catalog_sale->getOrders($orders[0]['orders_id']);
		$this->load->model('tool/image');

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
			$product[$value['product_id']] = [
				'name' => $value['name'],
				'price' => $value['total_price']
			];
		}
		foreach($accessories as $key_1 => $value_1) {
			$accessory[$value_1['accessories_id']] = [
				'name' => $value_1['name'],
				'price' => $value_1['price']
			];
		}
		foreach($fittings as $key_2 => $value_2) {
			$fitts[$value_2['fittings_id']] = [
				'name' => $value_2['name'],
				'price' => $value_2['price']
			];
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
		foreach ($orders as $order_key => $order_value) {
		$prod_ids = explode(",",$order_value['product_id']);
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
		}

			$total_price = 0;
			$client = !empty($orders[0]['vendor_id']) && str_starts_with($orders[0]['vendor_id'],'cli_') ? $this->model_catalog_sale->getClient(str_replace("cli_","",$orders[0]['vendor_id'])) : (str_starts_with($orders[0]['vendor_id'],'mol_') ? $this->model_catalog_sale->getMoulder(str_replace("mol_","",$orders[0]['vendor_id'])) : 'None');
$html = '
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice</title>
    <style>
		html, body{
			padding:0px;
			margin:0px;
		}
        p {
            margin: 0;
        }
        th, td {
            padding: 0.5rem;
            border: 1px solid #000;
            text-align: left;
        }
        h1 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table td, .table th {
            border: 1px solid #000;
            padding: 0.5rem;
        }
        .total-row {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            text-align: center;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .declaration {
            margin-top: 2rem;
        }
        .bank-details, .declaration, .signature-section {
            border: 1px solid #000;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
	<div style="padding:0 2rem;">
    <h1>Tax Invoice</h1>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 0.5rem;">
                <p><strong>ASHA ENTERPRISE</strong></p>
                <p>Shop No.4-B, Ghanshyam Dube Chwal,</p>
                <p>251, S.V. Road, Opp. Ambawadi</p>
                <p>Bus Stop, Dahisar (East), Mumbai 400068</p>
                <p>GSTIN/UIN: 27AGVPS5933R1ZI</p>
                <p>State Name: Maharashtra, Code: 27</p>
           <!-- <p>Contact: +91-9833233707/ 9897570772</p> -->
           <!-- <p>E-Mail: ashaenterprises1981@gmail.com</p> -->
            </td>
            <td style="width: 50%; padding: 0.5rem;">
                <p><strong>Invoice No.</strong>: '.($this->request->post['selected'][0]).'/'.date('Y').'-'.(date('y')+1).'</p>
                <p><strong>Dated</strong>: '.date('d-M-y', strtotime($orders[0]['sale_date'])).'</p>
           <!-- <p><strong>Delivery Note</strong>: </p> -->
           <!-- <p><strong>Mode/Terms of Payment</strong>: </p> -->
                <p><strong>Reference No. & Date</strong>: dt. '.date('d-M-y', strtotime($orders[0]['sale_date'])).'</p>
           <!-- <p><strong>Other References</strong>: </p> -->
                <p><strong>Buyer’s Order No.</strong>: MPPL/PO-39/'.date('y').'-'.(date('y')+1).'</p>
                <p><strong>Dated</strong>: '.date('d-M-y', strtotime($orders[0]['sale_date'])).'</p>
           <!-- <p><strong>Dispatch Doc No.</strong>: </p> -->
           <!-- <p><strong>Delivery Note Date</strong>: </p> -->
           <!-- <p><strong>Dispatched through</strong>: </p> -->
           <!-- <p><strong>Destination</strong>: </p> -->
            </td>
        </tr>
    </table>
		
	<table class="table">
	<tr>
	<td>
    <p><strong>Buyer (Bill to)</strong></p>
    <p>'.$client["name"].'</p>
    <p>'.$client["address"].'</p>
	</td>
	</tr>
	</table>

    <table class="table">
        <thead>
            <tr>
                <th>Sl</th>
                <th>Description of Goods</th>
                <th>Quantity</th>
                <th>Rate</th>
           <!-- <th>Disc. %</th> -->
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>';

        $total_price = 0;
        foreach($all_prod as $key => $value) {
            $total_price += isset($value['price']) ? $value['price'] : $orders[0]['amount'];
            $html .= '<tr>
                <td>'.($key + 1).'</td>
                <td>'.(isset($value['name']) ? $value['name'] : $value).'</td>
                <td>'.$orders[0]['qty'].' PCS</td>
                <td>'.$orders[0]['rate'].'</td>
           <!-- <td></td> -->
                <td>'.(isset($value['price']) ? $value['price'] * $orders[0]['qty'] : $orders[0]['amount']).'</td>
            </tr>';
        }
        
        $gst_price = ($total_price * $orders[0]['gst']) / 100;
        $after_gst_price = $total_price + $gst_price;

        $html .= '<tr class="total-row">
            <td colspan="5">Total</td>
            <td>'.$total_price.'</td>
        </tr>
        <tr class="total-row">
            <td colspan="5">GST @'.$orders[0]['gst'].'%</td>
            <td>'.$gst_price.'</td>
        </tr>
        <tr class="total-row">
            <td colspan="5">Total (including GST)</td>
            <td>'.$after_gst_price.'</td>
        </tr>
    </tbody>
    </table>
	
	<table class="table">
	<tr>
	<td>
    <p><strong>Amount Chargeable (in words)</strong></p>
    <p><strong>'.($this->convert_to_words_array(round($after_gst_price))).'</strong></p>
	</td>
	</tr>
	</table>

	<table class="table">
	<tr>
	<td>
    <p><strong>Date & Time</strong>: 6-May-24 at 11:56</p>
    <p><strong>Company\'s Bank Details</strong></p>
    <p><strong>Acc Holder\'s Name</strong>: Noel Enterprises</p>
	</td>
	<td>
    <p><strong>Bank Name</strong>: Noel Enterprises</p>
    <p><strong>A/C No</strong>: 5949409822</p>
    <p><strong>IFS Code</strong>: KKBK0001490</p>
    <p><strong>SWIFT Code</strong>: </p>
	</td>
	</tr>
	</table>

	<table class="table">
	<tr>
	<td>
    <p><strong>Declaration</strong></p>
    <p>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</p>
	</td>
	</tr>
	</table>

    <!-- <div class="signature-section">
        <p>Customer\'s Seal and Signature</p>
        <p>for Noel Enterprises</p>
    </div> -->

	<div class_2="border border-black p-2 mb-4" style="--tw-border-opacity: 1;border-width: 1px;border-color: rgb(0 0 0 / var(--tw-border-opacity, 1));border-style:solid;padding: 0.5rem;margin-bottom: 1rem;">
		<div style="display:flex;justify-content:space-between;margin-bottom:40px;">
        	<p>Customer\'s Seal and Signature</p>
        	<p class_2="text-right" style="text-align:right;">for Noel Enterprises</p>
		</div>
        <p class_2="text-right" style="text-align:right;">Authorised Signatory</p>
    </div>

    <p class="footer">This is a Computer Generated Invoice</p>
	</div>
</body>
</html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
		$pdfOutput = $dompdf->output();

		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Sale ' . $client['name'] . " PO " . $order_data[0]['po_no'] . '.pdf"');
		echo $pdfOutput;
		exit;
	}

	public function convert_to_words_array($number)
	{
		$words = array(
			'0' => 'Zero', '1' => 'One', '2' => 'Two',
			'3' => 'Three', '4' => 'Four', '5' => 'Five',
			'6' => 'Six', '7' => 'Seven', '8' => 'Eight',
			'9' => 'Nine', '10' => 'Ten', '11' => 'Eleven',
			'12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fourteen',
			'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
			'18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
			'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty', '60' => 'Sixty',
			'70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninety'
		);

		if ($number <= 20) {
			return $words[$number];
		}
		elseif ($number < 100) {
			return $words[10 * floor($number / 10)]
				. ($number % 10 > 0 ? ' ' . $words[$number % 10] : '');
		}
		else {
			$output = '';
			if ($number >= 1000000000) {
				$output .= $this->convert_to_words_array(floor($number / 1000000000))
					. ' Billion ';
				$number %= 1000000000;
			}
			if ($number >= 1000000) {
				$output .= $this->convert_to_words_array(floor($number / 1000000))
					. ' Million ';
				$number %= 1000000;
			}
			if ($number >= 1000) {
				$output .= $this->convert_to_words_array(floor($number / 1000))
					. ' Thousand ';
				$number %= 1000;
			}
			if ($number >= 100) {
				$output .= $this->convert_to_words_array(floor($number / 100))
					. ' Hundred ';
				$number %= 100;
			}
			if ($number > 0) {
				$output .= ($number <= 20) ? $words[$number] :
				$words[10 * floor($number / 10)] . ' '
					. ($number % 10 > 0 ? $words[$number % 10] : '');
			}
			return trim($output); 
		}
	}

	public function getRate(){
		// print_r($this->request->post['prod_ids']);
		$this->load->model('catalog/sale');

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
			$product[$value['product_id']] = [
				'name' => $value['name'],
				'price' => $value['total_price']
			];
		}
		foreach($accessories as $key_1 => $value_1) {
			$accessory[$value_1['accessories_id']] = [
				'name' => $value_1['name'],
				'price' => $value_1['price']
			];
		}
		foreach($fittings as $key_2 => $value_2) {
			$fitts[$value_2['fittings_id']] = [
				'name' => $value_2['name'],
				'price' => $value_2['price']
			];
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
		
		// $prod_ids = explode(",",$order_value['product_id']);
		foreach($this->request->post['prod_ids'] as $value){
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

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($all_prod));

	}

}