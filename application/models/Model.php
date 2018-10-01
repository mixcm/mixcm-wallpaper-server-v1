<?php

class Model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }
    
    public function get_client_ip (){
        if (getenv('HTTP_CLIENT_IP')){
            $ip=getenv('HTTP_CLIENT_IP');
        }elseif (getenv('HTTP_X_FORWARDED_FOR')){
            $ip=getenv('HTTP_X_FORWARDED_FOR');
        }elseif (getenv('HTTP_X_FORWARDED')){
            $ip=getenv('HTTP_X_FORWARDED');
        }elseif (getenv('HTTP_FORWARDED_FOR')){
            $ip=getenv('HTTP_FORWARDED_FOR');
        }elseif (getenv('HTTP_FORWARDED')){
            $ip=getenv('HTTP_FORWARDED');
        }else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function get_mixcm_user($biao='uid'){
        if(isset($_COOKIE['uid'])){
            $uid = $_COOKIE['uid'];
            $this->load->database('mixcm');
            $query = $this->db->get_where('user', array('name' => $uid));
            $row = $query->first_row('array');
            if ($_COOKIE['c'] == md5($row['name'].$row['uid'].'mixcm520')){
                return $row[$biao];
            }
        }
    }

}