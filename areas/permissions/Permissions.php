<?php

namespace Areas\Permissions;

use Lib\Framework\Dataprovider\Table;
use PDO;

class Permissions extends Table
{
    public function __construct()
    {
        parent::__construct(CONNECTION_STRING, 'permissions');
    }

    public function createPermission(PermissionDTO $request)
    {
        $permissionEntity = $this->newEntity((array) $request);
        $validatePermission = $this->validatePermission($permissionEntity);

        if ($validatePermission) {
            return $this->Add($permissionEntity);
        }

        return false;
    }

    public function readPermissions()
    {
        return $this->GetAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionById($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return false;
        }
        return $this->Find(array('id = ' . $id))->fetch(PDO::FETCH_ASSOC);
    }

    public function getRolePermission($permission_id)
    {
        if (filter_var($permission_id, FILTER_VALIDATE_INT) === false) {
            return false;
        }
        return $this->Find(array('permission_id = ' . $permission_id))->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePermission(PermissionDTO $request)
    {
        $permissionEntity = $this->newEntity((array) $request);
        $validatePermission = $this->validatePermission($permissionEntity);

        if ($validatePermission) {
            $this->Update($permissionEntity);
            return true;
        }

        return false;
    }

    public function validatePermission($entity)
    {
        $class = $entity->classe->Value;
        $method = $entity->metodo->Value;
        $sql = "SELECT * FROM permissions WHERE classe = '$class' AND metodo = '$method'";
        $permissions = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);

        if ($permissions) {
            return false;
        }

        return true;
    }

    public function getPermissionOptions($permissionId = null)
    {
        $permissions = $this->readPermissions();
        $option_tag = '';

        foreach ($permissions as $permission) {
            $selected = '';
            if (isset($permissionId) && $permissionId == $permission['id']) {
                $selected = ' SELECTED';
            }

            $option_tag .= '<option value = "' . $permission['id'] . '" ' . $selected . '>' . $permission['classe'] . '/' . $permission['metodo'] . '</option>';
        }

        return $option_tag;
    }
}
