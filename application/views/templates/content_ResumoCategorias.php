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
                        
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </thead>    
                        
                        <tbody>
                            
                            <!-- SUB CATEGORIAS - JS -->
                            <?php $sub_categoria_n = 1; foreach($sub_categorias[$categoria["DescricaoCategoria"]] as $sub_categoria){?>

                                <tr 
                                    id="sub_categoria_n<?=$sub_categoria_n?>" 
                                    class="sub_categoria-resumo" 
                                    data-id-subcategoria="<?=$sub_categoria["IdSubCategoria"]?>">
                                        <th colspan="3" class="nome_sub_categoria">
                                            <?=trim($sub_categoria["DescricaoSubCategoria"])?>
                                        </th>
                                </tr>
                                

                            <?php $sub_categoria_n++; } ?>
                            
                        </tbody>
                        
                    </table>    
                                
                            
                            <?php /*if($sub_categorias[$categoria["DescricaoCategoria"]] != NULL){?>

                            <?php foreach($sub_categorias[$categoria["DescricaoCategoria"]] as $content){?>  
                    
                                <?php $sub_categoria = valida_sub($categoria["DescricaoCategoria"],$content["DescricaoSubCategoria"])?>

                                    <!-- TOTAL -->
                                    <table>
                                        <tr><td class="valor-total-sub_categoria"></td></tr>
                                    </table>

                                </div>

                    <?php }}

                        <table class="total">
                            <tr><td class="valor-total-categoria"></td></tr>
                        </table>*/ ?>
                </div>
            
            </div> 
        </div>
    
        <?php /*CONTAGEM CATEGORIA*/ $c++; ?>
    
    <?php } ?>
    
</div>