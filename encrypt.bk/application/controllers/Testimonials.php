<?php
class Testimonials extends CI_Controller {
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
        $testimonial_objects            = array();
        $data['testimonials']           = array();
        $testimonial_objects['key']     = 'testimonials';
        $testimonial_callback           = 'testimonials';
        $testimonial_params             = array();
        $testimonial_params['select']   = 't_name, t_other_detail, t_image, t_text';
       
        $testimonials                   = $this->memcache->get($testimonial_objects, $testimonial_callback, $testimonial_params);
        $data['testimonials']           = $testimonials;
        $this->load->view($this->config->item('theme').'/testimonial', $data);
    }
}