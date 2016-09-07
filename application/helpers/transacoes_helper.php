<?php

    function transacao_getPosts(){
        
		$ci = get_instance();
		
			$data["Dia"] 		= $ci->input->post("dia");
			$data["Descricao"] 	= $ci->input->post("descricao");	
			$data["Valor"]		= valor_decimal($ci->input->post("valor"));		

            $data["Ano"]        = $ci->input->post("ano");	
			$data["Mes"]  		= $ci->input->post("mes");

				/*CATEGORIA*/						   							       	
				if(!empty($nova_categoria)){$data["IdCategoria"] = $id_nova_categoria["id_categoria"];}
            	if(empty($nova_categoria)){ $data["IdCategoria"] = $ci->input->post("categoria");}
				
				/*SUB_CATEGORIA*/
				if(!empty($nova_sub_categoria)){$data["IdSubCategoria"] = $id_nova_sub_categoria["id_sub_categoria"];}
                if(empty($nova_sub_categoria)){ $data["IdSubCategoria"] = $ci->input->post("sub_categoria");}
			
            // -- ATUALIZACAO --
            if($ci->input->post("id") > 0)     { $data["Id"] 	 = $ci->input->post("id");}
            if($ci->input->post("usuario") > 0){$data["IdUsuario"] = $ci->input->post("usuario");}
        
			// ----- TYPE -----
            $data["IdTipoTransacao"] = 3;
        
            //Recorrente
			if($ci->input->post("isRecorrente") == 1)
            {
                $data["IdTipoTransacao"] = 1;
            }
            else{
                //Recorrente 
                if($ci->input->post("totalParcelas") > 0)
                {
                    $data["IdTipoTransacao"]    = 2;
                    $data["TotalParcelas"] = $ci->input->post("totalParcelas");
                }
            }
        
		return $data;	
    
    }