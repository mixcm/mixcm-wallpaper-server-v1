<?php

class Meta extends CI_Model {

    public function __construct(){
        $this->load->database();
    }
    
    function row_meta($slug=null,$return=false){
        
        $this->db->where('slug',$slug);
        $query = $this->db->get('meta');
        $array = $query->row_array();

        if($return==true){
            return $array;
        }else{
            echo json_encode($array);
        }
    }

    function mid_meta($mid=null,$return=false){
        
        $this->db->where('mid',$mid);
        $query = $this->db->get('meta');
        $array = $query->row_array();

        if($return==true){
            return $array;
        }else{
            echo json_encode($array);
        }
    }

}