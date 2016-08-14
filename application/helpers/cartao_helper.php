<?php

    function cartao_getPosts(){
        
        $ci = get_instance();
		
        $data["ano"]  		= $ci->input->post("ano");	
		$data["mes"]  		= $ci->input->post("mes");
        $data["type"]		= $ci->input->post("type");
		$data["usuario"]	= $ci->input->post("usuario");
		$data["data_compra"]= dataPtBrParaMysql($ci->input->post("data_compra"));
		$data["descricao"] 	= $ci->input->post("descricao");			
		$data["valor"]		= valor_decimal($ci->input->post("valor"));
        $data["p_total"]	= $ci->input->post("total");
		
		// -- CATEGORIA --						   							       	
		if(!empty($nova_categoria)){$data["categoria"] = $id_nova_categoria["id_categoria"];}
        if(empty($nova_categoria)){ $data["categoria"] = $ci->input->post("categoria");}
				
        /*SUB_CATEGORIA*/
        if(!empty($nova_sub_categoria)){$data["sub_categoria"] = $id_nova_sub_categoria["id_sub_categoria"];}
        if(empty($nova_sub_categoria)){ $data["sub_categoria"] = $ci->input->post("sub_categoria");}	
		
		/*TYPE*/
		if(empty($data["type"])){$data["type"] = "3";}
	   				       				
		return $data;
        
    }