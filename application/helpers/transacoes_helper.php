<?php

    function transacao_getPosts(){
        
		$ci = get_instance();
        
        if($ci->input->post("dia")  != ""){$data["Dia"] 		= $ci->input->post("dia");}
        $data["Descricao"] 	        = $ci->input->post("descricao");	
        $data["Valor"]		        = valor_decimal($ci->input->post("valor"));		
        $data["Ano"]                = $ci->input->post("ano");	
        $data["Mes"]  		        = $ci->input->post("mes");
        $data["CodigoTransacao"]    = $ci->input->post("codigoTransacao");
        $data["NumeroParcela"]      = $ci->input->post("numero-parcela");

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
        if($ci->input->post("id-tipo-transacao")){
            $data["IdTipoTransacao"] = $ci->input->post("id-tipo-transacao");

            if($data["IdTipoTransacao"] != 3){
                $data["espelhar-proximas"] = $ci->input->post("espelhar-proximas");
            }
        }
        else{

            $data["IdTipoTransacao"] = 3;

            //Recorrente
            if($ci->input->post("isRecorrente") == 1)
            {
                $data["IdTipoTransacao"] = 1;

                $data["espelhar-proximas"] = $ci->input->post("espelhar-proximas");
            }
            else{
                //Parcelada 
                if($ci->input->post("totalParcelas") > 0)
                {
                    $data["IdTipoTransacao"]    = 2;
                    $data["TotalParcelas"] = $ci->input->post("totalParcelas");

                    $data["espelhar-proximas"] = $ci->input->post("espelhar-proximas");
                }
            }
        }

        //-- Cartao --    
        if($ci->input->post("dataCompra") != ""){
            $data["DataCompra"]  		= dataPtBrParaMysql($ci->input->post("dataCompra"));
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

    function buscarTransacoesPorTipo($pTipo,$pParam){

        $ci = get_instance();

        $pParam["IdTipoTransacao"]    = $pTipo;
        if($pTipo == 3){
            return $ci->transacoes_model->Listar($pParam);
        }
        if($pTipo == 1 || $pTipo == 2 || $pTipo == 4){

            $diaSemana   = date("w", mktime(0,0,0,$pParam["Mes"],$pParam["Dia"],$pParam["Ano"])); 
            $qtdeDiasMes =  days_in_month($pParam["Mes"]);
            $dataDia     = array();
            $diaParam    = $pParam["Dia"];
 
            //Transações do FDS caem na Segunda
            if($diaSemana == 1) {
                
                //SABADO
                $pParam["Dia"] = $diaParam - 2;   
                        
                foreach($ci->transacoes_model->Listar($pParam) as $itemContent){
                    array_push($dataDia,$itemContent);
                }
                        
                //DOMINGO
                $pParam["Dia"] = $diaParam - 1;   
                        
                foreach($ci->transacoes_model->Listar($pParam) as $itemContent){
                    array_push($dataDia,$itemContent);
                }

            }

            //Transacoes Final do Mes no ultimo dias
            if($pParam["Dia"] == $qtdeDiasMes){

            //if(($diaSemana == 5)&&( ($pParam["Dia"] == 29)||($pParam["Dia"] == 30) )) {
                
                //SABADO
                $pParam["Dia"] = $diaParam + 1;   
                        
                foreach($ci->transacoes_model->Listar($pParam) as $itemContent){
                    array_push($dataDia,$itemContent);
                }
                        
                //DOMINGO
                $pParam["Dia"] = $diaParam + 2;   
                        
                foreach($ci->transacoes_model->Listar($pParam) as $itemContent){
                    array_push($dataDia,$itemContent);
                }

            }
                        
            // -- SEGUNDA A SEXTA 
            if(($diaSemana >= 1)&&($diaSemana <= 5))
            {
                $pParam["Dia"] = $diaParam;  

                foreach( $ci->transacoes_model->Listar($pParam) as $itemContent){
                    array_push($dataDia,$itemContent);
                }
            }

            return $dataDia;

        }
                           

    }