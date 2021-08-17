<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {
  
	public function __construct()
    {
        parent::__construct();
        $params = array('server_key' => 'SB-Mid-server-lb-lFo5P17OIHyDvD11zVrbW', 'production' => false);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		$this->load->helper('url');	
    }
  
	public function index()
	{
    $data['pembayaran'] = $this->db->get('pembayaran')->result_array();
		$this->load->view('pembayaran', $data);
	}
  
  public function token()
  {
    // Required
    $transaction_details = array(
      'order_id'      => rand(),
      'gross_amount'  => (int) $this->input->post('harga'), // no decimal allowed for creditcard
    );

    // Optional
    $item1_details = array(
      'id' => 'a1',
      'price' => (int) $this->input->post('harga'),
      'quantity' => 1,
      'name' => "Pembayaran Jasa"
    );
    
    // Optional
    $item_details = array ($item1_details);

  // Optional
  $billing_address = array(
    'first_name'    => "Andri",
    'last_name'     => "Litani",
    'address'       => "Mangga 20",
    'city'          => "Jakarta",
    'postal_code'   => "16602",
    'phone'         => "081122334455",
    'country_code'  => 'IDN'
  );

  // Optional
  $shipping_address = array(
    'first_name'    => "Obet",
    'last_name'     => "Supriadi",
    'address'       => "Manggis 90",
    'city'          => "Jakarta",
    'postal_code'   => "16601",
    'phone'         => "08113366345",
    'country_code'  => 'IDN'
  );

  // Optional
  $customer_details = array(
    'first_name'    => "Andri",
    'last_name'     => "Litani",
    'email'         => "andri@litani.com",
    'phone'         => "081122334455",
    'billing_address'  => $billing_address,
    'shipping_address' => $shipping_address
  );

  // Data yang akan dikirim untuk request redirect_url.
      $credit_card['secure'] = true;
      //ser save_card true to enable oneclick or 2click
      //$credit_card['save_card'] = true;

      $time = time();
      $custom_expiry = array(
          'start_time'  => date("Y-m-d H:i:s O",$time),
          'unit'        => 'day', 
          'duration'    => 1
      );
      
      $transaction_data = array(
          'transaction_details'=> $transaction_details,
          'item_details'       => $item_details,
          'customer_details'   => $customer_details,
          'credit_card'        => $credit_card,
          'expiry'             => $custom_expiry
      );

  error_log(json_encode($transaction_data));
  $snapToken = $this->midtrans->getSnapToken($transaction_data);
  error_log($snapToken);
  echo $snapToken;
  }

  public function finish()
  {
    $result = json_decode($this->input->post('result_data'));
    
    $result = json_decode($this->input->post('result_data'));
    $this->db->insert('pembayaran', [
      'id_penawaran_jasa'   => $this->input->post('id_penawaran_jasa'),
      'status_code'         => $result->status_code,
      'transaction_id'      => $result->transaction_id,
      'order_id'            => $result->order_id,
      'gross_amount'        => $result->gross_amount,
      'payment_type'        => $result->payment_type,
      'transaction_time'    => $result->transaction_time,
      'transaction_status'  => $result->transaction_status
    ]);
    redirect();
  }
}
