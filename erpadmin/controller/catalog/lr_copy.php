<?php
namespace Opencart\Admin\Controller\Catalog;
class LRCopy extends \Opencart\System\Engine\Controller {
	private $error = array();
	public function index():void {
		$data = [];
		$url = '';
		$this->load->language('catalog/lr_copy');
		$this->document->setTitle('LR Copy');
		
		// $this->load->model('catalog/lr_copy');

		$data['add'] = $this->url->link('catalog/lr_copy|form', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/lr_copy|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['send'] = $this->url->link('catalog/lr_copy|send', 'user_token=' . $this->session->data['user_token'] . $url, true);
		// $data['list'] = $this->getList();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('catalog/lr_copy', $data));
	}

    public function send(){
        $data = [];
        if ($this->config->get('config_mail_engine')) {
            $mail_option = [
                'parameter'     => $this->config->get('config_mail_parameter'),
                'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
                'smtp_username' => $this->config->get('config_mail_smtp_username'),
                'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
                'smtp_port'     => $this->config->get('config_mail_smtp_port'),
                'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
            ];

            $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
            $mail->setTo('kirankidecha3@gmail.com');
            $mail->setFrom('kirankidecha85@gmail.com');
            $mail->setSender('Your Store');
            $mail->setSubject('Testing');
            $mail->setHtml($this->load->view('catalog/lr_copy_mail', $data));

            // $file_path = DIR_DOWNLOAD . 'your_file.pdf'; // Ensure the file path is correct
            // if (file_exists($file_path)) {
            //     $mail->addAttachment($file_path);
            // }
            $mail->send();
        }
    }
}