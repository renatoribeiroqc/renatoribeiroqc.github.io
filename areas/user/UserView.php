<?php

namespace Areas\User;

use Lib\Framework\Core\DBGrid;
use Lib\Framework\Helpers\Html;

$Html_Helper = new Html();

extract($this->viewBag);
?>
<div class="container">
    <h1><?= $title ?></h1>
    <?php
    if ($mode == 'read') {
        $dbGrid = new DBGrid();
        echo $dbGrid->createGrid(
            $model,
            array(
                'update' => BASE_URI . 'user/update',
                'delete' => BASE_URI . 'user/delete'
            ),
            'users'
        );
    ?>

        <div>
            <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'user/signup' ?>"><?=Html::text(array('label'=>'CADASTRO_LABEL_TITULO'))?></a>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_URI . 'access/read' ?>"><?=Html::text(array('label'=>'ACCESS_LABEL_TITLE'))?></a>
        </div>
        <script>
            let table = new DataTable('#tbusers');
        </script>

    <?php

    } else if ($mode == 'create') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'user/create' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>
            <input type="hidden" name="pes_pais" value="BR" id="inputCountry">

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_NOME')); ?>
                </label>
                <div class="controls">
                    <input type="text" required name="pes_nome" id="inputName" 
                    placeholder="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_PLACEHOLDER_NOME')); ?>" class="form-control" 
                    title="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_TITLE_NOME')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_EMAIL')); ?>
                </label>
                <div class="controls">
                    <input type="text" name="pes_login" id="inputEmail" 
                    placeholder="<?php echo $Html_Helper->label(array('label' => 'CADASTRO_PLACEHOLDER_EMAIL')); ?>" class="form-control" required 
                    title="<?php echo $Html_Helper->label(array('label' => 'CADASTRO_TITLE_EMAIL')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_WHATSAPP')); ?>
                </label>
                <div class="controls">
                    <input type="tel" required name="pes_telefone1" id="inputWhatsapp" placeholder="WhatsApp" class="form-control" title="Preencha seu WhatsApp">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Sou brasileiro</label>
                <div class="controls">
                    <input type="checkbox" name="pes_brasil" value="1">
                </div>
            </div>

            <div class="form-group">
                <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_DATADENASCIMENTO')); ?>
                <div class="controls">
                    <input type="date" name="pes_datanasc" min="0001-01-01" max="9999-12-31" id="inputDataNasc" placeholder="Nascimento" class="form-control" required title="Preencha sua Data de Nascimento">
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary mt-2" type="submit">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_BOTAO_CADASTRAR')); ?>
                </button>
            </div>
        </form>

        <div>
            <a href="<?php echo BASE_URI . 'user/read' ?>"><button>Ver usuários</button></a>
        </div>

    <?php

    } else if ($mode == 'signUp') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'user/signup' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>
            <input type="hidden" name="pes_pais" value="BR" id="inputCountry">

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_NOME')); ?>
                </label>
                <div class="controls">
                    <input type="text" required name="pes_nome" id="inputName" 
                    placeholder="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_PLACEHOLDER_NOME')); ?>" class="form-control" 
                    title="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_TITLE_NOME')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_EMAIL')); ?>
                </label>
                <div class="controls">
                    <input type="text" name="pes_login" id="inputEmail" 
                    placeholder="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_PLACEHOLDER_EMAIL')); ?>" class="form-control" required 
                    title="<?php echo $Html_Helper->text(array('label' => 'CADASTRO_TITLE_EMAIL')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_WHATSAPP')); ?>
                </label>
                <div class="controls">
                    <input type="tel" required name="pes_telefone1" id="inputWhatsapp" placeholder="WhatsApp" class="form-control" title="Preencha seu WhatsApp">
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" name="pes_brasil">
                <label class="form-check-label" for="flexCheckDefault">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_IAM_BRAZILIAN')); ?>
                </label>
            </div>

            <div class="form-group">
                <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_DATADENASCIMENTO')); ?>
                <div class="controls">
                    <input type="date" name="pes_datanasc" min="0001-01-01" max="9999-12-31" id="inputDataNasc" placeholder="Nascimento" class="form-control" required title="Preencha sua Data de Nascimento">
                </div>
            </div>

            <div class="form-group">
                <?php echo $Html_Helper->label(array('label' => 'CADASTRO_LABEL_SENHA')); ?>
                <div class="controls">
                    <input type="password" name="pes_pwd" id="inputPassword" placeholder="Password" onkeyup="validatePassword()" class="form-control" pattern=".{8,64}" required maxlength="64" title="A senha precisa cumprir todos os requisitos de segurança descritos abaixo do campo senha">
                    <div class="requirements">
                        <span id="lengthReq">Mínimo de 8 caracteres</span><br>
                        <span id="uppercaseReq">Pelo menos uma letra maiúscula</span><br>
                        <span id="lowercaseReq">Pelo menos uma letra minúscula</span><br>
                        <span id="numericReq">Pelo menos um número</span><br>
                        <span id="specialCharReq">Pelo menos um caractere especial</span>
                    </div>
                    <div id="checkmarks"></div>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" type="checkbox" id="pes_permission_granted_data_usage" name="pes_permission_granted_data_usage" value="1">
                <label class="form-check-label" for="flexCheckDefault">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LABEL_TERMOS_DE_USO')) . $Html_Helper->text(array('label' => 'CADASTRO_LABEL_POLITICA_PRIVACIDADE')); ?>
                </label>
            </div>
            <div class="form-group">
                <button class="btn btn-primary mt-2" type="submit">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_BOTAO_CADASTRAR')); ?>
                </button>
            </div>
            <div class="form-group">
                <a href="<?php echo BASE_URI . 'login/signin' ?>">
                    <?php echo $Html_Helper->text(array('label' => 'CADASTRO_LINK_SOUCADASTRADO')); ?>
                </a>
            </div>
        </form>
    <?php

    } else if ($mode == 'errorAddUser') { ?>
        <div>
            <h2> <?= $error; ?> </h2>
        </div>

        <div>
            <a href="<?php echo BASE_URI . 'user/read' ?>"><button>Ver usuários</button></a>
        </div>
    <?php
    } ?>
</div>