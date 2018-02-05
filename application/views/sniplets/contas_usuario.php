<select class="menu-contas_usuario form-control" name="competencia">
    <option value="Total">Total</option>
    <?php foreach($lcontaUsuario as $key => $itemConta){ ?>

        <option value="<?=$key?>"><?=$itemConta["Descricao"]?></option>

    <?php } ?>
</select>