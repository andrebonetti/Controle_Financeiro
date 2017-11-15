<?php $msg = $this->session->flashdata('msg-error'); if (!empty($msg)){?>
    <p class="alert alert-danger"><?=$msg?></p>
<?php } ?> 

<?php $msg = $this->session->flashdata('msg-success'); if (!empty($msg)){?>
    <p class="alert alert-success"><?=$msg?></p>
<?php } ?>