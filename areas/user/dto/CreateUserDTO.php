<?php

namespace Areas\User\Dto;

use Exception;
use Lib\Framework\Dataprovider\AbstractDTO;

class CreateUserDTO extends AbstractDTO
{
    public string $email;
    public ?string $pwd;

    public function __construct($email, $pwd) {
        parent::__construct([
            'email' => $email,
            'pwd' => $pwd
        ]);
    }

    public static function fromUser($data, $signUp = false)
    {
        try {
            $user = new self(
                $data['pes_login'] ?? null,
                $data['pes_pwd'] ?? null
            );
        } catch (Exception $e) {
            return ("Erro ao criar UserDTO: " . $e->getMessage());
        }

        if ($signUp) {
            $validatePwd = self::validatePwd($user);

            if (!$validatePwd) {
                return "password is not defined";
            }
        }

        return $user;
    }

    public static function validatePwd($user)
    {
        return isset($user->pwd);
    }
}