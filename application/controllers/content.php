<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($year,$month){
			
		$this->output->enable_profiler(TRUE);
           
        $dataContent["mes"] = $month;
        $dataContent["ano"] = $year;
			
		// -- USUARIO --
        $user    = $this->session->userdata('usuario');  
		$usuario = $this->usuarios_model->busca_usuario($user); 
		
        $dataContent["usuario"]        = $usuario["id_usuario"];   
        $dataContent["isListaPorTipo"] = true;
           
		// -- TABELAS --
           
            // -- CATEGORIAS --
            $lCategorias         = $this->categoria_model->Listar();	
            $lSubCategoriasTotal =  $this->subCategoria_model->Listar();	
            $lCategoriasFinal    = [];  
            foreach($lCategorias as $itemCategoria)
            {
                $pDataSubCategoria["categoria"] = $itemCategoria["id_categoria"];
		        $lSubCategorias = $this->subCategoria_model->Listar($pDataSubCategoria);
                
                $lCategoriasFinal[$itemCategoria["nome_categoria"]] = $lSubCategorias;
            }
           
            // -- SALDO --
            $saldoAtual   	= $this->geral_model->Buscar($dataContent);
		
            // -- MENU COMPETENCIAS
            if($month == 1){
                $competenciaAnterior["mes"] = 12;
                $competenciaAnterior["ano"] = $year-1;
            }
            else{
                $competenciaAnterior["mes"] = $month-1;;
                $competenciaAnterior["ano"] = $year;
            }	

            $saldoAnterior = $this->geral_model->Buscar($competenciaAnterior);

		// -- DATA --
		$qtdeDiasMes 	=  days_in_month($month);
		$primeiroDiaMes =  date("w", mktime(0,0,0,$month,1,$year));  
        $DiaAtual       =  mdate("%d");  
                
		// ---------- CONTEUDO ---------
            
            // -- DIAS --    
            for ($n = 1; $n <= $qtdeDiasMes ;$n++){
                
                $Dia_transacoesSimples             = null;
                $Dia_transacoesParceladas          = null;
                $lDia_transacoesParceladasSabado   = null;
                $lDia_transacoesParceladasDomingo  = null;
                $lDia_transacoesRecorrentes        = null;
                $lDia_transacoesRecorrentesSabado  = null;
                $lDia_transacoesRecorrentesDomingo = null;
                
                $dataContent["dia"]                      = $n;
                $diaSemana                               = date("w", mktime(0,0,0,$dataContent["mes"],$n,$dataContent["ano"]));  
                $DiaContent                              = [];
                $dataContent["PreencherEntidadesFilhas"] = true;
                    
                // -- TRANSACOES SIMPLES --
                
                    $dataContent["type"]    = 3;
                    $lDia_transacoesSimples = $this->transacoes_model->Listar($dataContent);

                    foreach($lDia_transacoesSimples as $itemContent){
                        array_push($DiaContent,$itemContent);
                    }
                
                // -- TRANSACOES PARCELADAS --
                
                    // -- SEGUNDA A SEXTA 
                    if(($diaSemana >= 1)&&($diaSemana <= 5))
                    {
                        $dataContent["type"]       = 2;
                        $lDia_transacoesParceladas = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesParceladas as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                    // -- SEGUNDA (BUSCA PARCELAS QUE CAEM EM FDS) --
                    if($diaSemana == 1) {
                        
                        // TRANSACOES SABADO
                        $dataContent["dia"]               = $n - 2;
                        $lDia_transacoesParceladasSabado  = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesParceladasSabado as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                        
                        // TRANSACOES DOMINGO
                        $dataContent["dia"]               = $n - 1;
                        $lDia_transacoesParceladasDomingo = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesParceladasDomingo as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                // -- TRANSACOES RECORRENTES --
                
                    // -- SEGUNDA A SEXTA 
                    if(($diaSemana >= 1)&&($diaSemana <= 5))
                    {
                        $dataContent["type"]        = 1;
                        $lDia_transacoesRecorrentes = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentes as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                    // -- SEGUNDA (BUSCA TRANSACOES QUE CAEM EM FDS) -- 
                    if($diaSemana == 1) {
                        
                        // TRANSACOES SABADO
                        $dataContent["dia"]                = $n - 2;
                        $lDia_transacoesRecorrentesSabado  = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentesSabado as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                        
                        // TRANSACOES DOMINGO
                        $dataContent["dia"]               = $n - 1;
                        $lDia_transacoesRecorrentesDomingo = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentesDomingo as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                $data_month["dia-".$n] = $DiaContent;
            }
					
            // -- CARTAO --
            
                $data_cartao = array();    
            
                $fatura_infinit 	= $this->cartao_model->fatura_infinit($usuario['id_usuario'],$year,$month);		
                $fatura_parcela 	= $this->cartao_model->fatura_parcela($year,$month,$usuario['id_usuario']);	
                $fatura_unique 	    = $this->cartao_model->fatura_unique($year,$month,$usuario['id_usuario']);	
            
                if(!empty($fatura_infinit)){
                    foreach($fatura_infinit as $content){
                    	$ano_fim = $content["ano_fim"];
						$mes_fim = $content["mes_fim"];
						
                    	if(($ano_fim == 0)&&($mes_fim == 0)){
                    		array_push($data_cartao,$content);
                    	}
						else{
							if(($ano_fim >= $year)&&($mes_fim >= $month)){
								array_push($data_cartao,$content);
							}
						}
                    
                }}
                if(!empty($fatura_parcela)){
                    foreach($fatura_parcela as $content){
                    array_push($data_cartao,$content);
                }}
                if(!empty($fatura_unique)){
                    foreach($fatura_unique as $content){
                    array_push($data_cartao,$content);
                }}
           	                   
		/*--------------------------CONTENT----------------------------------*/
		$content = array( 
		"saldo_anterior"		=> $saldoAnterior,
		"saldo_atual"			=> $saldoAtual,       
		"usuario"		  		=> $usuario,
		"ano"			  		=> $year,
		"mes"			  		=> $month,
        "hoje"                  => $DiaAtual,
		"categorias"	  		=> $lCategorias,
		"all_sub_categorias"  	=> $lSubCategoriasTotal,
		"n"		 	      		=> "1",
        "fatura_cartao"   		=> $data_cartao,
        "sub_categorias"  		=> $lCategoriasFinal,      
        "first_day"       		=> $primeiroDiaMes, 
        "last_day"       		=> $n - 1, 
		"data_month"      		=> $data_month);
		
		// -- VIEW --
        $this->load->template("content.php",$content);
            
	   }

	   public function geral(){
	   		
			$lista_anos = $this->geral_model->lista_distinct("ano");
			
			$data = array();
			foreach($lista_anos as $lista){
				$meses = $this->geral_model->get_where("ano",$lista["ano"]);
				
				foreach($meses as $mes){
				    $mes_data = $this->geral_model->get_where2("ano",$lista["ano"],"mes",$mes["mes"]);
				    $data[$lista["ano"]."-".$mes["mes"]] = $this->geral_model->get_where2("ano",$lista["ano"],"mes",$mes["mes"]);
			}}
		
			$content = array( 
				"anos"		=> $lista_anos,
				"data"		=> $data,
			);
		
			/*VIEW*/$this->load->template("geral.php",$content);
            	
		
	   }	
    }