<?php

namespace Areas\Pessoa;

use Lib\Framework\Core\DBGrid;
use Lib\Framework\Helpers\Html;

$Html_Helper = new Html();

extract($this->viewBag);
?>
<h1><?= $title ?></h1>
<?php
if ($mode == 'read') {
    $dbGrid = new DBGrid();
    echo $dbGrid->createGrid(
        $model,
        array(
            'update' => BASE_URI . 'pessoa/update',
            'delete' => BASE_URI . 'pessoa/delete'
        ),
        'pessoa'
    );
?>
    <div>
        <a href="<?php echo BASE_URI . 'pessoa/createclient' ?>"><button>Criar usuário</button></a>
    </div>

    <script>
        let table = new DataTable('#tbpessoa');
    </script>
<?php

} else if ($mode == 'createClient') { ?>
    <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'pessoa/createclient' ?>">
        <?php echo $Html_Helper->getTokenInput(); ?>
        <input type="hidden" name="pes_pais" value="BR" id="inputCountry">

        <div class="form-group">
            <label class="control-label">
                <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_NOME')); ?>
            </label>
            <div class="controls">
                <input type="text" required name="pes_nome" id="inputName" placeholder="Nome completo" class="form-control" title="Preencha seu nome completo">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_EMAIL')); ?>
            </label>
            <div class="controls">
                <input type="text" name="pes_login" id="inputEmail" placeholder="Preencha seu email" class="form-control" required title="Preencha seu email">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_WHATSAPP')); ?>
            </label>
            <div class="controls">
                <input type="tel" required name="pes_telefone1" id="inputWhatsapp" placeholder="WhatsApp" class="form-control" title="Preencha seu WhatsApp">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">Sou brasileiro</label>
            <div class="controls">
                <input type="checkbox" name="pes_brasil" value="1">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_DATADENASCIMENTO')); ?></label>
            <div class="controls">
                <input type="date" name="pes_datanasc" min="0001-01-01" max="9999-12-31" id="inputDataNasc" placeholder="Nascimento" class="form-control" required title="Preencha sua Data de Nascimento">
            </div>
        </div>

        <button style="margin: 1rem 0;" type="submit">Criar</button>
    </form>

    <div>
        <a href="<?php echo BASE_URI . 'pessoa/read' ?>"><button>Ver usuários</button></a>
    </div>

<?php

} else if ($mode == 'profile') { ?>
    <form class="form profile" method="post" id="form-profile" action="<?= $formActionURL ?>">
        <div class="form-group">
            <label class="control-label">
                CODIGO CLIENTE
            </label>
            <div class="controls">
                <input type="text" name="idpessoa" value="<?php echo $model['idpessoa']; ?>" class="form-control" readonly>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                NOME COMPLETO
            </label>
            <div class="controls">
                <input type="text" name="pes_nome" value="<?php echo $model['pes_nome']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                DATA NASCIMENTO
            </label>
            <div class="controls">
                <input type="date" min="0001-01-01" max="9999-12-31" name="pes_datanasc" id="inputDataNasc" value="<?php echo $model['pes_datanasc']; ?>" max="<?= date('Y-m-d', strtotime('-18 years')) ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                CPF
            </label>
            <div class="controls">
                <input type="text" name="pes_cpf" value="<?php echo $model['pes_cpf']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Sou brasileiro</label>
            <div class="controls">
                <input type="checkbox" name="pes_brasil" <?php echo ($model['pes_brasil'] == 1) ? 'checked' : ''; ?> value="1">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                CEP
            </label>
            <div class="input-group">
                <input type="text" name="pes_cep" value="<?php echo $model['pes_cep']; ?>" id="pes_cep" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                ENDEREÇO
            </label>
            <div class="controls">
                <input type="text" name="pes_endereco" value="<?php echo $model['pes_endereco']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                NUMERO
            </label>
            <div class="controls">
                <input type="text" name="pes_numero" value="<?php echo $model['pes_numero']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                COMPLEMENTO
            </label>
            <div class="controls">
                <input type="text" name="pes_complemento" value="<?php echo $model['pes_complemento']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                BAIRRO
            </label>
            <div class="controls">
                <input type="text" name="pes_bairro" value="<?php echo $model['pes_bairro']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                CIDADE
            </label>
            <div class="controls">
                <input type="text" name="pes_cidade" value="<?php echo $model['pes_cidade']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                ESTADO
            </label>
            <div class="controls">
                <input type="text" name="pes_estado" value="<?php echo $model['pes_estado']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                PROFISSAO
            </label>
            <div class="controls">
                <input type="text" name="pes_profissao" value="<?php echo $model['pes_profissao']; ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                EMAIL
            </label>
            <div class="controls">
                <input type="text" name="pes_login" value="<?php echo $model['pes_login']; ?>" <?php echo $model['pes_login'] !== '' ? 'readonly' : ''; ?> class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">
                CELULAR
            </label>
            <div class="controls">
                <input type="tel" name="pes_telefone1" id="inputWhatsapp" value="<?php echo $model['pes_telefone1']; ?>" class="form-control">
            </div>
        </div>

    </form>

<?php

} else if ($mode == 'errorAddUser') { ?>
    <div>
        <h2> <?= $error;?> </h2>
    </div>

    <div>
        <a href="<?php echo BASE_URI . 'pessoa/read' ?>"><button>Ver usuários</button></a>
    </div>

<?php
}