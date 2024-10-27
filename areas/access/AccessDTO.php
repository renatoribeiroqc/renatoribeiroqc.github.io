<?php 

namespace Areas\Access;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class AccessDTO extends AbstractDTO
{
    public ?int $id;
    public int $role_id;
    public int $permission_id;
    public bool $enabled;

    public function __construct($id, $role_id, $permission_id, $enabled) {
        parent::__construct([
            'id' => $id,
            'role_id' => $role_id,
            'permission_id' => $permission_id,
            'enabled' => $enabled,
        ]);
    }

    public static function fromAccess($data, $isCreate = false)
    {
        try {
            $access = new self(
                $data['id'] ?? null,
                $data['role_id'] ?? null,
                $data['permission_id'] ?? null,
                $data['enabled'] ?? null
            );
        } catch (Exception $e) {
            return ("Erro ao criar AccessDTO: " . $e->getMessage());
        }

        if (!$isCreate) {
            $validateId = self::validateId($access);

            if (!$validateId) {
                return "ID is not defined";
            }
        }

        return $access;
    }

    public static function validateId($access)
    {
        return isset($access->id);
    }
}