<?php 
require_once APPPATH . 'third_party/razorpay/Razorpay.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class Razorpay 
{
    private $__razorpay;
    function __construct($config = array())
    {
        $this->CI = &get_instance();
        $this->__razorpay = new Api($config['key'], $config['secret']);
    }

    function create_order($order = array())
    {
        $order['currency']  = 'INR';
        $razorpay_order     = $this->__razorpay->order->create($order);

        $this->CI->session->set_userdata(array(
            'razorpay_order_id' => $razorpay_order['id']
        ));

        $return = array(
            'order_id' => $razorpay_order['id']
        );

        return $return;
    }

    function verify_payment_signature($payload = array())
    {
        $response            = array();
        $response['success'] = true;
        $response['message'] = 'Payment Failed';
        $response['payment'] = array();

        if (isset($payload['razorpay_payment_id']))
        {
            try
            {
                $attributes = array(
                    'razorpay_order_id' => $payload['razorpay_order_id'],
                    'razorpay_payment_id' => $payload['razorpay_payment_id'],
                    'razorpay_signature' => $payload['razorpay_signature']
                );

                $this->__razorpay->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e)
            {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        }

        if ($response['success'] === true)
        {
            $response['message'] = 'Payment Successfull';
            $response['payment'] = $this->__razorpay->payment->fetch($payload['razorpay_payment_id']);
        }
        return $response;
    }
}
?>