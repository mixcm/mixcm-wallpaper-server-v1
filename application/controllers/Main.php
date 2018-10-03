<?php
class Main extends CI_Controller {

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        $this->load->model('Model');
        $this->load->helper('url_helper');
    }

    public function wallpaper($aid=""){
        if(!isset($_GET['page'])) {
            $_GET['page'] = 1;
        }
        $limit = $_GET['page']*20-20;
        $this->db->limit(20, $limit);
        $query = $this->db->get('app');
        $array = $query->result_array();
        $x = 0;
        while(isset($array[$x])){
            $array[$x]['urls'] = json_decode($array[$x]['urls']);
            $array[$x]['links'] = json_decode($array[$x]['links']);
            $x += 1;
        }
        echo json_encode($array);
    }

    public function meta() {
        if(!isset($_GET['type']) or ($_GET['type'] != 'class' and $_GET['type'] != 'tag')) {
            $_GET['type'] = 'class';
        }
        $this->db->select('name,slug,description,created_at');
        $this->db->where('type',$_GET['type']);
        $query = $this->db->get('meta');
        $array = $query->result_array();
        echo json_encode($array);
    }

    public function mode($page='home'){
        $data = json_decode(
            $this->Model->get_info('https://api.unsplash.com/photos/?client_id=0d095f7c17a870835c4b9aae20fa4ffcafb7ba4cb0c627668dfe56561a6fa83c&per_page=30&page=6')
            ,true
        );
        print_r($data);
        foreach ($data as $e) {
            $this->db->where('aid', $e['id']);
            $this->db->from('app');
            $count = $this->db->count_all_results();
            if($count == 0){
                $datae = array(
                    'aid' => $e['id'],
                    'created_at' => strtotime($e['created_at']),
                    'updated_at' => strtotime($e['updated_at']),
                    'width' => $e['width'],
                    'height' => $e['height'],
                    'color' => $e['color'],
                    'description' => $e['description'],
                    'urls' => json_encode($e['urls']),
                    'links' => json_encode($e['links']),
                    'uid' => '6000',
                );
                $this->db->insert('app', $datae);
            }
        } 
    }


}