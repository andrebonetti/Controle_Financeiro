<?php

    function transacao_getPosts(){
        
		$ci = get_instance();
		
			$data["dia"] 		= $ci->input->post("dia");
			$data["descricao"] 	= $ci->input->post("descricao");	
			$data["valor"]		= valor_decimal($ci->input->post("valor"));		
			$data["type"]		= $ci->input->post("type");
            $data["p_total"]    = $ci->input->post("total");
            $data["ano"]        = $ci->input->post("ano");	
			$data["mes"]  		= $ci->input->post("mes");

				/*CATEGORIA*/						   							       	
				if(!empty($nova_categoria)){$data["categoria"] = $id_nova_categoria["id_categoria"];}
            	if(empty($nova_categoria)){ $data["categoria"] = $ci->input->post("categoria");}
				
				/*SUB_CATEGORIA*/
				if(!empty($nova_sub_categoria)){$data["sub_categoria"] = $id_nova_sub_categoria["id_sub_categoria"];}
                if(empty($nova_sub_categoria)){ $data["sub_categoria"] = $ci->input->post("sub_categoria");}
			
            // -- ATUALIZACAO --
                if($ci->input->post("id") > 0)     { $data["id"] 	 = $ci->input->post("id");}
                if($ci->input->post("usuario") > 0){$data["usuario"] = $ci->input->post("usuario");}
        
			/*TYPE*/
			if(empty($data["type"])){$data["type"] = "3";}
            if(empty($data["dia"]))	{$data["type"] = "4";}
			
		return $data;	
    
    }