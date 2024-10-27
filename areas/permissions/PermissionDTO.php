<?php 

namespace Areas\Permissions;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class PermissionDTO extends AbstractDTO
{
    public ?int $id;
    public string $classe;
    public string $metodo;

    public function __construct($id, $classe, $metodo) {
        parent::__construct([
            'id' => $id,
            'classe' => $classe,
            'metodo' => $metodo,
        ]);
    }

    public static function fromPermission($data, $isCreate = false)
    {
        try {
            $permission = new self(
                $data['id'] ?? null,
                $data['classe'] ?? null,
                $data['metodo'] ?? null
            );
        } catch (Exception $e) {
            return ("Erro ao criar PermissionDTO: " . $e->getMessage());
        }

        if (!$isCreate) {
            $validateId = self::validateId($permission);

            if (!$validateId) {
                return "ID is not defined";
            }
        }

        return $permission;
    }

    public static function validateId($permission)
    {
        return isset($permission->id);
    }
}