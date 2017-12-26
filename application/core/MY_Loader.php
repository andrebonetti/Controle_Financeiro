<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
   
    public function template($nome,$content) {
        $this->view("base_html/header.php",$content);
        $this->view("templates/".$nome,$content);
        $this->view("base_html/footer.php",$content);
    }

}