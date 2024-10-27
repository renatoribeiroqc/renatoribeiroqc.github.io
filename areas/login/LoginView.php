<?php

use Areas\Login\LoginController;
use Lib\Framework\Core\ResourceManager;
use Lib\Framework\Helpers\Html;

$loginController = new LoginController();
$Html_Helper = new Html();

if ($this->viewBag) {
    extract($this->viewBag);
}
?>
<div class="container">
    <h2><?= $title ?></h2>
    <div class="container-fluid">
        <div class="card alpha60 loginbox">
            <div class="card-body">
                <?php

                if ($mode == 'signin') { ?>
                    <form class="form" role="form" id="loginform" method="post" action="<?php echo $loginController->readRoute('signin', 'url'); ?>">
                        <?php echo $Html_Helper->getTokenInput(); ?>
                        <div class="form-group mb-1">
                            <?= Html::label(array("label" => 'LOGIN_LABEL_USUARIO')) ?>
                            <input type="email" name="login" id="inputEmail" placeholder="Email" class="form-control">
                        </div>

                        <div class="form-group mb-1">
                            <?= Html::label(array("label" => 'LOGIN_LABEL_SENHA')) ?>
                            <input type="password" name="pwd" id="inputPassword" placeholder="Password" class="form-control" maxlength="64">
                        </div>

                        <div class="form-group mb-1">
                            <button class="btn btn-primary mt-2" type="submit">Submit</button>
                        </div>

                        <div class="form-group text-right mb-1">
                            <br>
                            <?php echo $Html_Helper->ahrefLink(array('label' => 'LOGIN_LINK_ESQUECI_SENHA', 'link' => BASE_URI . 'login/forgot')); ?> | 
                            <?php echo $Html_Helper->ahrefLink(array('label' => 'LOGIN_LINK_CADASTRO', 'link' => BASE_URI . 'user/signup')); ?>
                        </div>
                    </form>

                <?php

                } else if ($mode == 'forgotPassword') { ?>
                    <form class="form" role="form" method="post" action="<?php echo BASE_URI . 'login/forgot' ?>">
                        <?php echo $Html_Helper->getTokenInput(); ?>

                        <div class="form-group mb-1">
                            <?= Html::label(array("label" => 'RECUPERAR_SENHA_LABEL_EMAIL')) ?>
                            <input type="email" name="login" id="inputEmail" placeholder="e-mail" class="form-control" required>
                        </div>
                        <div class="form-group mb-1">
                            <button type="submit" class="btn btn-primary mt-2"><?= ResourceManager::TextFor('RECUPERAR_SENHA_BOTAO_SOLICITAR') ?></button>
                        </div>

                        <div class="form-group mb-1">
                            <?php echo $Html_Helper->ahrefLink(array('label' => 'RECUPERAR_SENHA_LINK_SOUCADASTRADO', 'link' => BASE_URI . 'login/signin')); ?> |
                            <?php echo $Html_Helper->ahrefLink(array('label' => 'RECUPERAR_SENHA_LINK_CADASTRO', 'link' => BASE_URI . 'user/signup')); ?>
                        </div>
                    </form>
                <?php

                } else if ($mode == 'showPasswordLink') { ?>
                    <div>
                        <a href="<?= $data ?>">Click here</a>
                    </div>

                <?php

                } else if ($mode == 'changePassword') { ?>
                    <form class="form changepassword" method="post" action="<?php echo BASE_URI . 'login/reset' ?>">
                        <?php echo $Html_Helper->getTokenInput(); ?>
                        <input type="hidden" value="<?php echo $hash; ?>" name="hash">

                        <div class="form-group mb-2">
                            <label class="form-label"><?php echo $Html_Helper->label(array('label' => 'NOVA_SENHA_LABEL_NOVASENHA')); ?></label>
                            <div class="controls">
                                <input type="password" name="newpwd" id="inputPassword1" placeholder="digite a nova senha" onkeyup="validatePassword()" class="form-control" pattern=".{8,64}" required maxlength="64" title="A senha precisa cumprir todos os requisitos de segurança descritos abaixo do campo senha">
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

                        <div class="form-group mb-2">
                            <label class="form-label"><?php echo $Html_Helper->label(array('label' => 'NOVA_SENHA_LABEL_DIGITENOVAMENTE')); ?></label>
                            <input type="password" name="pwd" id="inputPassword2" placeholder="redigite a nova senha" onkeyup="validatePassword()" class="form-control" pattern=".{8,64}" required maxlength="64" title="A senha precisa cumprir todos os requisitos de segurança descritos abaixo do campo senha">
                        </div>

                        <div class="form-group mb-2">
                            <?php echo $Html_Helper->button(
                                array(
                                    'label' => 'NOVA_SENHA_BOTAO_CRIARNOVASENHA',
                                    'class' => "btn btn-primary mt-2",
                                    'type' => "submit",
                                    'name' => "btn-salvar-senha-cliente"
                                )
                            ); ?>
                        </div>
                    </form>
                <?php

                } else if ($mode == 'ChangePasswordConfirmation') { ?>
                    <div>
                        <h2>Senha alterada com sucesso!</h2>
                    </div>

                <?php
                } ?>
            </div>
        </div>
    </div>
</div>