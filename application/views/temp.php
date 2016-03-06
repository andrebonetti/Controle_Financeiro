
            
			$transacao_atual 		= $this->transacoes_model->get_where("transacoes","id",$id);
			$salario  	  			= $this->input->post("total_salario"); 
						
            $data["ano"]  		    = $this->input->post("ano");	
            $data["mes"]  		    = $this->input->post("mes");
            $data["dia"]  		    = $this->input->post("dia");
			$data["categoria"]  	= $this->input->post("categoria");
            $data["sub_categoria"]  = $this->input->post("sub_categoria");
            $data["descricao"]      = $this->input->post("descricao");
			$data["valor"]          = valor_decimal($this->input->post("valor"));	
			
			if(($data["dia"] != 0)&&($transacao_atual["type"]) == "4"){
				$data["type"] = "3";
				/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);
			}
			
			if( (int)$data["mes"] != (int)$transacao_atual["mes"]){
				
				if($transacao_atual["type"] == 1){
					$transacao_atual["type"] = 3; 
					$transacao_atual["id"] = ""; 
					var_dump($transacao_atual);
					/*BD-CRUD*/$this->crud_model->insert("transacoes",$transacao_atual);			
				}
				else{		
					/*SALDO GERAL*/crud_geral($transacao_atual["ano"],$transacao_atual["mes"],$data["categoria"],-$transacao_atual["valor"]);
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$data["valor"]);	
				}
			}
	
            if(($transacao_atual["valor"]) != $data["valor"]){

				$diferenca 		= $data["valor"] - $transacao_atual["valor"]  ;
				$lista_geral 	= $this->geral_model->lista_geral($data["ano"],$data["mes"]);
				
				echo "diferença : ".$diferenca."<br>";
				
            	if($transacao_atual["type"] == "1"){
            		
            		$data["mes"] = $data["mes"] + 1;
					$data["ano"] = $ano;
					
					if($data["mes"] > 12){
						$data["ano"] = $data["ano"] + 1;
						$data["mes"] = 1;
					}	
										
					$transacao_atual["id"] 		= "";	
					$transacao_atual["type"] 	= "3";	
					$transacao_atual["ano"] 	= $this->input->post("ano");
					$transacao_atual["mes"] 	= $this->input->post("mes");
					$transacao_atual["valor"] 	= $this->input->post("valor");	
						
					/*BD-CRUD*/$this->crud_model->insert("transacoes",$transacao_atual);

					foreach($lista_geral as $lista){
						/*SALDO GERAL*/crud_geral($lista["ano"],$lista["mes"],$data["categoria"],$diferenca);
					}	
				}
				else{
					/*SALDO GERAL*/crud_geral($data["ano"],$data["mes"],$data["categoria"],$diferenca);	
				}
            }
            
			/*BD-CRUD*/$this->crud_model->update("transacoes",$id,$data);
            /*MSG*/$this->session->set_flashdata('msg-success',"Transação alterada com sucesso!");
            /*REDIRECT*/redirect("content/month_content/".$ano."/".$mes);