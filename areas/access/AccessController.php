<?php

namespace Areas\Access;

use Areas\Roles\Roles;
use Areas\Roles\RoleDTO;
use Areas\Permissions\Permissions;
use Areas\Permissions\PermissionDTO;
use Lib\Framework\Core\Controller;
use Lib\Framework\Core\ResourceManager;
use PDO;

class AccessController extends Controller
{
    public function Create()
    {
        $this->init();

        if ($this->isGet()) {
            $roles = new Roles();
            $permissions = new Permissions();
            $dataset['roles'] = $roles->getRoleOptions();
            $dataset['permissions'] = $permissions->getPermissionOptions();
            $viewBag = array(
                'title' => ResourceManager::TextFor('ACCESS_CREATE_LABEL_TITLE'),
                'model' => $dataset,
                'mode' => 'create',
            );

            $this->view($viewBag);
        } else {
            $dtoData = AccessDTO::fromAccess($_POST, true);

            if (!($dtoData instanceof AccessDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $this->model->createAccess($dtoData);

            if ($result) {
                $dtoData->id = $result;
            }
            $this->Read();
        }
    }

    public function Read()
    {
        $this->init();
        $dataset = $this->model->getAllAccesses();
        $viewBag = array(
            'title' => ResourceManager::TextFor('ACCESS_LABEL_TITLE'),
            'model' => $dataset,
            'mode' => 'read',
        );
        $this->view($viewBag);
    }

    public function Update($id = null)
    {
        $this->init();

        if (!$this->isPost()) {
            $roles = new Roles();
            $permissions = new Permissions();
            $access = $this->model->getAccessById($id);
            if (!$access) {
                return $this->sendResponse('failed', null, 'bad request');
            }   
            $dataset['id'] = $access['id'];
            $dataset['roles'] = $roles->getRoleOptions($access['role_id']);
            $dataset['permissions'] = $permissions->getPermissionOptions($access['permission_id']);
            $viewBag = array(
                'title' => ResourceManager::TextFor('ACCESS_UPDATE_LABEL_TITLE'),
                'model' => $dataset,
                'mode' => 'updateAccess',
            );
            $this->view($viewBag);
        } else {
            $dtoData = AccessDTO::fromAccess($_POST);

            if (!($dtoData instanceof AccessDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $this->model->updateAccess($dtoData);

            if ($result) {
                $this->Read();
            }

            $this->sendResponse('failed', null, 'bad request');
        }
    }

    public function Delete($id = null)
    {
        if (!$this->isPost()) {
            return false;
        }
        $this->init();
    }

    public function Async()
    {
        $this->model = new Access();
        $dataset = $this->model->GetAll()->fetchAll(PDO::FETCH_ASSOC);
        $this->AsJson($dataset);
    }

    public function createPermission() {
        $this->init();

        if (!$this->isPost()) {
            $viewBag = array(
                'title' => ResourceManager::TextFor('RESOURCES_CREATE_LABEL_TITLE'),
                'mode' => 'createPermission',
            );

            $this->view($viewBag);
        } else {
            $oPermissions = new Permissions();
            $dtoData = PermissionDTO::fromPermission($_POST, true);

            if (!($dtoData instanceof PermissionDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $oPermissions->createPermission($dtoData);

            if ($result) {
                $dtoData->id = $result;
            }
            $this->readPermissions();
        }
    }

    public function readPermissions() {
        $this->init();
        $oPermissions = new Permissions();
        $permissions = $oPermissions->readPermissions();

        $viewBag = array(
            'title' => ResourceManager::TextFor('RESOURCES_LABEL_TITLE'),
            'model' => $permissions,
            'mode' => 'readPermissions',
        );

        $this->view($viewBag);
    }

    public function updatePermission($id = null) {
        $this->init();
        $oPermissions = new Permissions();

        if (!$this->isPost()) {
            $permission = $oPermissions->getPermissionById($id);
            if (!$permission) {
                return $this->sendResponse('failed', null, 'bad request');
            }
            $dataset['id'] = $permission['id'];
            $dataset['class'] = $permission['classe'];
            $dataset['method'] = $permission['metodo'];

            $viewBag = array(
                'title' => ResourceManager::TextFor('RESOURCES_UPDATE_LABEL_TITLE'),
                'model' => $dataset,
                'mode' => 'updatePermission',
            );
            $this->view($viewBag);
        } else {
            $dtoData = PermissionDTO::fromPermission($_POST);

            if (!($dtoData instanceof PermissionDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $oPermissions->updatePermission($dtoData);

            if ($result) {
                $this->Read();
            }

            $this->sendResponse('failed', null, 'bad request');
        }
    }

    public function createRole() {
        $this->init();

        if (!$this->isPost()) {
            $viewBag = array(
                'title' => ResourceManager::TextFor('ROLE_CREATE_LABEL_TITLE'),
                'mode' => 'createRole',
            );

            $this->view($viewBag);
        } else {
            $oRoles = new Roles();
            $dtoData = RoleDTO::fromRole($_POST, true);

            if (!($dtoData instanceof RoleDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $oRoles->createRole($dtoData);

            if ($result) {
                $dtoData->id = $result;
            }
            
            $this->readRoles();
            $this->sendResponse('failed', null, 'bad request');
        }
    }

    public function readRoles() {
        $this->init();
        $oRoles = new Roles();
        $roles = $oRoles->readRoles();

        $viewBag = array(
            'title' => ResourceManager::TextFor('ROLE_LABEL_TITLE'),
            'model' => $roles,
            'mode' => 'readRoles',
        );

        $this->view($viewBag);
    }

    private function init()
    {
        $this->view->master(dirname(__DIR__) . '/index.php');
        $this->view->partial(__DIR__ . '/accessView.php');
        $this->model = new Access();
    }
}
