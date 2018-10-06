<?php

class Wallpaper extends CI_Model {

    public function __construct(){
        $this->load->database();
    }
    
    function row_wallpaper($aid,$return=false,$select=null){
        $this->load->model('Meta');
        $this->db->where('aid',$aid);
        if($select!=null){
            $this->db->select($select);
        }
        $query = $this->db->get('app');
        $array = $query->row_array();
        if(isset($array['urls'])){
            $array['urls'] = json_decode($array['urls']);
        }
        if(isset($array['links'])){
            $array['links'] = json_decode($array['links']);
        }
        if(isset($array['author'])){
            $array['author'] = json_decode($array['author']);
        }
        if(isset($array['exif'])){
            $array['exif'] = json_decode($array['exif']);
        }
        if(isset($array['location'])){
            $array['location'] = json_decode($array['location']);
        }

        $this->db->where('aid',$array['aid']);
        $this->db->select('mid');
        $query = $this->db->get('relationship');
        $metas = $query->result_array();
        foreach ($metas as $meta) {
            $meta = $this->Meta->mid_meta($meta['mid'],true);
            if($meta['type'] == 'class'){
                $array['meta']['class'] = $meta;
            }elseif($meta['type'] == 'tag'){
                array_push($array['meta']['tag'],$meta);
            }
        }
        
        if($return==true){
            return $array;
        }else{
            echo json_encode($array);
        }
    }

}