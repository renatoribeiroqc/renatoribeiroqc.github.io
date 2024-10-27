<?php

namespace Areas\Roles;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class RoleDTO extends AbstractDTO
{
    public ?int $id;
    public string $role;

    public function __construct($id, $role) {
        parent::__construct([
            'id' => $id,
            'role' => $role
        ]);
    }

    public static function fromRole($data, $isCreate = false)
    {
        try {
            $role = new self(
                $data['id'] ?? null,
                $data['role'] ?? null,
            );
        } catch (Exception $e) {
            return ("Erro ao criar RoleDTO: " . $e->getMessage());
        }

        if (!$isCreate) {
            $validateId = self::validateId($role);

            if (!$validateId) {
                return "ID is not defined";
            }
        }

        return $role;
    }

    public static function validateId($role)
    {
        return isset($role->id);
    }
}