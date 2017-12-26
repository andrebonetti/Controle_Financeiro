<select class="menu-competencias form-control" name="competencia">
    <?php foreach($lCompetencias as $competencia){ ?>

        <option value="<?=$competencia["Id"]?>"><?=$competencia["Ano"]?>-<?=$competencia["Mes"]?></option>

    <?php } ?>
</select>