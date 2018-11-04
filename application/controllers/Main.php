<?php
class Main extends CI_Controller {

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header('Content-type: application/json');
        $this->load->model('Model');
    }

    public function wallpaper($aid=null){
        if($aid==null){
            if(!isset($_GET['page'])) {
                $_GET['page'] = 1;
            }

            if(isset($_GET['slug'])) {
                $this->load->model('Meta');
                $meta = $this->Meta->row_meta($_GET['slug'],true);
                $query = $this->db->where('mid',$meta['mid']);
            }

            $limit = $_GET['page']*20-20;
            $this->db->limit(20, $limit);
            $this->db->select('aid,author,width,height,urls');
            $this->db->order_by('updated_at DESC');
            $query = $this->db->get('app');
            $array = $query->result_array();

            $this->load->model('Meta');
            for ($x=0; isset($array[$x]); $x++) {
                $array[$x]['urls'] = json_decode($array[$x]['urls']);
                $this->db->where('aid',$array[$x]['aid']);
                $this->db->select('mid');
                $query = $this->db->get('relationship');
                $metas = $query->result_array();
                foreach ($metas as $meta) {
                    $meta = $this->Meta->mid_meta($meta['mid'],true);
                    if($meta['type'] == 'class'){
                        $array[$x]['meta']['class'] = $meta;
                    }elseif($meta['type'] == 'tag'){
                        array_push($array[$x]['meta']['tag'],$meta);
                    }
                }
            } 
            echo json_encode($array);
        }else{
            $this->load->model('Wallpaper');
            $this->Wallpaper->row_wallpaper($aid);
        }
    }

    public function meta($slug=null) {
        if($slug==null){
            if(!isset($_GET['type']) or ($_GET['type'] != 'class' and $_GET['type'] != 'tag')) {
                $_GET['type'] = 'class';
            }
            $this->db->select('name,slug,description,created_at');
            $this->db->where('type',$_GET['type']);
            $query = $this->db->get('meta');
            $array = $query->result_array();
            echo json_encode($array);
        }else{
            $this->load->model('Meta');
            $this->Meta->row_meta($slug);
        }
    }

    public function re() {
        $query = $this->db->get('app');
        $array = $query->result_array();
        foreach ($array as $value) {
            $this->db->where('aid', $value['aid']);
            $this->db->from('relationship');
            $count = $this->db->count_all_results();
            if($count == 0){
                $datae = array(
                    'aid' => $value['aid'],
                    'mid' => 3,
                );
                $this->db->insert('relationship', $datae);
            }
        }
        print_r($array);
    }

    public function mode($page='home'){
        $data = json_decode(
            $this->Model->get_info('https://api.unsplash.com/photos/?client_id=7ab2e7a3a804f2007c49530a0d0372fea2231079181a22775c23d58a3cfb1880&per_page=30&page=4')
            ,true
        );
        foreach ($data as $e) {
            $this->db->where('crawler_id', $e['id']);
            $this->db->from('app');
            $count = $this->db->count_all_results();
            if($count == 0){
                $user = array(
                    'name' => $e['user']['name'],
                    'url' => $e['user']['links']['html'],
                );
                $datae = array(
                    'crawler_id' => $e['id'],
                    'created_at' => strtotime($e['created_at']),
                    'updated_at' => strtotime($e['updated_at']),
                    'width' => $e['width'],
                    'height' => $e['height'],
                    'color' => $e['color'],
                    'description' => $e['description'],
                    'uid' => '6000',
                    'urls' => json_encode($e['urls']),
                    'links' => json_encode($e['links']),
                    'author' => json_encode($user),
                );
                $this->db->insert('app', $datae);
                print_r($this->db->insert_id());
                $re = array(
                    'aid' => $this->db->insert_id(),
                    'mid' => 3,
                );
                $this->db->insert('relationship', $re);
            }
        } 
    }


}