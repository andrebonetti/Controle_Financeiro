<?php

    function filter($type,$value){
        
       $ci = get_instance();    
        
	   /* STATUS */
       if($type == "clean"){$ci->session->set_userdata("status","0");}
       if($type != "clean"){$ci->session->set_userdata("status","1");}              
                            
            
       if($type  != "clean"){
            $ci->session->set_userdata($type,$value);
            $ci->session->set_userdata("type",$type);
        }  
                   
        if($type  == "clean"){$ci->session->sess_destroy();}//CLEAN
        if($value == "cancel"){$ci->session->unset_userdata($type);}//CANCEL
        
        /* --- TITULO --- */      
        if($type == "categoria"){$titulo = $ci->produtos_model->titulo("categoria",$value);  }
        if($type == "marca"){$titulo = $ci->produtos_model->titulo("marca",$value);}
        if($type == "clean"){$titulo["nome_clean"]   = "Catálogo Completo";}
        
        $ci->session->set_userdata("titulo",$titulo["nome_".$type.""]);  
	}