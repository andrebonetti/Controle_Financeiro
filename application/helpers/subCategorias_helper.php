<?php 

    function novaSubCategoria(){
		$ci = get_instance();
		
		$novaCategoria		              = $ci->input->post("adiciona-categoria"); 
		$novaSubCategoria	              = $ci->input->post("adiciona-sub_categoria");
	
        /*if(!empty($novaSubCategoria)){
            
        	$categoriaRelacionada     = $ci->input->post("categoria-sub");
                
            if($categoriaRelacionada != "categoria-nova"){
                $subCategoria["categoria"] = $categoria_relacionada;
            }
                
            $sub_categoria["nome_sub_categoria"] = $nova_sub_categoria;
            $ci->crud_model->insert("sub_categoria",$sub_categoria);
        }
		
		return $nova_sub_categoria;	*/
	}
	