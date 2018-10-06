<?php
class Main extends CI_Controller {

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
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
            $query = $this->db->get('relationship');
            $array = $query->result_array();
            
            $this->load->model('Wallpaper');
            for ($x=0; isset($array[$x]); $x++) {
                $array[$x] = $this->Wallpaper->row_wallpaper($array[$x]['aid'],true,'aid,width,height,urls');
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
            $this->Model->get_info('https://api.unsplash.com/photos/?client_id=0d095f7c17a870835c4b9aae20fa4ffcafb7ba4cb0c627668dfe56561a6fa83c&per_page=30&page=5')
            ,true
        );
        print_r($data);
        foreach ($data as $e) {
            $this->db->where('aid', $e['id']);
            $this->db->from('app');
            $count = $this->db->count_all_results();
            if($count == 0){
                $data_single = json_decode(
                    $this->Model->get_info('https://api.unsplash.com/photos/'.$e['id'].'?client_id=0d095f7c17a870835c4b9aae20fa4ffcafb7ba4cb0c627668dfe56561a6fa83c&per_page=30&page=1')
                    ,true
                );
                $user = array(
                    'name' => $e['user']['name'],
                    'url' => $e['user']['links']['html'],
                );
                $datae = array(
                    'aid' => $e['id'],
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
                    'exif' => json_encode($data_single['exif']),
                    'location' => json_encode($data_single['location']),
                );
                $this->db->insert('app', $datae);

                $re = array(
                    'aid' => $e['id'],
                    'mid' => 3,
                );
                $this->db->insert('relationship', $re);
            }
        } 
    }


}