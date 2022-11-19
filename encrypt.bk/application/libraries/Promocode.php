<?php
 class Promocode
 {
     public function __construct()
     {
         $this->CI = &get_instance();
         $this->CI->load->model(array('Promocode_model'));
         $this->__response   = array();
     }

    /**
     *
     * This will add a header index on response array.
     * @param    array  $headers
     * @return   no return
     *
     */
     private function set_header( $headers = array() )
     {
         if(!isset($this->__response['header']))
         {
             $this->__response['header'] = array( 'error' => false, 'message' => '' );
         }
         if(!empty($headers))
         {
             foreach($headers as $header_key => $header)
             {
                 $this->__response['header'][$header_key] = $header;
             }
         }
     }
 
    /**
     *
     * This will add a body index on response array.
     * @param    array  $body
     * @return   no return
     *
     */
     private function set_body( $body = array() )
     {
         if(!isset($this->__response['body']))
         {
             $this->__response['body'] = array();
         }
         if(!empty($body))
         {
             foreach($body as $body_key => $body_value)
             {
                 $this->__response['body'][$body_key] = $body_value;
             }
         }
     }

    /**
     *
     * This will return response array.
     * @param    array  $body
     * @return   array 
     *
     */
     private function set_response()
     {
        return $this->__response; 
        //exit;
     }
     
    /**
     *
     * This will add or update the created and generated promocodes.
     * @param    array  $param
     * @return   array 
     *
     */
     function save_promocode( $param = array() )
     { 
        //input declaration
        $promocode_creation_type   = isset($param['promocode_creation_type'])?$param['promocode_creation_type']:0;
        $promocode_name            = isset($param['promocode_name'])?$param['promocode_name']:'';
        $promocode_description     = isset($param['promocode_description'])?$param['promocode_description']:'';
        $promocode_user_permission = '0';
        $promocode_user_limit      = '1';
        //$promocode_creation_type : 0 - Create Promocode; 1- Generate Promocode
        if($promocode_creation_type == 0)
        {
            $promocode_user_permission   = isset($param['promocode_user_permission'])?$param['promocode_user_permission']:'';
            //$promocode_user_permission: 0 - Open to all; 1 - Open to N users
            $promocode_user_limit  = false;
            if($promocode_user_permission == 1)
            {
                $promocode_user_limit    = isset($param['promocode_user_limit'])?$param['promocode_user_limit']:0;
            }
        }
        //$promocode_discount_type : 0 - Percentage Discount; 1 - Flat Discount
        $promocode_discount_type    = isset($param['promocode_discount_type'])?$param['promocode_discount_type']:'';
        $promocode_discount_rate    = isset($param['promocode_discount_rate'])?$param['promocode_discount_rate']:0;
        $promocode_count            = isset($param['promocode_count'])?$param['promocode_count']:0;
        $promocode_expiry_date      = isset($param['promocode_expiry_date'])?$param['promocode_expiry_date']:'';
        $promocode_id               = isset($param['promocode_id'])?$param['promocode_id']:false;
        $promocode_created_date     = isset($param['promocode_created_date'])?$param['promocode_created_date']:date('Y-m-d H:i:s');
        //input declaration ends here
        $header                     = array();
        $body                       = array();
        $error_message              = array();
        $error_count                = 0;

        if($promocode_name == '')
        {
            $error_count            = 1;
            $error_message[]        = 'Discount Coupon Name required.';
        }

        // if($promocode_description == '')
        // {
        //     $error_count            = 1;
        //     $error_message[]        = 'Discount Coupon Description required.';
        // }

        if($promocode_creation_type == 0)
        {
            if($promocode_user_permission == '')
            {
                $error_count        = 1;
                $error_message[]    = 'Discount Coupon User Permission required.';
            }
            
            if($promocode_user_permission == 1)
            {
                if($promocode_user_limit <= 0)
                {
                    $error_count     = 1;
                    $error_message[] = 'Discount Coupon User Limit required.';
                }
            }
        }

        if($promocode_discount_type == '')
        {
            $error_count            = 1;
            $error_message[]        = 'Discount Type required.';
        }

        if($promocode_discount_type == 0)
        {
            if($promocode_discount_rate <= 0)
            {
                $error_count            = 1;
                $error_message[]        = 'Discount Coupon Percentage Rate required.';
            }
            if($promocode_discount_rate > 100)
            {
                $error_count            = 1;
                $error_message[]        = 'Discount Coupon Percentage Rate should be valid.';
            }
        }

        if($promocode_discount_type == 1)
        {
            if($promocode_discount_rate <= 0)
            {
                $error_count            = 1;
                $error_message[]        = 'Discount Rate required.';
            }
        }

        if($promocode_creation_type == 1 && $promocode_id == false)
        {
            if($promocode_count == 0)
            {
                $error_count        = 1;
                $error_message[]    = 'Discount Coupon Count required.';
            }
            $promocode_length       = strlen($promocode_name) + strlen($promocode_count);
            if($promocode_length > 10)
            {
                $error_count        = 1;
                $error_message[]    = 'Generated Discount Coupons Should be Maximum Ten Characters.';
            }
        }

        if($promocode_expiry_date == '')
        {
            $error_count            = 1;
            $error_message[]        = 'Discount Coupon Expiry Date required.';
        }

        if($error_count > 0)
        {
            $header['success']      = false;
            $header['message']      = $error_message;
            $this->set_header($header);
            return $this->set_response();
        }
        
        if($promocode_count > 0)
        {
            $generate_param         = array(
                                              'promocode_name'             =>  $promocode_name,
                                              'promocode_description'      =>  $promocode_description,
                                              'promocode_user_permission'  =>  $promocode_user_permission,
                                              'promocode_user_limit'       =>  $promocode_user_limit,
                                              'promocode_discount_type'    =>  $promocode_discount_type,
                                              'promocode_discount_rate'    =>  floor($promocode_discount_rate*100)/100,
                                              'promocode_count'            =>  $promocode_count,
                                              'promocode_expiry_date'      =>  $promocode_expiry_date
                                           );
            return $this->generate_promocodes($generate_param);
        }
        else
        {
        if($promocode_id)
        {
            $header['success']      = true;
            $header['message']      = 'Discount Coupon Updated.';
        }
        else
        {
            $header['success']      = true;
            $header['message']      = 'Discount Coupon Created.';
        }

        $promocode                  = array(
                                                'id' => $promocode_id, 
                                                'pc_type' => $promocode_creation_type,
                                                'pc_promo_code_name' => $promocode_name,
                                                'pc_description' => $promocode_description,
                                                'pc_user_permission' => $promocode_user_permission,
                                                'pc_user_limit' => $promocode_user_limit,
                                                'pc_discount_type' => $promocode_discount_type,
                                                'pc_discount_rate' => floor($promocode_discount_rate*100)/100,
                                                'pc_status' => '1',
                                                'pc_user_count' => '0',
                                                'pc_user_detail' => '',
                                                'pc_account_id'  => config_item('id'),
                                                'pc_expiry_date' => date('Y-m-d H:i:s',strtotime($promocode_expiry_date)),
                                                'pc_created_date' => $promocode_created_date,
                                                'pc_updated_date' => date('Y-m-d H:i:s')
                                            );                                 
                                            
        $promocode['id']            = $this->CI->Promocode_model->save_promocode($promocode);
        if(!$promocode['id'])
        {
            $header['success']      = false;
            $header['message']      = 'Discount Coupon Name already exists.';
        }
        $header['type']    = 'Created';
        $body['promocode'] = $promocode;
        $this->set_header($header);
        $this->set_body($body);
        return $this->set_response();
        }
     }
     
    /**
     *
     * This will return the promocode depends upon the parameters.
     * @param    array  $param
     * @return   array 
     *
     */
     function promocode( $param = array() )
     {
        $header                     = array(); 
        $body                       = array();
        $promocode                  = $this->CI->Promocode_model->promocode($param);
         if(!empty($promocode))
         {
             $header['success']     = true;
             $header['message']     = 'Discount Coupon Details Fetched Successfully.';
             $body['promocode']     = $promocode;
         }
         else
         {
             $header['success']     = false;
             $header['message']     = 'Error to Fetch Discount Coupon Details.';
             $body['promocode']     = array();
         }
         $this->set_header($header);
         $this->set_body($body);
         return $this->set_response();
     }
     
    /**
     *
     * This will return all the promocodes depends upon the parameters.
     * @param    array  $param
     * @return   array 
     *
     */
     function promocodes( $param = array() )
     {
        $header                      = array();
        $body                        = array();
        $promocodes                  = $this->CI->Promocode_model->promocodes($param);
        if( $promocodes )
        {
            $header['success']       = true;
            $header['message']       = 'Discount Coupons Fetched Successfully.';
        }
        else
        {
            $header['success']       = false;
            $header['message']       = 'Error to Fetch Discount Coupons.';
        }
        $body['promocodes']          = $promocodes;
        $this->set_header($header);
        $this->set_body($body);
        return $this->set_response();
     }
     
    /**
     *
     * This will delete the promocode.
     * @param    int  $promocode_id
     * @return   response message
     *
     */
     function delete_promocode( $promocode_id )
     {
         $header                     = array();
         $promocode_delete           = $this->CI->Promocode_model->delete_promocode($promocode_id);
         if($promocode_delete)
         {
             $header['success']      = true;
             $header['message']      = 'Discount Coupon Deleted Successfully.';
         }
         else
         {
             $header['success']      = false;
             $header['message']      = 'Error to Delete Discount Coupon.';
         }
         $this->set_header($header);
         return $this->set_response();
     }
     
    /**
     *
     * This will return the generated promocodes depends upon the parameters.
     * @param    array  $param
     * @return   array 
     *
     */
     function generate_promocodes( $param = array() )
     {   
         $header                     = array();
         $body                       = array();
         $generated_promocodes       = array();
         $promocode_name             = $param['promocode_name'];
         $promocode_description      = $param['promocode_description'];
         $promocode_user_permission  = $param['promocode_user_permission'];
         $promocode_user_limit       = $param['promocode_user_limit'];
         $promocode_discount_type    = $param['promocode_discount_type'];
         $promocode_discount_rate    = $param['promocode_discount_rate'];
         $promocode_count            = $param['promocode_count'];
         $promocode_expiry_date      = $param['promocode_expiry_date'];

         for($i = 1; $i <= $promocode_count; $i++)
         {
             $generated_promocodes[] = $promocode_name.$i;
         }

         $unique_promocodes          = $this->unique_promocodes($generated_promocodes);
         $promocodes                 = array();
         foreach($unique_promocodes as $unique_promocode)
         {
            $invalid_unique_promocode = array(); 
            if(strlen($unique_promocode) > 10)
             {
                 $invalid_unique_promocode[] = $unique_promocode;
                 continue;
             }

            $promocodes[]            = array(
                                                'pc_type'               => '1',
                                                'pc_promo_code_name'    => $unique_promocode,
                                                'pc_description'        => $promocode_description,
                                                'pc_user_permission'    => $promocode_user_permission,
                                                'pc_user_limit'         => $promocode_user_limit,
                                                'pc_discount_type'      => $promocode_discount_type,
                                                'pc_discount_rate'      => $promocode_discount_rate,
                                                'pc_status'             => '1',
                                                'pc_user_count'         => '0',
                                                'pc_user_detail'        => '',
                                                'pc_account_id'         => config_item('id'),
                                                'pc_expiry_date'        => date('Y-m-d H:i:s',strtotime($promocode_expiry_date)),
                                                'pc_created_date'       => date('Y-m-d H:i:s'),
                                                'pc_updated_date'       => date('Y-m-d H:i:s')
                                            );
         }
         $save_promocodes            = $this->CI->Promocode_model->save_generated_promocodes($promocodes);
         $header['success']          = false;
         $header['message']          = 'Error to Save Generated Discount Coupons';
         $header['type']             = 'Generated';
         if($save_promocodes || !empty($invalid_unique_promocode))
         {
             if(!empty($invalid_unique_promocode))
             {
                $header['success']   = true;
                $header['message']   = 'All Generated Discount Coupons are not Saved because it exceeds the Maximum Characters.';
             }
             else
             {
                $header['success']   = true;
                $header['message']   = 'Generated Discount Coupons Saved Successfully';
             }
         }
         $this->set_header($header);
         return $this->set_response();
     }
     
     /**
     *
     * This will create and return the unique promocodes from the generated promocodes.
     * @param    array  $duplicate_promocode_names
     * @return   array 
     *
     */
     private function unique_promocodes( $duplicate_promocode_names, $unique_promocode_names = array() )
     {
        $params                             = array(
                                                    'promocode_names'   => $duplicate_promocode_names
                                                );
        $existing_promocode_names           = $this->CI->Promocode_model->promocodes($params);
        $existing_promocodes_name_queue     = array();

        if(!empty($existing_promocode_names))
        {
            foreach($existing_promocode_names as $existing_promocode_name)
            {
                $existing_promocodes_name_queue[] = $existing_promocode_name['pc_promo_code_name'];
            }            
        }
        
        if(!empty($duplicate_promocode_names))
        {
            $i = 1;
            $duplicate_promocode_name_to_filter = array();
            foreach($duplicate_promocode_names as $promocode_name)
            {
                if( in_array($promocode_name, $existing_promocodes_name_queue ))
                {
                    $duplicate_promocode_name_to_filter[] = $promocode_name.$i;
                    $i++;
                }
                else
                {
                    $unique_promocode_names[] = $promocode_name;
                }
            }            
        }

        if(empty($duplicate_promocode_name_to_filter))
        {
            return $unique_promocode_names;
        }
        else
        {
            return $this->unique_promocodes( $duplicate_promocode_name_to_filter, $unique_promocode_names);
        }
     }
     
     /**
     *
     * This will check the promocode is valid or not.
     * @param    array  $param
     * @return   response message
     *
     */
     public function check_valid_promocode( $param = array() )
     {
        $header                     = array();
        $body                       = array();
        $promocode_name             = $param['promocode'];
        $user_details               = $param['user_details'];
        $promocode_reports          = array(
                                                'pc_promo_code_name'   => $promocode_name,
                                                'pc_user_detail'       => $user_details
                                            );
        $valid_promocode             = $this->CI->Promocode_model->check_valid_promocode($promocode_reports);
        $header['success']           = false;
        $header['message']           = 'Error to check the Discount Coupon';
        $body['promocode']           = array();
        switch($valid_promocode)
        {
            case 'expired':
                $header['success']   = false;
                $header['message']   = 'Discount Code Expired';
                $body['promocode']   = array();
            break;
            case 'deactivated':
                $header['success']   = false;
                $header['message']   = 'Discount Coupon Deactivated';
                $body['promocode']   = array();
            break;
            case 'limit_exceeded':
                $header['success']   = false;
                $header['message']   = 'Discount Coupon usage limit exceeded';
                $body['promocode']   = array();
            break;
            case 'already_used':
                $header['success']   = false;
                $header['message']   = 'You have already used this Discount Code';
                $body['promocode']   = array();
            break;
            case 'invalid':
                $header['success']   = false;
                $header['message']   = 'Discount Code Invalid';
                $body['promocode']   = array();
            break;
            default:
                $header['success']   = true;
                $header['message']   = 'This is valid Discount Coupon.';
                $body['promocode']   = $valid_promocode;
            break;
        }
        $this->set_header($header);
        $this->set_body($body);
        return $this->set_response();
     }
     
     /**
     *
     * This will keep records of the promocodes used by the students.
     * @param    array  $param
     * @return   array
     *
     */
     function record_promocode_usage( $param = array() )
     {
         $header                     = array();
         $body                       = array();
         $promocode_name             = $param['promocode'];
         $user_details               = $param['user_details'];
         $promocode_reports          = array(
                                                'pc_promo_code_name'   => $promocode_name,
                                                'pc_user_detail'       => $user_details
                                            );
        $promocode_usage_report      = $this->CI->Promocode_model->record_promocode_usage($promocode_reports);
        $header['success']           = false;
        $header['message']           = 'Error to apply Discount Coupon';
        $body['promocode']           = array();
        switch($promocode_usage_report)
        {
            case 'expired':
                $header['success']   = false;
                $header['message']   = 'Discount Code Expired';
                $body['promocode']   = array();
            break;
            case 'deactivated':
                $header['success']   = false;
                $header['message']   = 'Discount Coupon Deactivated';
                $body['promocode']   = array();
            break;
            case 'limit_exceeded':
                $header['success']   = false;
                $header['message']   = 'Discount Coupon usage limit exceeded';
                $body['promocode']   = array();
            break;
            case 'already_used':
                $header['success']   = false;
                $header['message']   = 'You have already used this Discount Code';
                $body['promocode']   = array();
            break;
            case 'invalid':
                $header['success']   = false;
                $header['message']   = 'Discount Code Invalid';
                $body['promocode']   = array();
            break;
            default:
                $header['success']   = true;
                $header['message']   = 'Discount Coupon applied Successfully';
                $body['promocode']   = $promocode_usage_report;
            break;
        }
        $this->set_header($header);
        $this->set_body($body);
        return $this->set_response();
     }
     
     /**
     *
     * This will change the status of the promocode.
     * @param    array $promocode_status_params
     * @return   response message
     *
     */
     function change_promocode_status( $promocode_status_params = array() )
     {
        $header                     = array();
        $promocode_id               = $promocode_status_params['promocode_id'];
        $status                     = $promocode_status_params['status'];
        if(!$promocode_id)
        {
            $header['success']      = false;
            $header['message']      = 'Discount Coupon id missing';
            $this->set_header($header);
            return $this->set_response();
        }
        $promocode_status_params     = array(
                                                'id'              => $promocode_id,
                                                'pc_status'       => $status,
                                                'pc_updated_date' => date('Y-m-d H:i:s')
                                            );
        $promocode_status            = $this->CI->Promocode_model->change_promocode_status($promocode_status_params);

        $header['success']           = false;
        $header['message']           = 'Error to change Discount Coupon Status.';
        if($promocode_status)
        {
            $header['success']       = true;
            $header['message']       = 'Discount Coupon Status Updated Successfully.';   
        }
        $this->set_header($header);
        return $this->set_response();
     }
     
     /**
     *
     * This will change the status of the bulk promocodes.
     * @param    array $promocode_status_params
     * @return   response message
     *
     */
     function change_promocode_status_bulk( $promocode_status_params = array() )
     {
        $header                     = array();
        $promocode_ids              = $promocode_status_params['promocode_ids'];
        $status                     = $promocode_status_params['status'];
        if(empty($promocode_ids))
        {
            $header['success']      = false;
            $header['message']      = 'Discount Coupon id missing';
            $this->set_header($header);
            return $this->set_response();
        }
        $promocode_status_params     = array();
        foreach($promocode_ids as $promocode_id)
        {
            $promocode               = array();
            $promocode['id']         = $promocode_id;
            $promocode['pc_status']  = $status;
            $promocode['pc_updated_date']  = date('Y-m-d H:i:s');
            $promocode_status_params[] = $promocode; 
        }
        $promocode_status            = $this->CI->Promocode_model->change_promocode_status_bulk($promocode_status_params);

        $header['success']           = false;
        $header['message']           = 'Error to change Discount Coupon Status.';
        if($promocode_status)
        {
            $header['success']       = true;
            $header['message']       = 'Discount Coupon Status Updated Successfully.';
        }
        $this->set_header($header);
        return $this->set_response();
     }
     
     /**
     *
     * This will delete bulk promocodes.
     * @param    array $promocode_ids
     * @return   response message
     *
     */
     function delete_promocode_bulk( $promocode_ids = array() )
     {
         $header                     = array();
         $promocode_delete           = $this->CI->Promocode_model->delete_promocode_bulk($promocode_ids);
         if($promocode_delete)
         {
             $header['success']      = true;
             $header['message']      = 'Discount Coupon Deleted Successfully.';
         }
         else
         {
             $header['success']      = false;
             $header['message']      = 'Error to Delete Discount Coupon.';
         }
         $this->set_header($header);
         return $this->set_response();
     }
     
     /**
     *
     * This will get all the users used promocodes.
     * @param    array $promocode_details
     * @return   array
     *
     */
     function users( $promocode_details = array() )
     {
         $header                     = array();
         $body                       = array();
         $promocode_id               = $promocode_details['promocode_id'];
         $parm                       = array(
                                                'id'    => $promocode_id
                                            );
         $usage_report               = $this->CI->Promocode_model->promocode($parm);
         $header['success']          = false;
         $header['message']          = 'Error to Fetch Discount Coupon Usage Details';
         $body['promocode_usage']    = array();
         if($usage_report)
         {
            $header['success']       = true;
            $header['message']       = 'Discount Coupon Usage Details Fetched Successfully';
            $body['promocode_usage'] = $usage_report;
         }
         $this->set_header($header);
         $this->set_body($body);
         return $this->set_response();

     }
}