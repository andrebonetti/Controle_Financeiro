<section class="signin">

	<?php $msg = $this->session->flashdata('msg-error'); if (!empty($msg)){?>
		<p class="alert alert-danger"><?=$msg?></p>
	<?php } ?>  
	
	<?= form_open("login/validacao",array( "class" => "login") ) ?>
	    <h2 class="form-signin-heading">Controle Financeiro</h2>
        <input type="text" class="form-control" placeholder="Usuario" name="usuario" required autofocus>
        <input type="password" class="form-control" placeholder="Senha" name="senha" required>
        <input type="submit" class="btn btn-lg btn-success" value="Entrar">
	<?= form_close()  ?>
	
</section>