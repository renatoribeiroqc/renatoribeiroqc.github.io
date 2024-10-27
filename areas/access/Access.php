<?php

/**
 * Access.php is a class to operate over the table role_permissions, that defines the accesses of agenda 
 * @author Guilherme Xavier <guilherme.xavier@conexperience.com.br>
 */
namespace Areas\Access;

use Areas\Roles\Roles;
use Lib\Framework\Dataprovider\Table;
use PDO;

class Access extends Table
{
    public function __construct()
    {
        parent::__construct(CONNECTION_STRING, 'role_permissions');
    }

    public function createAccess(AccessDTO $request) {
        $accessEntity = $this->newEntity((array) $request);
        $validateAccess = $this->validateAccess($accessEntity);

        if ($validateAccess) {
            return $this->Add($accessEntity);
        }

        return false;
    }

    public function getAllAccesses() {
        $sql = "SELECT role_permissions.id, permissions.classe, permissions.metodo, roles.role
        FROM 
        role_permissions        
        INNER JOIN  permissions ON permissions.id = role_permissions.permission_id
        INNER JOIN roles ON roles.id = role_permissions.role_id";

        $accesses = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $accesses;
    }

    public function updateAccess(AccessDTO $request) {
        $accessEntity = $this->newEntity((array) $request);
        $validateAccess = $this->validateAccess($accessEntity);

        if ($validateAccess) {
            $this->Update($accessEntity);
            return true;
        }

        return false;
    }

    public function getAccessById($id) {
        if(filter_var($id, FILTER_VALIDATE_INT) === false){
            return false;
        }

        $sql = "SELECT role_permissions.id, permissions.id as permission_id, roles.id as role_id
        FROM role_permissions 
        INNER JOIN permissions ON permissions.id = role_permissions.permission_id
        INNER JOIN roles ON roles.id = role_permissions.role_id
        WHERE 
        role_permissions.id = :id";

        $stmt = $this->connection()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute();   

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getExternalAccess() {
        $oRoles = new Roles();
        $roleId = $oRoles->Find(array("role = 'external'"))->fetch(PDO::FETCH_ASSOC)['id'];

        if ($roleId) {
            $sql = "select classe, metodo 
            from permissions  
            inner join role_permissions on role_permissions.permission_id = permissions.id 
            where 
            role_permissions.role_id = '$roleId' and role_permissions.enabled = 1";
            $permissions = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $permissions;
        }

        return false;
    }

    public function validateAccess($entity) {
        $role_id = $entity->role_id->Value;
        $permission_id = $entity->permission_id->Value;
        $sql = "SELECT * FROM role_permissions WHERE role_permissions.role_id = '$role_id' AND role_permissions.permission_id = '$permission_id'";
        $accesses = $this->execQuery($sql)->fetchAll(PDO::FETCH_ASSOC);

        if ($accesses) {
            return false;
        }

        return true;
    }
}
