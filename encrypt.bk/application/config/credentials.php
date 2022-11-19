<?php
$config['menu_color']       = '#0b6ab4';
$config['code_version']     = '?v=1.8';
$config['memcache_server'] = array(
    'host' => '172.31.13.163',
    'port' => 11211,
    'weight' => 1,
);
$config['memcache_timeout'] = 7200;
$config['server_name'] = get_server_identifier();//$_SERVER['SERVER_NAME']; //SERVER_NAME,REMOTE_ADDR, HTTP_HOST;
$config['host'] = '172.31.12.121:6033';
$config['user'] = 'proxysql_user';
$config['password'] = 'p@$sw0rd';
$config['database'] = 'sgu';
$sess_save_path = '172.31.13.163:11211';
$config['message_api_url']  = 'https://sglearningapp.com/nservermessage/api/';
$config['jwt_token']        = 'yHFNF84ywxMvGBy';


function get_server_identifier()
{
    $server_identifier = $_SERVER['SERVER_NAME'];
    if($server_identifier == '')
    {
        $server_identifier = $_SERVER['HTTP_HOST'];
        if($server_identifier == '')
        {
            $server_identifier = $_SERVER['REMOTE_ADDR'];
        }
    }
    return $server_identifier;
}