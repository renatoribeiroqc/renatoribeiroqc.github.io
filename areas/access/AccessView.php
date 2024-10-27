<?php

namespace Areas\Access;

use Lib\Framework\Core\DBGrid;
use Lib\Framework\Helpers\Html;

extract($this->viewBag);

$Html_Helper = new Html();
?>
<div class="container">
    <h1><?= $title ?></h1>
    <?php
    if ($mode == 'read') {
        $dbGrid = new DBGrid();
        echo $dbGrid->createGrid(
            $model,
            array(
                'update' => BASE_URI . 'access/update',
                'delete' => BASE_URI . 'access/delete'
            ),
            'acessos'
        );
    ?>
        <div>
            <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/create' ?>"><?= Html::text(array('label' => 'ACCESS_CREATE_LABEL_TITLE')) ?></a>
            <a class="btn btn-outline-info" href="<?php echo BASE_URI . 'access/readpermissions' ?>"><?= Html::text(array('label' => 'RESOURCES_LABEL_TITLE')) ?></a>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_URI . 'access/readroles' ?>"><?= Html::text(array('label' => 'ROLE_LABEL_TITLE')) ?></a>
        </div>
        <script>
            let table = new DataTable('#tbacessos');
        </script>
    <?php

    } else if ($mode == 'create') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'access/create' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>

            <div class="form-group">
                <label class="form-label">
                    <?= Html::text(array('label' => 'ACCESS_TH_02')) ?>
                </label>
                <div class="forms">
                    <select required name="permission_id" class="form-control">
                        <option></option>
                        <?= $model['permissions'] ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= Html::text(array('label' => 'ROLES_TH_02')) ?></label>
                <div class="forms">
                    <select required name="role_id" class="form-control">
                        <option></option>
                        <?= $model['roles'] ?>
                    </select>
                </div>
            </div>

            <div class="form-check mt-2 mb-2">
                <input type="checkbox" class="form-check-input" name="enabled" value="1">
                <label class="form-check-label"><?= Html::text(array('label' => 'ROLES_CHECK_ACTIVE')) ?></label>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit"><?= Html::text(array('label' => 'ACCESS_BUTTON_LABEL')) ?></button>
                <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/read' ?>"><?= Html::text(array('label' => 'ACCESS_LABEL_TITLE')) ?></a>
            </div>

        </form>
    <?php

    } else if ($mode == 'updateAccess') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'access/update' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>
            <input type="hidden" name="id" value="<?php echo $model['id']; ?>">

            <div class="form-group">
                <label class="form-label">Classe/Metodo</label>
                <div class="forms">
                    <select required name="permission_id" placeholder="Função" title="Preencha a rota">
                        <option></option>
                        <?= $model['permissions'] ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Função</label>
                <div class="forms">
                    <select required name="role_id" placeholder="Função" title="Preencha a função">
                        <option></option>
                        <?= $model['roles'] ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ativo?</label>
                <div class="forms">
                    <input type="checkbox" name="enabled" value="1">
                </div>
            </div>

            <button type="submit" style="margin: 1rem 0;">Salvar</button>
        </form>

        <div>
            <a href="<?php echo BASE_URI . 'access/read' ?>"><button>Voltar</button></a>
        </div>
    <?php

    } else if ($mode == 'createPermission') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'access/createpermission' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>

            <div class="form-group">
                <label class="form-label">
                    <?=Html::text(array('label'=>'RESOURCES_CLASSE_LABEL'))?>
                </label>
                <input type="text" required name="classe" id="inputClasse" placeholder="Nome da classe" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?=Html::text(array('label'=>'RESOURCES_METODO_LABEL'))?>
                </label>
                <div class="forms">
                    <input type="text" name="metodo" placeholder="Método" class="form-control" required title="Preencha o método">
                </div>
            </div>
            <div class="form-group mt-2 mb-2">
                <button class="btn btn-primary" type="submit">
                    <?=Html::text(array('label'=>'RESOURCES_BUTTON_LABEL'))?>
                </button>
                <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/readpermissions' ?>"><?=Html::text(array('label'=>'RESOURCES_LABEL_TITLE'))?></a>
            </div>

        </form>

        <div>
            
        </div>

        <script>
            table = new DataTable('#tbpermissions');
        </script>
    <?php

    } else if ($mode == 'readPermissions') {
        $dbGrid = new DBGrid();
        echo $dbGrid->createGrid(
            $model,
            array(
                'update' => BASE_URI . 'access/updatepermission',
                'delete' => BASE_URI . 'access/deletepermission'
            ),
            'permissions'
        );
    ?>
        <div>
            <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/createpermission' ?>"><?= Html::text(array('label' => 'RESOURCES_CREATE_LABEL_TITLE')) ?></a>
            <a class="btn btn-outline-info" href="<?php echo BASE_URI . 'access/read' ?>"><?= Html::text(array('label' => 'ACCESS_LABEL_TITLE')) ?></a>
        </div>

        <script>
            table = new DataTable('#tbpermissions');
        </script>
    <?php

    } else if ($mode == 'updatePermission') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'access/updatepermission' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>
            <input type="hidden" name="id" value="<?php echo $model['id']; ?>">

            <div class="form-group">
                <label class="form-label"><?=Html::text(array('label'=>'RESOURCES_CLASSE_LABEL'))?></label>
                <div class="forms">
                    <input required name="classe" class="form-control" placeholder="<?=Html::text(array('label'=>'RESOURCES_CLASSE_LABEL'))?>" title="Preencha a classe" value="<?= $model['class'] ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?=Html::text(array('label'=>'RESOURCES_METODO_LABEL'))?></label>
                <div class="forms">
                    <input required name="metodo" class="form-control" placeholder="<?=Html::text(array('label'=>'RESOURCES_METODO_LABEL'))?>" title="Preencha o método" value="<?= $model['method'] ?>">
                </div>
            </div>

            <div class="form-group mt-2 mb-2">
                <button class="btn btn-primary" type="submit">
                    <?=Html::text(array('label'=>'RESOURCES_BUTTON_LABEL'))?>
                </button>
                <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/readpermissions' ?>"><?=Html::text(array('label'=>'RESOURCES_LABEL_TITLE'))?></a>
            </div>
        </form>        
    <?php

    } else if ($mode == 'readRoles') {
        $dbGrid = new DBGrid();
        echo $dbGrid->createGrid(
            $model,
            array(
                'update' => BASE_URI . 'access/updaterole',
                'delete' => BASE_URI . 'access/deleterole'
            ),
            'roles'
        );
    ?>
        <div>
            <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/createrole' ?>"><?= Html::text(array('label' => 'ROLES_CREATE_BUTTON_LABEL')) ?></a>
            <a class="btn btn-outline-info" href="<?php echo BASE_URI . 'access/read' ?>"><?= Html::text(array('label' => 'ACCESS_LABEL_TITLE')) ?></a>
        </div>

        <script>
            table = new DataTable('#tbroles');
        </script>
    <?php

    } else if ($mode == 'createRole') { ?>
        <form class="form form-horizontal signup" method="post" action="<?php echo BASE_URI . 'access/createrole' ?>">
            <?php echo $Html_Helper->getTokenInput(); ?>

            <div class="form-group">
                <label class="form-label">
                    <?= Html::text(array('label' => 'ROLES_LABEL_TITLE')) ?>
                </label>
                <input type="text" required name="role" class="form-control" placeholder="<?= Html::text(array('label' => 'ROLES_LABEL_TITLE')) ?>">
            </div>
            
            <div class="form-group mt-2 mb-2">
                <button class="btn btn-primary" type=submit">
                    <?= Html::text(array('label' => 'ROLES_SAVE_BUTTON_LABEL')) ?>
                </button>
                <a class="btn btn-outline-primary" href="<?php echo BASE_URI . 'access/readroles' ?>">
                    <?= Html::text(array('label' => 'ROLES_LABEL_TITLE')) ?>
                </a>
            </div>

        </form>
    <?php } ?>
</div>