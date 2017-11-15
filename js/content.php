

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
            <div class="saldo-mes <?=sinal_valor($saldo_atual["SaldoMes"])?>" value="<?=$saldo_atual["SaldoMes"]?>">
                
                <span class="alteracao_manual" name="SaldoMes" title="Alterar" value="0">Saldo do Mes:</span>
                <span class="valor_span"><?=numeroEmReais2($saldo_atual["SaldoMes"])?></span> 
            
                <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-SaldoMes"))?>
                    
                    <input type="hidden" name="tipo_alteracao" value="SaldoMes">
                    <input type="hidden" name="ano" value="<?=$ano?>">
                    <input type="hidden" name="mes" value="<?=$mes?>">
                    <input type="hidden" name="SaldoAnterior" value="<?=$saldo_anterior["SaldoFinal"]?>">
                
                    <input type="text" class="form-control input-SaldoMes" name="valor" value="<?=numeroEmReais2($saldo_atual["SaldoMes"])?>">

                    <input type="submit" class="btn btn-success" value="Ok"> 
                    
                <?= form_close()?>
                
            </div>
            
            <div class="saldo-dia btn btn-default ">
                <p>Saldo do dia</p>
                <input type="text" class="dia_change form-control" value="<?=$hoje?>" maxlength="2">
                <p>:</p>
                <span class="saldo-value"></span>
            </div>
            
            <div class="saldo-futuro" value="<?=$saldo_atual["SaldoFinal"]?>">
                
                <span class="alteracao_manual" name="SaldoFinal" title="Alterar" value="0">Saldo Futuro:</span>
                <span class="valor_span"><?=numeroEmReais2($saldo_atual["SaldoFinal"])?></span> 
                   
                <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-SaldoFinal"))?>
                    
                    <input type="hidden" name="tipo_alteracao" value="SaldoFinal">
                    <input type="hidden" name="ano" value="<?=$ano?>">
                    <input type="hidden" name="mes" value="<?=$mes?>">
                    <input type="hidden" name="SaldoAnterior" value="<?=$saldo_anterior["SaldoFinal"]?>">
                
                    <input type="text" class="form-control input-SaldoFinal" name="valor" value="<?=numeroEmReais2($saldo_atual["SaldoFinal"])?>">

                    <input type="submit" class="btn btn-success" value="Ok"> 
                    
                <?= form_close()?>
                
            </div>
            
        </div>
        
        <!--<div class="poupanca_content">
        	
            <div class="poup-anterior" value="<?=$saldo_anterior["PoupancaFinal"]?>">Poupança Anterior: <span><?=numeroEmReais2($saldo_anterior["PoupancaFinal"])?></span> </div>
            <div class="poup-mes"  value="<?=$saldo_atual["Poupanca"]?>">Poupança do Mes:  <span><?=numeroEmReais2($saldo_atual["PoupancaFinal"])?></span> </div>
            <div class="poup-futuro" value="<?=$saldo_atual["PoupancaFinal"]?>">Poupança Final: <span><?=numeroEmReais2($saldo_atual["PoupancaFinal"])?></span> </div>
            
        </div>-->
        
		<div class="calendario">
            
            <div class="header-calendario">
                <h1><?=nome_mes($mes)?> - <?=$ano?></h1>
                <div class="despesas">
                    <p> 
                        <span class="alteracao_manual" name="despesas" title="Alterar" value="0">Despesas:</span>
                        <span class="valor_span"><?=numeroEmReais2($saldo_atual["Despesas"])?></span>
                    </p>
                    <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-despesas"))?>
                    
                        <input type="hidden" name="tipo_alteracao" value="Despesas">
                        <input type="hidden" name="ano" value="<?=$ano?>">
                        <input type="hidden" name="mes" value="<?=$mes?>">
                    
                        <input type="text" class="form-control input-despesas" name="valor" value="<?=numeroEmReais2($saldo_atual["Despesas"])?>">
                        
                         <input type="submit" class="btn btn-success" value="Ok"> 
                    
                    <?= form_close()?>
                    
                </div> 
                <div class="receita">
                    <p> 
                        <span class="alteracao_manual" name="receita" value="0">Receita:</span>
                        <span class="valor_span"><?=numeroEmReais2($saldo_atual["Receita"])?></span>
                    </p>
                    <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-receita"))?>
                    
                        <input type="hidden" name="tipo_alteracao" value="Receita">
                        <input type="hidden" name="ano" value="<?=$ano?>">
                        <input type="hidden" name="mes" value="<?=$mes?>">
                    
                        <input type="text" class="form-control input-receita" name="valor" value="<?=numeroEmReais2($saldo_atual["Receita"])?>">
                        
                         <input type="submit" class="btn btn-success" value="Ok"> 
                    
                    <?= form_close()?>
                </div> 
                
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
                <div class="dia dia-<?=$n?> ds-<?=$first_day?> <?php if($first_day == 1){echo "new-line";}?> semana_mes-<?=$semana?>" id="dia-<?=$n?>" name="<?=$first_day?>" >
                    
                    <table name="<?=$n?>">
                        
                        <!-- INFO DIA -->
                        <tr class="data-day" data-toggle="modal" data-target="#add-transacao" >
                            
                            <th colspan="2" class="dia_mes"><?=$n?></th>
                            
                            <span class="dia_semana-<?=$first_day?>"></span>
                            
                        </tr>

                        
                        <!-- CONTEUDO DIA -->
                        <?php foreach($data_day as $content){?> 
                        
                            <?php $sub_categoria = valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>
                        
                            <tr class="content-day IdSubCategoria-<?=$content["IdSubCategoria"]?>" data-toggle="modal" data-target="#edit-transacao">  
                                
                                <span class="info-content"
                                      data-valor="<?=$content["Valor"]?>"
                                      data-descricao="<?=trim($content["Descricao"])?>"
                                      >
                                </span>
                                
	                            <td class="no-view id"><?=$content["Id"]?></td>   
	                            <td class="no-view type"><?=$content["IdTipoTransacao"]?></td>       
	                            <td class="no-view dia-atual"><?=$content["Dia"]?></td>    
	                            <td class="no-view p_total-atual"><?=$content["TotalParcelas"]?></td>    
	                            <td class="no-view categoria" value="<?=$content["IdCategoria"]?>"><?=$content["DescricaoCategoria"]?></td>
	                            <td class="no-view sub_categoria" value="<?=$content["IdSubCategoria"]?>"><?=valida_sub($content["DescricaoCategoria"],$content["DescricaoSubCategoria"])?></td>
                                
                                <td class="descricao">
                                    <?php if($content["Descricao"] == NULL){?>
                                        <?=trim($content["DescricaoSubCategoria"])?>
                                    <?php } else{ ?>
                                        <?=trim($content["Descricao"])?>
                                        <?php if($content["IdTipoTransacao"] == "2"){?>
                                            - <?=$content["NumeroParcela"]?>/<?= $content["TotalParcelas"] ?>	
                                    <?php }} ?>
                                </td>
                                
                                <td class="valor <?=sinal_valor($content["Valor"])?>"><?=numeroEmReais2($content["Valor"])?></td>
                                
                            </tr>
                        
                        <?php } ?>
                        
                        <!-- RESUMO DIA -->
                        
                        <?php if(isset($DiaSaldo[$n]["SaldoFinal"])){ ?>
                        
                            <tr class="data-total_saldoDia">
                                <td class="titulo">Saldo Dia</td>
                                <td class="valorSaldo"><?=numeroEmReais2($DiaSaldo[$n]["SaldoDia"])?></td>
                            </tr>
                             <tr class="data-total_saldoFinal">
                                <td class="titulo">Saldo Final</td>
                                <td class="valorSaldo"><?=numeroEmReais2($DiaSaldo[$n]["SaldoFinal"])?></td>
                            </tr>

                        <?php } ?>
                        
                        <?php if( (($n == 9)&&($first_day < 6)) || ($n == 10 && $first_day == 1) || ($n == 11 && $first_day == 1) ) {?>
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
            <div class="box">
                
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

                        </table> 

                        <div class="total_cartao">

                            <span class="alteracao_manual valor-total" name="Cartao" title="Alterar" value="0">Alterar</span>
                            <span class="valor_span"><?=numeroEmReais2(-$saldo_atual["Cartao"])?></span> 

                            <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-Cartao"))?>

                                <input type="hidden" name="tipo_alteracao" value="Cartao">
                                <input type="hidden" name="ano" value="<?=$ano?>">
                                <input type="hidden" name="mes" value="<?=$mes?>">
                                <input type="hidden" name="ValorAnterior" value="<?=$saldo_atual["Cartao"]?>">
                                <input type="hidden" name="SaldoMes" value="<?=$saldo_atual["SaldoMes"]?>">
                                <input type="hidden" name="SaldoFinal" value="<?=$saldo_atual["SaldoFinal"]?>">

                                <input type="text" class="form-control input-Cartao" name="valor" value="<?=numeroEmReais2($saldo_atual["Cartao"])?>">

                                <input type="submit" class="btn btn-success" value="Ok"> 

                            <?= form_close()?>

                        </div>

                    </div>
                
                </div>
                
            </div>
            
            <?php /*CALCULO CATEGORIA*/ $c = 1 ?>
         
            <?php foreach($categorias as $categoria){?>
                
                <div class="box">
                    
                    <div class="categoria-resumo resumo-<?=$categoria["DescricaoCategoria"]?> <?=$c?> <?php if($c%3 == "0"){echo "new-line";}?>" data-nome-categoria="<?=$categoria["DescricaoCategoria"]?>">
                    
                        <div class="categoria-content">

                            <h2><?=$categoria["DescricaoCategoria"]?></h2>

                            <table>
                                  <tr>
                                    <th>Data</th>
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

                                        <div class="sub_categoria-resumo" data-id-subcategoria="<?=$content["IdSubCategoria"]?>"> 

                                            <table class="content">
                                                <tr>
                                                    <th colspan="3" class="nome_sub_categoria"><?=$sub_categoria?></th>
                                                </tr>
                                            </table> 
                                            
                                            <!-- TOTAL -->
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