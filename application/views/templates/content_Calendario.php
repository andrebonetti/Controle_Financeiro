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
                        
                        <td class="info-content no-view"
                                data-dia="<?=$content["Dia"]?>"
                                data-valor="<?=$content["Valor"]?>"
                                data-descricao="<?=trim($content["Descricao"])?>"
                                >
                        </td>
                        
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