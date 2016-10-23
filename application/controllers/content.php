<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
       public function month_content($year,$month){
			
		$this->output->enable_profiler(TRUE);
           
        $dataContent["Mes"] = $month;
        $dataContent["Ano"] = $year;
			
		// -- USUARIO --
        $usuarioLogado["Id"]    = $this->session->userdata('usuario');
        $usuarioLogado = $this->usuarios_model->Buscar($usuarioLogado); 
		
        $dataContent["IdUsuario"]      = $usuarioLogado["Id"];   
        $dataContent["isListaPorTipo"] = true;
           
		// -- TABELAS --
           
            // -- CATEGORIAS --
            $lCategorias         = $this->categoria_model->Listar();	
            $lSubCategoriasTotal =  $this->subCategoria_model->Listar();	
            $lCategoriasFinal    = [];  
            foreach($lCategorias as $itemCategoria)
            {
                $pDataSubCategoria["IdCategoria"] = $itemCategoria["IdCategoria"];
		        $lSubCategorias = $this->subCategoria_model->Listar($pDataSubCategoria);
                
                $lCategoriasFinal[$itemCategoria["DescricaoCategoria"]] = $lSubCategorias;
            }
           
            // -- SALDO --
            $saldoAtual   	= $this->geral_model->Buscar($dataContent);
		
            // -- MENU COMPETENCIAS
            if($month == 1){
                $competenciaAnterior["Mes"] = 12;
                $competenciaAnterior["Ano"] = $year-1;
            }
            else{
                $competenciaAnterior["Mes"] = $month-1;;
                $competenciaAnterior["Ano"] = $year;
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
                
                
                $dataContent["Dia"]                      = $n;
                $dataContent["Mes"]                      = $month;
                $dataContent["Ano"]                      = $year;
                $diaSemana                               = date("w", mktime(0,0,0,$dataContent["Mes"],$n,$dataContent["Ano"]));  
                $DiaContent                              = [];
                $dataContent["PreencherEntidadesFilhas"] = true;
                    
                // -- TRANSACOES SIMPLES --
                
                    $dataContent["IdTipoTransacao"]    = 3;
                    $lDia_transacoesSimples = $this->transacoes_model->Listar($dataContent);

                    foreach($lDia_transacoesSimples as $itemContent){
                        array_push($DiaContent,$itemContent);
                    }
                
                // -- TRANSACOES PARCELADAS --
                
                    // -- SEGUNDA A SEXTA 
                    if(($diaSemana >= 1)&&($diaSemana <= 5))
                    {
                        $dataContent["Dia"]             = $n;   
                        $dataContent["IdTipoTransacao"] = 2;
                        
                        $lDia_transacoesParceladas      = $this->transacoes_model->Listar($dataContent);
                        
                        foreach($lDia_transacoesParceladas as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                    // -- SEGUNDA (BUSCA PARCELAS QUE CAEM EM FDS) --
                    if($diaSemana == 1) {
                        
                        // TRANSACOES DOMINGO
                        if(($n - 1) == 0)
                        {
                            if($month == 1){
                                $dataContent["Mes"] = 12;
                                $dataContent["Ano"] = $year-1;
                            }
                            else{
                                $dataContent["Mes"] = $month-1;;
                                $dataContent["Ano"] = $year;
                            }
                            
                            $dataContent["Dia"] = days_in_month($dataContent["Mes"]);
                        }
                        else{
                            $dataContent["Dia"]     = $n - 1;   
                        }
                        
                        $lDia_transacoesParceladasDomingo = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesParceladasDomingo as $itemContent){
                           array_push($DiaContent,$itemContent);
                        }
                        
                        // TRANSACOES SABADO
                        if(($n - 2) <= 0)
                        {
                            if($month == 1){
                                $dataContent["Mes"] = 12;
                                $dataContent["Ano"] = $year-1;
                            }
                            else{
                                $dataContent["Mes"] = $month-1;;
                                $dataContent["Ano"] = $year;
                            }
                            
                            if(($n - 2) == -1)
                            {
                                $dataContent["Dia"] = days_in_month($dataContent["Mes"]) - 1;
                            }
                            if(($n - 2) == 0)
                            {
                                $dataContent["Dia"] = days_in_month($dataContent["Mes"]);
                            }
                        }
                        else{
                            $dataContent["Dia"]     = $n - 2;   
                        }
                        
                        $lDia_transacoesParceladasSabado  = $this->transacoes_model->Listar($dataContent);
                            foreach($lDia_transacoesParceladasSabado as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                        
                    }
                
                // -- TRANSACOES RECORRENTES --
                
                    // -- SEGUNDA A SEXTA 
                    if(($diaSemana >= 1)&&($diaSemana <= 5))
                    {
                        $dataContent["Dia"]             = $n; 
                        $dataContent["IdTipoTransacao"] = 1;
                        
                        $lDia_transacoesRecorrentes     = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentes as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                    // -- SEGUNDA (BUSCA TRANSACOES QUE CAEM EM FDS) -- 
                    if($diaSemana == 1) {
                        
                        // TRANSACOES DOMINGO
                        if(($n - 1) == 0)
                        {
                            if($month == 1){
                                $dataContent["Mes"] = 12;
                                $dataContent["Ano"] = $year-1;
                            }
                            else{
                                $dataContent["Mes"] = $month-1;;
                                $dataContent["Ano"] = $year;
                            }
                            
                            $dataContent["Dia"] = days_in_month($dataContent["Mes"]);
                        }
                        else{
                            $dataContent["Dia"]     = $n - 1;   
                        }
                        
                        $lDia_transacoesRecorrentesDomingo = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentesDomingo as $itemContent){
                           array_push($DiaContent,$itemContent);
                        }
                        
                        // TRANSACOES SABADO
                        if(($n - 2) <= 0)
                        {
                            if($month == 1){
                                $dataContent["Mes"] = 12;
                                $dataContent["Ano"] = $year-1;
                            }
                            else{
                                $dataContent["Mes"] = $month-1;;
                                $dataContent["Ano"] = $year;
                            }
                            
                            if(($n - 2) == -1)
                            {
                                $dataContent["Dia"] = days_in_month($dataContent["Mes"]) - 1;
                            }
                            if(($n - 2) == 0)
                            {
                                $dataContent["Dia"] = days_in_month($dataContent["Mes"]);
                            }
                        }
                        else{
                            $dataContent["Dia"]     = $n - 2;   
                        }
                        
                        $lDia_transacoesRecorrentesSabado  = $this->transacoes_model->Listar($dataContent);
                        foreach($lDia_transacoesRecorrentesSabado as $itemContent){
                            array_push($DiaContent,$itemContent);
                        }
                    }
                
                $data_month["dia-".$n] = $DiaContent;
                
            }
					
            // -- CARTAO --
            $data_cartao = [];
           
            $cartao_Recorrente       = $this->cartao_model->ListarFaturaRecorrente($dataContent);
            foreach($cartao_Recorrente as $itemContent){
                array_push($data_cartao,$itemContent);
            }  
           
            $cartao_SimplesParcelado = $this->cartao_model->ListarFaturaSimplesParcelada($dataContent);
            foreach($cartao_SimplesParcelado as $itemContent){
                array_push($data_cartao,$itemContent);
            }
            
              
           
		// --------------------------CONTENT----------------------------------
		$content = array( 
		"saldo_anterior"		=> $saldoAnterior,
		"saldo_atual"			=> $saldoAtual,       
		"usuario"		  		=> $usuarioLogado,
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