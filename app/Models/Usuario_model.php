<?php
class Usuario_model extends CI_Model {
    
    public function get_by_username($username) {
        $this->db->where('strNombreUsuario', $username);
        $query = $this->db->get('Usuario');
        return $query->row();
    }
}