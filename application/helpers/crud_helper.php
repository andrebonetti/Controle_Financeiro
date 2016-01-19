<?php

	function nova_categoria(){
		$ci = get_instance();
		
		$nova_categoria	= $ci->input->post("adiciona-categoria"); 		
		if(!empty($nova_categoria)){
        	$categoria["nome_categoria"] = $nova_categoria;	
			/*BD-CRUD*/$ci->crud_model->insert("categoria",$categoria);
        }
		
		return $nova_categoria;
	}
	
	function nova_sub_categoria(){
		$ci = get_instance();
		
		$nova_categoria		= $ci->input->post("adiciona-categoria"); 
		$nova_sub_categoria	= $ci->input->post("adiciona-sub_categoria");
		$id_nova_categoria 	= $ci->transacoes_model->get_id_categoria($nova_categoria);
		
        if(!empty($nova_sub_categoria)){
        	$categoria_relacionada = $ci->input->post("categoria-sub");
                
            if($categoria_relacionada == "categoria-nova"){$sub_categoria["categoria"] = $id_nova_categoria["id_categoria"];}
            if($categoria_relacionada != "categoria-nova"){$sub_categoria["categoria"] = $categoria_relacionada;}
                
            $sub_categoria["nome_sub_categoria"] = $nova_sub_categoria;
            /*BD-CRUD*/$ci->crud_model->insert("sub_categoria",$sub_categoria);
        }
		
		return $nova_sub_categoria;	
	}

	function crud_transacao($type){
		$ci = get_instance();
		
			/*PADRAO*/
			$data["dia"] 			= $ci->input->post("dia");
			$data["usuario"]		= $ci->input->post("usuario");
			$data["descricao"] 		= $ci->input->post("descricao");	
			$data["valor"]			= valor_decimal($ci->input->post("valor"));		
			
				/*CATEGORIA*/						   							       	
				if(!empty($nova_categoria)){$data["categoria"] = $id_nova_categoria["id_categoria"];}
            	if(empty($nova_categoria)){ $data["categoria"] = $ci->input->post("categoria");}
				
				/*SUB_CATEGORIA*/
				if(!empty($nova_sub_categoria)){$data["sub_categoria"] = $id_nova_sub_categoria["id_sub_categoria"];}
                if(empty($nova_sub_categoria)){ $data["sub_categoria"] = $ci->input->post("sub_categoria");}
			
			/*TYPE*/
			if(empty($type))		{$data["type"] = "3";}
			if(empty($data["dia"]))	{$data["type"] = "4";} 
			else					{$data["type"] = $type;}
			
		return $data;	
	}
	
	function crud_cartao($type){
		$ci = get_instance();
		
		/*PADRAO*/
		$data["usuario"]	= $ci->input->post("usuario");
		$data["data_compra"]= dataPtBrParaMysql($ci->input->post("data_compra"));
		$data["descricao"] 	= $ci->input->post("descricao");			
		$data["valor"]		= valor_decimal($ci->input->post("valor"));	
		
		/*CATEGORIA*/						   							       	
			if(!empty($nova_categoria)){$data["categoria"] = $id_nova_categoria["id_categoria"];}
            if(empty($nova_categoria)){ $data["categoria"] = $ci->input->post("categoria");}
				
			/*SUB_CATEGORIA*/
			if(!empty($nova_sub_categoria)){$data["sub_categoria"] = $id_nova_sub_categoria["id_sub_categoria"];}
            if(empty($nova_sub_categoria)){ $data["sub_categoria"] = $ci->input->post("sub_categoria");}	
		
		/*TYPE*/
		if(empty($type))		{$data["type"] = "3";}
		else					{$data["type"] = $type;}
				   				       				
		return $data;	
	}
	
	function crud_geral_cartao($ano,$mes,$valor){
		$ci = get_instance();
		
		$saldo_atual  = 	$ci->geral_model->saldo($ano,$mes);			
		
		$data["cartao"] = $saldo_atual["cartao"] - $valor; 
		
		/*BD-CRUD*/$ci->crud_model->update_mes($ano,$mes,$data);	
	}
	
	function crud_geral($ano,$mes,$categoria,$valor){
		$ci = get_instance();
		
		$lista_geral  = 	$ci->geral_model->lista_geral($ano,$mes);		
		$saldo_atual  = 	$ci->geral_model->saldo($ano,$mes);	
		$salario	  =     $saldo_atual['salario'];
		echo "saldo antigo - ".$saldo_atual['saldo_mes']."<br>";
		echo "salario antigo - ".$salario."<br>";
		
		//SALARIO
		if($categoria == "1"){
			$total_salario  		= $salario + $valor;
			$data_geral["dizimo"] 	= $total_salario * 0.1;	
			$resto 					= $valor*0.9;	
			
			echo "total salario - ".$total_salario."<br>";
			echo "dizimo - ".$data_geral["dizimo"]."<br>";
			echo "resto - ".$resto."<br>";	 			
		}
		//OUTROS
		else{
			$resto			= $valor;	
			$resto_poupanca = 0;	
			echo "entrou no else - ".$resto."<br>";	
		}
			
		echo "resto teste - ".$resto."<br>";		
					
		/* --- MES --- */								
		$data_geral["saldo_mes"] = $saldo_atual['saldo_mes'] + $resto;
		echo "novo saldo mes - ".$data_geral["saldo_mes"]."<br>";
		/*BD-CRUD*/$ci->crud_model->update_mes($ano,$mes,$data_geral);
			
		/* --- FINAL --- */						
		foreach($lista_geral as $content){					
			$data_final["saldo_final"] = $content['saldo_final'] +  $resto;	
			echo "novo saldo final - ".$data_final["saldo_final"]."<br>";
			/*BD-CRUD*/$ci->crud_model->update_mes($content["ano"],$content["mes"],$data_final);	
		}

	}

	function crud_poupanca($ano,$mes,$diferenca){
		
		$ci = get_instance();
			
		$saldo_atual  = 	$ci->geral_model->saldo($ano,$mes);
		$lista_geral  = 	$ci->geral_model->lista_geral($ano,$mes);		
		
		/* --- MES --- */	
		$data_geral["poupanca"] = $saldo_atual["poupanca"] + $diferenca; 
		$data_geral["saldo_mes"] = $saldo_atual["saldo_mes"] - $diferenca;  
		 
		/*BD-CRUD*/$ci->crud_model->update_mes($ano,$mes,$data_geral);
			
		/* --- FINAL --- */						
		foreach($lista_geral as $content){					
			$data_final["saldo_final"] 	= $content["saldo_final"] - $diferenca;	
			$data_final["poupanca_final"] = $content["poupanca_final"] + $diferenca;
			/*BD-CRUD*/$ci->crud_model->update_mes($content["ano"],$content["mes"],$data_final);	
		}

	}
