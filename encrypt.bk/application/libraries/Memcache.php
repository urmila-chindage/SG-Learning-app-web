<?php
class Memcache
{
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->expire = 7200;
        $this->common_key = 'common';
    }

    public function get($objects, $callback = false, $params = array())
    {
        $pure_key   = isset($objects['pure_key']) ? $objects['pure_key'] : false;
        $base_key   = isset($objects['key']) ? $objects['key'] : $this->common_key;
        $key        = ($pure_key) ? $base_key: $this->accountify_index( $base_key );
        $expiry     = isset($objects['expiry']) ? $objects['expiry'] : $this->expire;
        $content    = $this->CI->cache->memcached->get($key);
        if (!is_array($content)) 
        {
            if ($callback) {
                
                $content = $callback($params, $this->CI);
                $this->set($base_key, $content, $expiry);
            }
        }
        return $content;
    }

    public function set($key = '', $content = array(), $expiry = false)
    {
        $expiry = ($expiry) ? $expiry : $this->expire;
        $key = $this->accountify_index( $key );
        return $this->CI->cache->memcached->save($key, $content, $expiry);
    }

    public function replace($key, $data)
    {
        $key = $this->accountify_index( $key );
        return $this->memcache->replace($key, $data, 0, $this->expire);
    }

    public function delete($key, $when = 0, $pure_key = false)
    {
        if(!$pure_key)
        {
            $key = $this->accountify_index( $key );
        }
        return $this->CI->cache->memcached->delete($key, $when);
    }

    public function get_multi($params = array())
    {
        $return = array();
        if (!empty($params)) {
            $keys = array();
            foreach ($params as $key => $value) {
                $keys[$key] = $this->accountify_index( $value );
            }
            $return = $this->CI->cache->memcached->get_multi($keys);
        }
        return $return;
    }
    
    public function set_multi($params = array(), $expire = false)
    {
        $expire = ($expire) ? $expire : $this->expire;
        $return = false;
        if (!empty($params)) {
            $return = true;
            $objects = array();
            foreach ($params as $key => $value) {
                $objects[$this->accountify_index( $key )] = $value;
            }
            $this->CI->cache->memcached->set_multi($objects, $expire);
        }
        return $return;
    }

    private function accountify_index( $index = '' )
    {
        return $index.$this->account_to_alpha(config_item('id'));
        // return $index;
    }

    private function account_to_alpha($number)
    {
        $alpha = '';
        $alphabet = range('a','z');
        $count = count($alphabet);
        if($number <= $count)
        {
            $alpha = $alphabet[$number-1];
        }
        else
        {
            while($number > 0)
            {
                $modulo     = ($number - 1) % $count;
                $alpha      = $alphabet[$modulo].$alpha;
                $number     = floor((($number - $modulo) / $count));
            }    
        }
        return $alpha;
    }

    public function getAllKeys()
    {
        return $this->CI->config->item('memcache_object')->getAllKeys();
    }

    public function getAllValues()
    {
        $keys = $this->CI->config->item('memcache_object')->getAllKeys(); 
        $this->CI->config->item('memcache_object')->getDelayed($keys); 
        return $this->CI->config->item('memcache_object')->fetchAll(); 
    }

    public function deleteAllKeys()
    {
        $keys = $this->CI->config->item('memcache_object')->getAllKeys(); 
        $i = 1;
        foreach($keys as $item) {
            echo '<pre>'.$i.' Deleting <b>'.$item.'</b>...!</br>'; 
            $this->CI->config->item('memcache_object')->delete($item); 
            $i++; 
        }
        echo '<pre>All Cached keys and values have been successfully deleted...!</br>'; 
    }

    public function resetAccountMemcache($key = false)
    {
        return $this->CI->config->item('memcache_object')->delete($key);
    }

}
