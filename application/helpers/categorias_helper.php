<?php

    function novaCategoria(){
        
		$ci = get_instance();
		
		$novaCategoria	= $ci->input->post("adiciona-categoria");
        
		if(!empty($novaCategoria)){
            
        	$categoria["nome_categoria"] = $novaCategoria;	
			
            // -- BD : INSERIR -- 
            $ci->categoria_model->Inserir($categoria);
        }
		
		return $novaCategoria;
        
	}
	