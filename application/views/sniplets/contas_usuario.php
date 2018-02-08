<select class="menu-contas_usuario form-control" name="competencia">
    <option value="Total">Total</option>
    <?php foreach($lcontaUsuario["Contas_Banco"] as $key => $itemConta){ ?>

        <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>

    <?php }  ?>
    <option value="Geral">Geral</option>
</select>