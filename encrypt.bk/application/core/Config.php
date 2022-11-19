<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT    MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config
{
    /**
     * List of all loaded config values
     *
     * @var    array
     */
    public $config = array();
    /**
     * List of all loaded config files
     *
     * @var    array
     */
    public $is_loaded = array();
    /**
     * List of paths to search when trying to load a config file.
     *
     * @used-by    CI_Loader
     * @var        array
     */
    public $_config_paths = array(APPPATH);
    private $_web_configs;
    private $today;
    // --------------------------------------------------------------------
    /**
     * Class constructor
     *
     * Sets the $config data from the primary config.php file as a class variable.
     *
     * @return    void
     */
    public function __construct()
    {
        $this->config   = &get_config();
        $this->time_out = $this->config['memcache_timeout'];
        log_message('info', 'Config Class Initialized');
        date_default_timezone_set("Asia/Kolkata");
        $this->today = date("Y-m-d H:i:s", time() + (5.30 * 60 * 60));
        $this->_connection_procedure();
        $this->_copy_webactions_to_config();
    }
    /**
     * This method will check all the criteria such as website expired or deactivated or deleted.
     * If any of the criteria matches then the website will be redirect ti its parent website(www.ofabee.com)
     **/
    public function _connection_procedure()
    {
        /*
         * Query Checking the site configuration
         * Account status
         * Account deleted or not
         * Account Expired or not
         */
        $this->_memcache_object = new Memcached();
        $this->set_item('memcache_object', $this->_memcache_object);
        $this->_memcache_object->addServer(
             $this->config['memcache_server']['host']
            ,$this->config['memcache_server']['port']
            ,$this->config['memcache_server']['weight']
        );
        $this->__account__  = $this->_memcache_object->get($this->config['server_name']);
        $this->__account__  = isset($this->__account__[0])?$this->__account__[0]:0;
        $this->_web_configs = array();

        if($this->__account__)
        {
            $this->_web_configs = $this->_memcache_object->get($this->__account__.'_web_configs');
            //echo '<pre>'; print_r($this->_web_configs);die;
        }

        if (!isset($this->_web_configs[0]) || empty($this->_web_configs[0])) 
        {
            $config_where = " AND (acct_domain LIKE '" . $this->config['server_name'] . "' OR acct_domain_whitelist LIKE '%" . $this->config['server_name'] . "%')";
            if (isset($_POST['domain_key']) && trim($_POST['domain_key']) != '') 
            {
                $config_where = " AND accounts.id = " . $_POST['domain_key'];
            }
            // $config_query = "SELECT accounts.*,users.id as us_id, users.us_name FROM accounts LEFT JOIN users ON users.us_account_id = accounts.id WHERE acct_status = '1' AND users.us_role_id = '1' AND acct_deleted = '0' AND acct_expiry_date >='" . $this->today . "' " . $config_where . " LIMIT 1";
            $config_query       = "SELECT accounts.*  FROM accounts WHERE acct_status = '1' AND acct_deleted = '0' AND acct_expiry_date >='" . $this->today . "' " . $config_where . " LIMIT 1";
            $this->connection   = isset($this->connection)?$this->connection:$this->_connect();
            $this->_web_configs = mysqli_fetch_assoc(mysqli_query($this->connection, $config_query));
            if(isset($this->_web_configs['id']))
            {
                $this->__account__              = $this->_web_configs['id'];
                $super_admin_query              = "SELECT users.id as us_id, users.us_name FROM users WHERE us_account_id =".$this->__account__." AND us_role_id='1'";
                $this->client_db_connection     = $this->_connect(array('host' => $this->_web_configs['acct_host'], 'user' => $this->_web_configs['acct_db_user'], 'database' => $this->_web_configs['acct_database'], 'password' => $this->_web_configs['acct_db_password']));
                $super_admin                    = mysqli_fetch_assoc(mysqli_query($this->client_db_connection, $super_admin_query));
                if(!empty($super_admin))
                {
                    $this->_web_configs['us_id']    = $super_admin['us_id'];
                    $this->_web_configs['us_name']  = $super_admin['us_name'];    
                }
                $this->_memcache_object->set($this->__account__.'_web_configs', array($this->_web_configs), $this->time_out);
                $this->_memcache_object->set($this->config['server_name'], array($this->__account__), $this->time_out);
            }
        } 
        else
        {
            $this->_web_configs = $this->_web_configs[0];
            //echo '<pre>'; print_r($this->_web_configs);die('-==-');
        }
        // echo '<pre>'; print_r($this->_web_configs);die('-==-');
        if (empty($this->_web_configs)) 
        {
            //echo 'redirecting...';die;
            header('Location:http://ofabee.com/');exit;
        }
        else
        {
            /*
             * applying the web configurtion to the array $config
             */
            if (!empty($this->_web_configs))
            {
                foreach ($this->_web_configs as $key => $value) 
                {
                    $this->set_item($key, $value);
                }
            }
        }
        $this->_sub_admin = $this->_memcache_object->get($this->__account__.'_sub_admin');
        if (empty($this->_sub_admin)) 
        {
            $sub_admin_query                = "SELECT GROUP_CONCAT(id) as ids FROM users WHERE us_account_id ='" . $this->item('id') . "' AND us_deleted='0' AND us_role_id = '1' AND id !='" . $this->item('us_id') . "'";
            $this->client_db_connection     = isset($this->client_db_connection)?$this->client_db_connection:$this->_connect(array('host' => $this->_web_configs['acct_host'], 'user' => $this->_web_configs['acct_db_user'], 'database' => $this->_web_configs['acct_database'], 'password' => $this->_web_configs['acct_db_password']));
            $sub_admin                      = mysqli_fetch_assoc(mysqli_query($this->client_db_connection, $sub_admin_query));
            $this->_sub_admin               = array();
            if (isset($sub_admin['ids'])) 
            {
                if(empty($sub_admin['ids']))
                {
                    $this->_sub_admin = array('0');    
                }
                else
                {
                    $this->_sub_admin = explode(',', $sub_admin['ids']);
                }
                $this->_memcache_object->set($this->__account__.'_sub_admin', array($this->_sub_admin), $this->time_out);
            }
        }
        $this->_sub_admin = isset($this->_sub_admin[0]) ? $this->_sub_admin[0] : array();
        $this->set_item('super_admin', $this->item('us_id'));
        $this->set_item('sub_admins', $this->_sub_admin);
    }
    /**
     * This method will set webaction in config variable called web_action
     **/
    public function _copy_webactions_to_config()
    {
        $actions = $this->_memcache_object->get($this->__account__.'_actions');
        $actions = isset($actions[0])?$actions[0]:array();
        //echo '<pre>'; print_r($actions);die;
        if (empty($actions)) 
        {
            /*
             * Query fetching web action from the table web_actions
             */
            $web_actions_query = "SELECT * FROM web_actions";
            $this->connection = isset($this->connection)?$this->connection:$this->_connect();
            $web_actions = mysqli_query($this->connection, $web_actions_query);
            /*
             * applying the web configurtion to the array $config
             */
            if (mysqli_num_rows($web_actions) > 0) {
                $actions = array();
                while ($web_action = mysqli_fetch_assoc($web_actions)) {
                    $actions[$web_action['id']] = array('code' => $web_action['wa_code'], 'label' => $web_action['wa_name'], 'weight' => $web_action['wa_weight']);
                    $actions[$web_action['wa_code']] = $web_action['id'];
                }
                $this->_memcache_object->set($this->__account__.'_actions', array($actions), $this->time_out);
            }
        }
        $this->set_item('actions', $actions);
    }
    
    public function _connect($param = array())
    {
        $host       = isset($param['host'])?$param['host']:$this->config['host'];
        $user       = isset($param['user'])?$param['user']:$this->config['user'];
        $password   = isset($param['password'])?$param['password']:$this->config['password'];
        $database   = isset($param['database'])?$param['database']:$this->config['database'];
        return mysqli_connect($host, $user, $password, $database);
    }
    // --------------------------------------------------------------------
    /**
     * Load Config File
     *
     * @param    string    $file            Configuration file name
     * @param    bool    $use_sections        Whether configuration values should be loaded into their own section
     * @param    bool    $fail_gracefully    Whether to just return FALSE or display an error message
     * @return    bool    TRUE if the file was loaded correctly or FALSE on failure
     */
    public function load($file = '', $use_sections = false, $fail_gracefully = false)
    {
        $file = ($file === '') ? 'config' : str_replace('.php', '', $file);
        $loaded = false;
        foreach ($this->_config_paths as $path) {
            foreach (array($file, ENVIRONMENT . DIRECTORY_SEPARATOR . $file) as $location) {
                $file_path = $path . 'config/' . $location . '.php';
                if (in_array($file_path, $this->is_loaded, true)) {
                    return true;
                }
                if (!file_exists($file_path)) {
                    continue;
                }
                include $file_path;
                if (!isset($config) or !is_array($config)) {
                    if ($fail_gracefully === true) {
                        return false;
                    }
                    show_error('Your ' . $file_path . ' file does not appear to contain a valid configuration array.');
                }
                if ($use_sections === true) {
                    $this->config[$file] = isset($this->config[$file])
                    ? array_merge($this->config[$file], $config)
                    : $config;
                } else {
                    $this->config = array_merge($this->config, $config);
                }
                $this->is_loaded[] = $file_path;
                $config = null;
                $loaded = true;
                log_message('debug', 'Config file loaded: ' . $file_path);
            }
        }
        if ($loaded === true) {
            return true;
        } elseif ($fail_gracefully === true) {
            return false;
        }
        show_error('The configuration file ' . $file . '.php does not exist.');
    }
    // --------------------------------------------------------------------
    /**
     * Fetch a config file item
     *
     * @param    string    $item    Config item name
     * @param    string    $index    Index name
     * @return    string|null    The configuration item or NULL if the item doesn't exist
     */
    public function item($item, $index = '')
    {
        if ($index == '') {
            return isset($this->config[$item]) ? $this->config[$item] : null;
        }
        return isset($this->config[$index], $this->config[$index][$item]) ? $this->config[$index][$item] : null;
    }
    // --------------------------------------------------------------------
    /**
     * Fetch a config file item with slash appended (if not empty)
     *
     * @param    string        $item    Config item name
     * @return    string|null    The configuration item or NULL if the item doesn't exist
     */
    public function slash_item($item)
    {
        if (!isset($this->config[$item])) {
            return null;
        } elseif (trim($this->config[$item]) === '') {
            return '';
        }
        return rtrim($this->config[$item], '/') . '/';
    }
    // --------------------------------------------------------------------
    /**
     * Site URL
     *
     * Returns base_url . index_page [. uri_string]
     *
     * @uses    CI_Config::_uri_string()
     *
     * @param    string|string[]    $uri    URI string or an array of segments
     * @param    string    $protocol
     * @return    string
     */
    public function site_url($uri = '', $protocol = null)
    {
        $base_url = $this->slash_item('base_url');
        if (isset($protocol)) {
            // For protocol-relative links
            if ($protocol === '') {
                $base_url = substr($base_url, strpos($base_url, '//'));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
            }
        }
        if (empty($uri)) {
            return $base_url . $this->item('index_page');
        }
        $uri = $this->_uri_string($uri);
        if ($this->item('enable_query_strings') === false) {
            $suffix = isset($this->config['url_suffix']) ? $this->config['url_suffix'] : '';
            if ($suffix !== '') {
                if (($offset = strpos($uri, '?')) !== false) {
                    $uri = substr($uri, 0, $offset) . $suffix . substr($uri, $offset);
                } else {
                    $uri .= $suffix;
                }
            }
            return $base_url . $this->slash_item('index_page') . $uri;
        } elseif (strpos($uri, '?') === false) {
            $uri = '?' . $uri;
        }
        return $base_url . $this->item('index_page') . $uri;
    }
    // -------------------------------------------------------------
    /**
     * Base URL
     *
     * Returns base_url [. uri_string]
     *
     * @uses    CI_Config::_uri_string()
     *
     * @param    string|string[]    $uri    URI string or an array of segments
     * @param    string    $protocol
     * @return    string
     */
    public function base_url($uri = '', $protocol = null)
    {
        $base_url = $this->slash_item('base_url');
        if (isset($protocol)) {
            // For protocol-relative links
            if ($protocol === '') {
                $base_url = substr($base_url, strpos($base_url, '//'));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
            }
        }
        return $base_url . ltrim($this->_uri_string($uri), '/');
    }
    // -------------------------------------------------------------
    /**
     * Build URI string
     *
     * @used-by    CI_Config::site_url()
     * @used-by    CI_Config::base_url()
     *
     * @param    string|string[]    $uri    URI string or an array of segments
     * @return    string
     */
    protected function _uri_string($uri)
    {
        if ($this->item('enable_query_strings') === false) {
            if (is_array($uri)) {
                $uri = implode('/', $uri);
            }
            return trim($uri, '/');
        } elseif (is_array($uri)) {
            return http_build_query($uri);
        }
        return $uri;
    }
    // --------------------------------------------------------------------
    /**
     * System URL
     *
     * @deprecated    3.0.0    Encourages insecure practices
     * @return    string
     */
    public function system_url()
    {
        $x = explode('/', preg_replace('|/*(.+?)/*$|', '\\1', BASEPATH));
        return $this->slash_item('base_url') . end($x) . '/';
    }
    // --------------------------------------------------------------------
    /**
     * Set a config file item
     *
     * @param    string    $item    Config item key
     * @param    string    $value    Config item value
     * @return    void
     */
    public function set_item($item, $value)
    {
        $this->config[$item] = $value;
    }
}