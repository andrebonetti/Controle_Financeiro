<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Content extends CI_Controller{
		    
		public function month_content($year,$month){
			
		$this->output->enable_profiler(FALSE);
			
		/*USUARIO*/	
        $user 	 	= $this->session->userdata('usuario');  
		$usuario 	= $this->usuarios_model->busca_usuario($user); 
		
		/*TABELAS*/
        $categorias     		= $this->transacoes_model->lista_table("categoria");		
		$saldo_atual   		    = $this->geral_model->saldo($year,$month);
		$all_sub_categorias 	= $this->transacoes_model->lista_table("sub_categoria");
		
		if($month == 1){$mesAnterior = 12; $anoAnterior = $year-1;}
		else{$mesAnterior = $month-1; $anoAnterior = $year;}		
		$saldo_anterior = $this->geral_model->saldo($anoAnterior,$mesAnterior);
		
		/*DATA*/
		$days_month 	=  days_in_month($month);
		$first_day_week =  date("w", mktime(0,0,0,$month,1,$year));  
        $day_week       =  $first_day_week;   
        $atual_d        = mdate("%d");    
                  
        foreach($categorias as $categoria){    
            $sub_categorias[$categoria["nome_categoria"]] = $this->transacoes_model->lista_sub_table($categoria["id_categoria"],"sub_categoria");
        }
            
		/*CONTENT*/
            
            /* DIAS */    
            for ($n = 1; $n <= $days_month ;$n++){

                $data_day     = array();
                
                /*UNIQUE*/
                $day_unique 	= $this->transacoes_model->pay_type("3",$year,$month,$n,$usuario['id_usuario']); 
                if(!empty($day_unique)){
                    foreach($day_unique as $content){
                    array_push($data_day,$content);
                }}
				              
                    /*SABADO OU DOMINGO*/
                    if($day_week > 5){}
                
                    /*SEGUNDA*/
                    if($day_week == 1) {

                        $day_infinit_sab_temp 	= $this->transacoes_model->pay_type("1",$year,$month,$n-2,$usuario['id_usuario']);	
                        $day_infinit_sab = array();
                        
                        foreach($day_infinit_sab_temp as $temp){
                            if($year > $temp["ano"])
                            {
                                array_push($day_infinit_sab,$temp);
                            }
                            if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                            {
                                array_push($day_infinit_sab,$temp);
                            }
                        }
                        
                        $day_parcela_sab 	    = $this->transacoes_model->pay_type("2",$year,$month,$n-2,$usuario['id_usuario']);

                        $day_infinit_dom_temp 	= $this->transacoes_model->pay_type("1",$year,$month,$n-1,$usuario['id_usuario']);				
                        $day_parcela_dom 	    = $this->transacoes_model->pay_type("2",$year,$month,$n-1,$usuario['id_usuario']);	
                        
                        $day_infinit_dom = array();
                            
                            foreach($day_infinit_dom_temp as $temp){
                                if($year > $temp["ano"])
                                {
                                    array_push($day_infinit_dom,$temp);
                                }
                                if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                                {
                                    array_push($day_infinit_dom,$temp);
                                }
                            }

                        if(!empty($day_infinit_sab)){
                            foreach($day_infinit_sab as $content){      
                            array_push($data_day,$content);
                        }}
                        if(!empty($day_parcela_sab)){
                            foreach($day_parcela_sab as $content){
                            array_push($data_day,$content);
                        }} 
                        if(!empty($day_infinit_dom)){
                            foreach($day_infinit_dom as $content){      
                            array_push($data_day,$content);
                        }}
                        if(!empty($day_parcela_dom)){
                            foreach($day_parcela_dom as $content){
                            array_push($data_day,$content);
                        }} 
                    }
                
                    /*RESTO SEMANAS*/
                    if($day_week <= 5)  {
                        //echo "Resto Semana";
                                
                        $day_infinit_temp 	    = $this->transacoes_model->pay_type("1",$year,$month,$n,$usuario['id_usuario']);				
                        $day_parcela 	        = $this->transacoes_model->pay_type("2",$year,$month,$n,$usuario['id_usuario']);
                        
                        $day_infinit = array();
                        
                        foreach($day_infinit_temp as $temp){
                            if($year > $temp["ano"])
                            {
                                array_push($day_infinit,$temp);
                            }
                            if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                            {
                                array_push($day_infinit,$temp);
                            }
                        }
                        
                        
                        if(!empty($day_infinit)){
                            foreach($day_infinit as $content){      
                            array_push($data_day,$content);
                        }}
                        if(!empty($day_parcela)){
                            foreach($day_parcela as $content){
                            array_push($data_day,$content);
                        }}
                        
                    }
					
					if(($days_month < 31) && ($n == 30))
					{
						$day_infinit_temp 	    = $this->transacoes_model->pay_type("1",$year,$month,$n+1,$usuario['id_usuario']);				
                        $day_parcela 	        = $this->transacoes_model->pay_type("2",$year,$month,$n+1,$usuario['id_usuario']);	
                        
                        $day_infinit = array();
                        
                        foreach($day_infinit_temp as $temp){
                            if($year > $temp["ano"])
                            {
                                array_push($day_infinit,$temp);
                            }
                            if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                            {
                                array_push($day_infinit,$temp);
                            }
                        }
						
						if(!empty($day_infinit)){
                            foreach($day_infinit as $content){      
                            array_push($data_day,$content);
                        }}
                        if(!empty($day_parcela)){
                            foreach($day_parcela as $content){
                            array_push($data_day,$content);
                        }}
					}
					
					if( 
						(($days_month == 31) && ($n == 30))
						or
						(($days_month == 30) && ($n == 29))
					  )	
					{
						
						if($day_week == 5){
							
							$day_infinit_sab_temp 	= $this->transacoes_model->pay_type("1",$year,$month,$n+2,$usuario['id_usuario']);				
                        	$day_parcela_sab 	    = $this->transacoes_model->pay_type("2",$year,$month,$n+2,$usuario['id_usuario']);
                            
                            $day_infinit_sab = array();
                        
                            foreach($day_infinit_sab_temp as $temp){
                                if($year > $temp["ano"])
                                {
                                    array_push($day_infinit_sab,$temp);
                                }
                                if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                                {
                                    array_push($day_infinit_sab,$temp);
                                }
                            }
                            
                        	$day_infinit_dom_temp 	= $this->transacoes_model->pay_type("1",$year,$month,$n+1,$usuario['id_usuario']);				
                        	$day_parcela_dom 	    = $this->transacoes_model->pay_type("2",$year,$month,$n+1,$usuario['id_usuario']);		
                            
                            $day_infinit_dom = array();
                            
                            foreach($day_infinit_dom_temp as $temp){
                                if($year > $temp["ano"])
                                {
                                    array_push($day_infinit_dom,$temp);
                                }
                                if(($year == $temp["ano"])&&($month >= $temp["mes"]))
                                {
                                    array_push($day_infinit_dom,$temp);
                                }
                            }
							
							if(!empty($day_infinit_sab)){
	                            foreach($day_infinit_sab as $content){      
	                            array_push($data_day,$content);
	                        }}
	                        if(!empty($day_parcela_sab)){
	                            foreach($day_parcela_sab as $content){
	                            array_push($data_day,$content);
	                        }} 
	                        if(!empty($day_infinit_dom)){
	                            foreach($day_infinit_dom as $content){      
	                            array_push($data_day,$content);
	                        }}
	                        if(!empty($day_parcela_dom)){
	                            foreach($day_parcela_dom as $content){
	                            array_push($data_day,$content);
	                        }} 
						}
					}
                
                $data_month["dia-".$n] = $data_day;
                
                if($day_week == 7){$day_week = 1;}
                else{$day_week++;}
            }
          
            /*UNDEFINED*/$data_undefined = $this->transacoes_model->pay_type("4",$year,$month,$n,$usuario['id_usuario']);	
            
            /*CARTAO*/
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
		"saldo_anterior"		=> $saldo_anterior,
		"saldo_atual"			=> $saldo_atual,       
		"usuario"		  		=> $usuario,
		"ano"			  		=> $year,
		"mes"			  		=> $month,
        "hoje"                  => $atual_d,
		"categorias"	  		=> $categorias,
		"all_sub_categorias"  	=> $all_sub_categorias,
		"n"		 	      		=> "1",
        "fatura_cartao"   		=> $data_cartao,
        "categorias"      		=> $categorias,
        "sub_categorias"  		=> $sub_categorias,      
        "first_day"       		=> $first_day_week, 
        "last_day"       		=> $n - 1, 
        "data_undefined"  		=> $data_undefined,    
		"data_month"      		=> $data_month);
		
		/*VIEW*/$this->load->template("content.php",$content);
            
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