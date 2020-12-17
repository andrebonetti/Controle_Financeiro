<!-- CATEGORIAS -->
<div class="categorias no-view">
    
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

                            <tr><td class="valor-total-categoria" colspan="3"></td></tr>
                            
                        </tbody>
                        
                    </table>    
                                
                </div>
            
            </div> 
        </div>
    
        <?php /*CONTAGEM CATEGORIA*/ $c++; ?>
    
    <?php } ?>
    
</div>