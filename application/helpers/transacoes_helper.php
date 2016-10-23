<?php

    function transacao_getPosts(){
        
		$ci = get_instance();
        
        if($ci->input->post("dia") != ""){$data["Dia"] 		= $ci->input->post("dia");}
        $data["Descricao"] 	= $ci->input->post("descricao");	
        $data["Valor"]		= valor_decimal($ci->input->post("valor"));		
        $data["Ano"]        = $ci->input->post("ano");	
        $data["Mes"]  		= $ci->input->post("mes");

        // ----- CATEGORIA -----
        
            // -- NOVA CATEGORIA
            if($ci->input->post("categoria") == "nova-categoria"){
                
                $novaCategoria["DescricaoCategoria"]	= $ci->input->post("adiciona-categoria");
                $ci->categoria_model->Incluir($novaCategoria);
                
                $novaCategoria = $ci->categoria_model->Buscar($novaCategoria);

                $novaSubCategoria["DescricaoSubCategoria"] = $ci->input->post("adiciona-subcategoria");
                $novaSubCategoria["IdCategoria"] = $novaCategoria["IdCategoria"];
                $ci->subCategoria_model->Incluir($novaSubCategoria); 

                $novaSubCategoria = $ci->subCategoria_model->Buscar($novaSubCategoria);

                $data["IdCategoria"]    = $novaCategoria["IdCategoria"];  
                $data["IdSubCategoria"] = $novaSubCategoria["IdSubCategoria"];
            }
            // -- CATEGORIA SELECIONADA
            else{ 
                $data["IdCategoria"] = $ci->input->post("categoria");
                
                // ----- SUB-CATEGORIA -----
                if($ci->input->post("sub_categoria") == "nova-sub_categoria"){

                    $novaSubCategoria["DescricaoSubCategoria"]  = $ci->input->post("adiciona-sub_categoria");
                    $novaSubCategoria["IdCategoria"]            = $ci->input->post("categoria");
                    $ci->subCategoria_model->Incluir($novaSubCategoria); 

                    $novaSubCategoria = $ci->subCategoria_model->Buscar($novaSubCategoria);

                    $data["IdSubCategoria"] = $novaSubCategoria["IdSubCategoria"];
                }
                else{ 
                    $data["IdSubCategoria"] = $ci->input->post("sub_categoria");
                }
            }
         
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

        //-- Cartao --    
        if($ci->input->post("dataCompra") != ""){
            $data["DataCompra"]  		= $ci->input->post("dataCompra");
        }
        
		return $data;	
    
    }

    function ValidaEntidadeTransacao($data){
        
        if(
            (isset($data["Dia"]))&&(($data["Dia"] > 0)&&($data["Dia"] <= 31 ))
            &&
            (isset($data["Descricao"]))&&($data["Descricao"] != "")
            &&
            (isset($data["Valor"]))&&($data["Valor"] > 0)	
            &&	
            (isset($data["Ano"]))&&($data["Ano"] > 1900)
            &&
            (isset($data["Mes"]))&&(($data["Mes"] >= 1)&&($data["Mes"] <= 12))
            &&
            (isset($data["IdCategoria"]))&&($data["IdCategoria"] > 0)
            &&
            (isset($data["IdSubCategoria"]))&&($data["IdSubCategoria"] > 0)
            &&
            (isset($data["IdUsuario"]))&&($data["IdUsuario"] > 0)
            &&
            (isset($data["IdTipoTransacao"]))&&($data["IdTipoTransacao"] > 0)
        )
        {
            return true;
        }
        else{
            return false;
        }
        
    }