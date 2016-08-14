<?php

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
