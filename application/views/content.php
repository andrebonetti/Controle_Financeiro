<section class="content">
	<div class="myContainer">
		        
   		<div class="saldos">
        	
            <div class="saldo-anterior" value="<?=$saldo_anterior["saldo_final"]?>">Saldo Anterior: <span><?=numeroEmReais2($saldo_anterior["saldo_final"])?></span> </div>
            <div class="saldo-mes <?=sinal_valor($saldo_atual["saldo_mes"])?>" value="<?=$saldo_atual["saldo_mes"]?>">Saldo do Mes: <span><?=numeroEmReais2($saldo_atual["saldo_mes"])?></span> </div>
            <div class="saldo-dia btn btn-default ">
                <p>Saldo do dia</p>
                <input type="text" class="dia_change form-control" value="<?=$hoje?>">
                <p>:</p>
                <span class="saldo-value"></span>
            </div>
            <div class="saldo-futuro" value="<?=$saldo_atual["saldo_final"]?>">Saldo Futuro: <span><?=numeroEmReais2($saldo_atual["saldo_final"])?></span> </div>
            
        </div>
        
        <div class="poupanca_content">
        	
            <div class="poup-anterior" value="<?=$saldo_anterior["poupanca_final"]?>">Poupança Anterior: <span><?=numeroEmReais2($saldo_anterior["poupanca_final"])?></span> </div>
            <div class="poup-mes"  value="<?=$saldo_atual["poupanca"]?>">Poupança do Mes:  <span><?=numeroEmReais2($saldo_atual["poupanca"])?></span> </div>
            <div class="poup-futuro" value="<?=$saldo_atual["poupanca_final"]?>">Poupança Final: <span><?=numeroEmReais2($saldo_atual["poupanca_final"])?></span> </div>
            
        </div>
        
		<div class="calendario">
            
            <div class="header-calendario">
                <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
                <p class="despesas">Despesas: <?=numeroEmReais2($saldo_atual["despesas"])?></p>
                <p class="receita">Receita: <?=numeroEmReais2($saldo_atual["receita"])?></p>               
            </div>
            
            
            <!-- SEMANAS -->
            <table class="semanas">           
                <tr>
                    <th>Segunda-Feira</th>  
                    <th>Terça-Feira</th> 
                    <th>Quarta-Feira</th> 
                    <th>Quinta-Feira</th> 
                    <th>Sexta-Feira</th> 
                    <th>Sabado</th>
                    <th>Domingo</th>
                </tr>    
            </table>
                    
            <!-- DIAS -->
            <div class="dias">
            
            <?php 
                /*-- CALCULO SEMANA --*/
                $s = 1;
                for($pre=0;$pre < $first_day-1;$pre++){?> 
                    <?php $semana = calcula_semana($s);?>
                    <div class="pre-nulo"></div>
                    <?php $s++; ?>
                <?php } 
                
                /*-- FOREACH DIAS MES --*/
			    foreach($data_month as $data_day) {?>
                
                <?php $semana = calcula_semana($s);?>
                <div class="dia dia-<?=$n?> ds-<?=$first_day?> <?php if($first_day == 1){echo "new-line";}?>" id="dia-<?=$n?>" name="<?=$first_day?>" >
                    
                    <table name="<?=$n?>">
                        
                        <!-- INFO DIA -->
                        <tr class="data-day" data-toggle="modal" data-target="#add-transacao">
                            
                            <th colspan="2" class="dia_mes"><?=$n?></th>
                            
                            <span class="semana_mes-<?=$semana?>"></span>
                            <span class="dia_semana-<?=$first_day?>"></span>
                            
                        </tr>

                        <!-- CONTEUDO DIA -->
                        <?php foreach($data_day as $content){?> 
                        
                            <?php $sub_categoria = valida_sub($content["nome_categoria"],$content["nome_sub_categoria"])?>
                        
                            <tr class="content-day content-<?=no_acento_code($sub_categoria)?>" data-toggle="modal" data-target="#edit-transacao">  
                                
	                            <td class="no-view id"><?=$content["id"]?></td>   
	                            <td class="no-view type"><?=$content["type"]?></td>       
	                            <td class="no-view dia-atual"><?=$content["dia"]?></td>    
	                            <td class="no-view categoria" value="<?=$content["categoria"]?>"><?=$content["nome_categoria"]?></td>
	                            <td class="no-view sub_categoria" value="<?=$content["sub_categoria"]?>"><?=valida_sub($content["nome_categoria"],$content["nome_sub_categoria"])?></td>
                                
                                <td class="descricao" value="<?=$content["descricao"]?>">
                                    <?php if($content["descricao"] == NULL){?>
                                        <?=$content["nome_sub_categoria"]?>
                                    <?php } else{ ?>
                                        <?=$content["descricao"]?>
                                        <?php if($content["type"] == "2"){?>
                                            - <?=$content["parcela"]?>/<?= $content["p_total"] ?>	
                                    <?php }} ?>
                                </td>
                                
                                <td class="valor <?=sinal_valor($content["valor"])?>" value="<?=$content["valor"]?>"><?=numeroEmReais2($content["valor"])?></td>
                                
                            </tr>
                        
                        <?php } ?>
                        
                        <!-- RESUMO DIA -->
                        
                        
                        <?php if($n == 9){?>
                            <tr class="cartao">
                                <td class='cartao'>Cartão</td>
                                <td class='valor-fatura valor' value="<?=-$saldo_atual["cartao"]?>"><?=numeroEmReais2(-$saldo_atual["cartao"])?></td>
                            </tr>
                        <?php } ?>
                        
                    </table>
                    
                    <div class="valor-total"></div>

                </div> 
                
                <?php 
                                                   
                    /*CONTAGEM DIA*/    $n++; 
                    /*CONTAGEM SEMANA*/ $s++; 
                    
                    /*CONTAGEM DIA DA SEMANA*/                               
                    if($first_day == 7){ $first_day = "1";}                              
                    else{$first_day++;} 
                ?>

			<?php } ?>
            
        </div>        
    </div>
        
        <div class="ativa-categoria">
            
                <div class="mostra-categoria">
                    <h5>Mostrar Categorias</h5>
                    <img src="<?=base_url("img/seta_baixo.png")?>">
                </div>
                    
                <div class="oculta-categoria no-view">
                    <h5>Oculta Categorias</h5>
                    <img src="<?=base_url("img/seta_cima.png")?>">
                </div>
                    
        </div>
        
        <!-- CATEGORIAS -->
        <div class="categorias no-view">
            
            <!-- FATURA CARTAO -->
            <div class="fatura-cartao">
                
                <div class="categoria-content">
                	
                    <h2>Cartão de Crédito</h2>
					
					<button class="btn btn-primary" data-toggle="modal" data-target="#add-cartao">Adicionar Contao</button>
                
                    <table>

                        <tr>
                            <th>Data da Compra</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>

                        <?php foreach($fatura_cartao as $content){?>

                        <?php $sub_categoria = valida_sub($content["nome_categoria"],$content["nome_sub_categoria"])?>

                                <tr class="content-fatura content-<?=no_acento_code($sub_categoria)?>" data-toggle="modal" data-target="#edita-cartao"> 
								
								<td class="no-view id"><?=$content["id"]?></td>   
	                            <td class="no-view type"><?=$content["type"]?></td>        
	                            <td class="no-view categoria" value="<?=$content["categoria"]?>"><?=$content["nome_categoria"]?></td>
	                            <td class="no-view sub_categoria" value="<?=$content["sub_categoria"]?>"><?=valida_sub($content["nome_categoria"],$content["nome_sub_categoria"])?></td>
                            
                                <td class="compra"><?=dataMysqlParaPtBr($content["data_compra"])?></td>
                                <td class="descricao" value="<?=$content["descricao"]?>">
                                    <?php if($content["descricao"] == NULL){?>
                                    <?=$content["nome_sub_categoria"]?>
                                    <?php } else{ ?>
                                    <?=$content["descricao"]?>
                                    <?php if($content["type"] == "2"){?>
                                        - <?=$content["parcela"]?>/<?= $content["p_total"] ?>	
                                    <?php }} ?>
                                </td>
                                <td class="valor" value="<?=$content["valor"]?>"><?=numeroEmReais2($content["valor"])?></td>

                            </tr>

                        <?php } ?>

                        <tr>
                            <td colspan="3" class="valor-total"><?=numeroEmReais2(-$saldo_atual["cartao"])?></td>
                        </tr>

                    </table> 
                    
                </div>
                
            </div>
            
            <?php /*CALCULO CATEGORIA*/ $c = 1 ?>
         
            <?php foreach($categorias as $categoria){?>
            
                <div class="categoria-resumo resumo-<?=$categoria["nome_categoria"]?> <?=$c?> <?php if($c%3 == "0"){echo "new-line";}?>">
                    
                    <div class="categoria-content">
                    
                        <h2><?=$categoria["nome_categoria"]?></h2>

                        <table>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </table> 

                        <!-- SUB CATEGORIAS -->
                        <?php if($sub_categorias[$categoria["nome_categoria"]] == NULL){?>

                            <div> 
                                <table>

                                    <tr>
                                        <th colspan="3" class="nome_sub_categoria" name="<?=$categoria["nome_categoria"]?>"><?=$categoria["nome_categoria"]?></th>
                                    </tr>

                                </table> 
                            </div>

                        <?php } ?>

                        <?php if($sub_categorias[$categoria["nome_categoria"]] != NULL){?>

                            <?php foreach($sub_categorias[$categoria["nome_categoria"]] as $content){?>                   

                                    <?php $sub_categoria = valida_sub($categoria["nome_categoria"],$content["nome_sub_categoria"])?>

                                    <div class="sub_categoria-resumo sub_resumo-<?=no_acento_code($sub_categoria)?>"> 

                                        <table class="content">
                                            <tr>
                                                <th colspan="3" class="nome_sub_categoria" name="<?=no_acento_code($sub_categoria)?>"><?=no_acento_code($sub_categoria)?></th>
                                            </tr>
                                        </table> 

                                        <table>
                                            <tr><td class="valor-total-sub_categoria"></td></tr>
                                        </table>

                                    </div>

                        <?php }} ?>

                            <table class="total">
                                <tr><td class="valor-total-categoria"></td></tr>
                            </table>
                    </div>
                    
                </div> 
            
                <?php /*CONTAGEM CATEGORIA*/ $c++; ?>
            
            <?php } ?>
            
        </div>
        
        <div class="calendario-geral">
            
            <div class="anterior">
                <?=anchor("content/month_content/".calendario("link","anterior",3,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",3,$ano,$mes)."</h3>")?>
                <?=anchor("content/month_content/".calendario("link","anterior",2,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",2,$ano,$mes)."</h3>")?>
                <?=anchor("content/month_content/".calendario("link","anterior",1,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",1,$ano,$mes)."</h3>")?>
            </div>
        
            <div class="geral">
                <?=anchor("content/geral","<img src='".base_url('img/Calendario_geral.png')."'><h3>Geral</h3>")?>
            </div>
            
            <div class="proximo">
                <?=anchor("content/month_content/".calendario("link","proximo",1,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",1,$ano,$mes)."</h3>")?>
                <?=anchor("content/month_content/".calendario("link","proximo",2,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",2,$ano,$mes)."</h3>")?>
                <?=anchor("content/month_content/".calendario("link","proximo",3,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",3,$ano,$mes)."</h3>")?>
            </div>
        
        </div>
        
	</div>
</section>