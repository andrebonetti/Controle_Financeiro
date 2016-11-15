<section class="content">
	<div class="myContainer">
        
        <?php $msg = $this->session->flashdata('msg-error'); if (!empty($msg)){?>
            <p class="alert alert-danger"><?=$msg?></p>
        <?php } ?> 
        
        <?php $msg = $this->session->flashdata('msg-success'); if (!empty($msg)){?>
            <p class="alert alert-success"><?=$msg?></p>
        <?php } ?>
		        
   		<div class="saldos">
        	
            <div class="saldo-anterior" value="<?=$saldo_anterior["SaldoFinal"]?>">Saldo Anterior: <span><?=numeroEmReais2($saldo_anterior["SaldoFinal"])?></span> </div>
            <div class="saldo-mes <?=sinal_valor($saldo_atual["SaldoMes"])?>" value="<?=$saldo_atual["SaldoMes"]?>">Saldo do Mes: <span><?=numeroEmReais2($saldo_atual["SaldoMes"])?></span> </div>
            <div class="saldo-dia btn btn-default ">
                <p>Saldo do dia</p>
                <input type="text" class="dia_change form-control" value="<?=$hoje?>" maxlength="2">
                <p>:</p>
                <span class="saldo-value"></span>
            </div>
            <div class="saldo-futuro" value="<?=$saldo_atual["SaldoFinal"]?>">Saldo Futuro: <span><?=numeroEmReais2($saldo_atual["SaldoFinal"])?></span> </div>
            
        </div>
        
        <div class="poupanca_content">
        	
            <div class="poup-anterior" value="<?=$saldo_anterior["PoupancaFinal"]?>">Poupança Anterior: <span><?=numeroEmReais2($saldo_anterior["PoupancaFinal"])?></span> </div>
            <div class="poup-mes"  value="<?=$saldo_atual["Poupanca"]?>">Poupança do Mes:  <span><?=numeroEmReais2($saldo_atual["PoupancaFinal"])?></span> </div>
            <div class="poup-futuro" value="<?=$saldo_atual["PoupancaFinal"]?>">Poupança Final: <span><?=numeroEmReais2($saldo_atual["PoupancaFinal"])?></span> </div>
            
        </div>
        
		<div class="calendario">
            
            <div class="header-calendario">
                <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
                <p class="despesas">Despesas: <?=numeroEmReais2($saldo_atual["Despesas"])?></p>
                <p class="receita">Receita: <?=numeroEmReais2($saldo_atual["Receita"])?></p>               
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
                        
                            <?php $sub_categoria = valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>
                        
                            <tr class="content-day content-<?=no_acento_code($sub_categoria)?>" data-toggle="modal" data-target="#edit-transacao">  
                                
	                            <td class="no-view id"><?=$content["Id"]?></td>   
	                            <td class="no-view type"><?=$content["IdTipoTransacao"]?></td>       
	                            <td class="no-view dia-atual"><?=$content["Dia"]?></td>    
	                            <td class="no-view p_total-atual"><?=$content["TotalParcelas"]?></td>    
	                            <td class="no-view categoria" value="<?=$content["IdCategoria"]?>"><?=$content["DescricaoCategoria"]?></td>
	                            <td class="no-view sub_categoria" value="<?=$content["IdSubCategoria"]?>"><?=valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?></td>
                                
                                <td class="descricao" value="<?=$content["Descricao"]?>">
                                    <?php if($content["Descricao"] == NULL){?>
                                        <?=$content["DescricaoSubCategoria"]?>
                                    <?php } else{ ?>
                                        <?=$content["Descricao"]?>
                                        <?php if($content["IdTipoTransacao"] == "2"){?>
                                            - <?=$content["NumeroParcela"]?>/<?= $content["TotalParcelas"] ?>	
                                    <?php }} ?>
                                </td>
                                
                                <td class="valor <?=sinal_valor($content["Valor"])?>" value="<?=$content["Valor"]?>"><?=numeroEmReais2($content["Valor"])?></td>
                                
                            </tr>
                        
                        <?php } ?>
                        
                        <!-- RESUMO DIA -->
                        
                        
                        <?php if($n == 9){?>
                            <tr class="cartao">
                                <td class='cartao'>Cartão</td>
                                <td class='valor-fatura valor' value="<?=-$saldo_atual["Cartao"]?>"><?=numeroEmReais2(-$saldo_atual["Cartao"])?></td>
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

                        <?php $sub_categoria = valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>

                            <tr class="content-fatura content-<?=no_acento_code($sub_categoria)?>" data-toggle="modal" data-target="#edita-cartao"> 
								
								<td class="no-view id"><?=$content["Id"]?></td>   
	                            <td class="no-view type"><?=$content["IdTipoTransacao"]?></td>        
	                            <td class="no-view categoria" value="<?=$content["IdCategoria"]?>"><?=$content["DescricaoCategoria"]?></td>
	                            <td class="no-view sub_categoria" value="<?=$content["IdSubCategoria"]?>"><?=valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?></td>
                                <td class="no-view p_total-atual"><?=$content["TotalParcelas"]?></td>    
	                            <td class="no-view IdTipoTransacaoCartao-atual"><?=$content["IdTipoTransacao"]?></td>    
	                            
                                <td class="compra dataCompra-atual"><?=dataMysqlParaPtBr($content["DataCompra"])?></td>
                                <td class="descricao" value="<?=$content["Descricao"]?>">
                                    <?php if($content["Descricao"] == NULL){?>
                                    <?=$content["DescricaoSubCategoria"]?>
                                    <?php } else{ ?>
                                    <?=$content["Descricao"]?>
                                    <?php if($content["IdTipoTransacao"] == "2"){?>
                                        - <?=$content["NumeroParcela"]?>/<?= $content["TotalParcelas"] ?>	
                                    <?php }} ?>
                                </td>
                                <td class="valor" value="<?=numeroEmReais2($content["Valor"])?>"><?=numeroEmReais2($content["Valor"])?></td>

                            </tr>

                        <?php } ?>

                        <tr>
                            <td colspan="3" class="valor-total"><?=numeroEmReais2(-$saldo_atual["Cartao"])?></td>
                        </tr>

                    </table> 
                    
                </div>
                
            </div>
            
            <?php /*CALCULO CATEGORIA*/ $c = 1 ?>
         
            <?php foreach($categorias as $categoria){?>
            
                <div class="categoria-resumo resumo-<?=$categoria["DescricaoCategoria"]?> <?=$c?> <?php if($c%3 == "0"){echo "new-line";}?>">
                    
                    <div class="categoria-content">
                    
                        <h2><?=$categoria["DescricaoCategoria"]?></h2>

                        <table>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </table> 

                        <!-- SUB CATEGORIAS -->
                        <?php if($sub_categorias[$categoria["DescricaoCategoria"]] == NULL){?>

                            <div> 
                                <table>

                                    <tr>
                                        <th colspan="3" class="nome_sub_categoria" name="<?=$categoria["DescricaoCategoria"]?>"><?=$categoria["DescricaoCategoria"]?></th>
                                    </tr>

                                </table> 
                            </div>

                        <?php } ?>

                        <?php if($sub_categorias[$categoria["DescricaoCategoria"]] != NULL){?>

                            <?php foreach($sub_categorias[$categoria["DescricaoCategoria"]] as $content){?>                   

                                    <?php $sub_categoria = valida_sub($categoria["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>

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