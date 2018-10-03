<?php

class Model extends CI_Model {

    public function __construct(){
        $this->load->database();
        if (isset($_COOKIE['username']) and isset($_COOKIE['c'])) {
            $post_data = array (
                'biao' => '*',
                'ip' => $this->get_client_ip (),
            );
            $username = $_COOKIE['username'];
            $c = $_COOKIE['c'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://account.mixcm.com/ajax/sso_user');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_COOKIE, "username=$username;c=$c");
            $this->user=json_decode(curl_exec($ch),true);
            curl_close($ch);
        }
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
        if (isset($_COOKIE['username']) and isset($_COOKIE['c'])) {
            return $this->user[$biao];
        }
    }
    function get_info($url){    
        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        return $output;
    }

}