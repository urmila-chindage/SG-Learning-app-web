<?php
if (file_exists(dirname(__FILE__).'/vimeo_vendor/autoload.php')) {
    require_once dirname(__FILE__).'/vimeo_vendor/autoload.php';
}

use Vimeo\Vimeo;
class VimeoUpload
{
    var $vimeo_keys;
    public function __construct()
    {
        $this->CI   = &get_instance();
        $v_config   = $this->CI->settings->setting('has_vimeo');
        $this->vimeo_keys   = $v_config['as_setting_value']['setting_value'];
    }

    function set_config()
    {
        // $this->lib  = new Vimeo($this->vimeo_keys['client_id'], $this->vimeo_keys['client_secret'], $this->vimeo_keys['access_token']);
        $client_id      = $this->vimeo_keys->client_id;
        $client_secret  = $this->vimeo_keys->client_secret;
        $access_token   = $this->vimeo_keys->access_token;
        $this->lib      = new Vimeo($client_id,$client_secret,$access_token);
        // $this->lib      = new Vimeo('b24f5310f27894266a46789772baee175c8a82b5','l+wzLwkDIyrgY/PPXX05YfKL+rd2YoqgbPLicVSthZtqMt7A/8MEhgN+6I1o+pkxSZxiJNQZTjpFnU1b6nsc1oJSk0tnAb38kX5p+t3b5N0gTkIAxpWRVDJrS5kFgGIV','ec42d379882840f11436c3d6ab05fc5c');
    }

    public function upload($upload_config = array()){ //Upload video method
        $return     = array();
        $video      = isset($upload_config['path'])?$upload_config['path']:false;
        
        if(file_exists($video)){
            $video_name         = isset($upload_config['name'])?$upload_config['name']:'SDPK Video';
            $video_description  = isset($upload_config['description'])?$upload_config['description']:'SDPK Video';
            $uri = $this->lib->upload($video, array(
                'name' => $video_name,
                'description' => $video_description,
            ));
            $return['message']  = 'Upload success.';
            $return['data']     = $uri;
            $return['success']  = true;
        }else{
            $return['message']  = 'Invalid file type.';
            $return['data']     = array();
            $return['success']  = false;
        }

        return $return;
    }

    function pull_upload($upload_config = array()){
        $return     = array();
        $video      = isset($upload_config['path'])?$upload_config['path']:false;
        
        if($video){
            $video_name         = isset($upload_config['name'])?$upload_config['name']:'SDPK Video';
            $video_description  = isset($upload_config['description'])?$upload_config['description']:'SDPK Video';
            // $uri = $this->lib->request('/me/videos', array('type' => 'pull', 'link' => $video), 'POST');
            //add videos to an album //https://api.vimeo.com/me/albums/{album_id}/videos/{video_id}
            ///albums/6596573/videos

            $uri = $this->lib->request(
                '/me/videos',
                [
                    'upload' => ['approach' => 'pull', 'link' => $video]
                ],
                'POST'
            );
            $return['message']  = 'Upload success.';
            $return['data']     = $uri;
            $return['success']  = true;
        }else{
            $return['message']  = 'Invalid file type.';
            $return['data']     = array();
            $return['success']  = false;
        }

        return $return;
    }

    public function delete($video_data){
        $uri        = isset($video_data['uri'])?$video_data['uri']:false;

        if($uri){
            $video_data = $this->lib->request($uri, [], 'DELETE');
            $return['data']     = $video_data;
            $return['message']  = 'Delete success.';
            $return['success']  = true;
        }else{
            $return['data']     = array();
            $return['message']  = 'Delete failed, invalid input.';
            $return['success']  = false;
        }
        
        return $return;
    }

    public function edit($video_data = array()){ //Edit video method name and description
        $uri        = isset($video_data['uri'])?$video_data['uri']:false;

        if($uri){
            $data       = array();
            if(isset($video_data['name'])){
                $data['name']   = $video_data['name'];
            }
            if(isset($video_data['description'])){
                $data['description']   = $video_data['description'];
            }
            $this->lib->request($uri,$data, 'PATCH');
            $return['message']  = 'Edit success.';
            $return['success']  = true;
        }else{
            $return['message']  = 'Edit failed invalid input.';
            $return['success']  = false;
        }

        return $return;
    }

    public function video($video_data = array()){ //Video details
        $uri        = isset($video_data['uri'])?$video_data['uri']:false;

        if($uri){
            $video_data = $this->lib->request($uri . '?fields=link');
            $return['data']     = $video_data;
            $return['message']  = 'Fetch success.';
            $return['success']  = true;
        }else{
            $return['data']     = array();
            $return['message']  = 'Fetch failed, invalid input.';
            $return['success']  = false;
        }
        
        return $return;
    }

    public function status($video_data = array()){
        $uri        = isset($video_data['uri'])?$video_data['uri']:false;

        if($uri){
            $video_data = $this->lib->request($uri . '?fields=transcode.status');
            $return['data']     = $video_data;
            $return['message']  = 'Fetch success.';
            $return['success']  = true;
        }else{
            $return['data']     = array();
            $return['message']  = 'Fetch failed, invalid input.';
            $return['success']  = false;
        }
        
        return $return;
    }

}
