<?php

namespace Areas\Roles;

use Lib\Framework\Dataprovider\Table;
use PDO;

class Roles extends Table
{
    const ADMIN = 1;
    const USER = 2;
    const PROFESSIONAL = 3;
    const CONSULTOR = 4;
    const FREE = 8;

    public function __construct()
    {
        parent::__construct(CONNECTION_STRING, 'roles');
    }

    public function createRole(RoleDTO $request) {
        $rolesEntity = $this->newEntity((array) $request);
        $validateRole = $this->validateRole($rolesEntity);

        if ($validateRole) {
            return $this->Add($rolesEntity);
        }

        return false;
    }

    public function readRoles() {
        return $this->GetAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoleOptions($roleId = null) {
        $roles = $this->readRoles();
        $option_tag = '';

        foreach ($roles as $role) {
            $selected = '';
            if (isset($roleId) && $roleId == $role['id']) {
                $selected = ' SELECTED';
            }

            $option_tag .= '<option value = "' . $role['id'] . '" ' . $selected . '>' . $role['role'] . '</option>';
        }

        return $option_tag;
    }

    public function validateRole($entity) {
        $role = $entity->role->Value;
        $sql = "SELECT * FROM roles WHERE role = '$role'";
        $roles = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);

        if ($roles) {
            return false;
        }

        return true;
    }

    public function getRoleIdByUserType($userType): int
    {
        // O admin agora terá tipo 0
        if ($userType == 0) {
            $role_id = $this::ADMIN;
        } else if ($userType == 1) {
            $role_id = $this::PROFESSIONAL;
        } else if ($userType == 2) {
            $role_id = $this::USER;
        } else if ($userType == 3) {
            $role_id = $this::CONSULTOR;
        }

        return $role_id;
    }
}
