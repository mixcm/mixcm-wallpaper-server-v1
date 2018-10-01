<?php
/**
 * https://codeigniter.org.cn/user_guide/helpers/url_helper.html
 * 加载静态内容:https://codeigniter.org.cn/user_guide/tutorial/static_pages.html
 */
class Main extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Model');
        $this->load->helper('url_helper');
    }

    public function view($page='home',$child="last_seen_time"){
        $this->load->model('Mixcm_pages');

        $this->load->view('pages/'.$page, $data);
    }


}