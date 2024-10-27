<?php

namespace Areas\User;

use Areas\User\User;
use Areas\Roles\Roles;
use Areas\Pessoa\Pessoa;
use Areas\User\Dto\CreateUserDTO;
use Lib\Framework\Core\Postman;
use Exception;

class UserService
{
    public $model;
    public $pessoa;
    const PROFESSIONAL = 1;
    const CLIENT = 2;
    const CONSULTOR = 3;
    const UID_LEN = 250;

    public function __construct()
    {
        $this->model = new User();
        $this->pessoa = new Pessoa();
    }

    public function createAccount(CreateUserDTO $userDTO, $personDTO, $type = 1, $adminType = null) {
        $isAdmin = !is_null($adminType) ? true : false;
        $result = $this->createUserAndProfile($userDTO, $personDTO, $type, $isAdmin);

        if (!empty($result['id'])) {
            // Se o admin for consultor, envia link pra redefinir senha. Se não for, envia link de ativação.
            if ($isAdmin && $adminType == $this::CONSULTOR) {
                $this->requestReset($userDTO->email);
            } else {
                try {
                    // $oPostman->prepareMessageAndSend($entUser->email->Value, 9);
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                    exit();
                }
            }
        }

        return $result;
    }

    public function createUserAndProfile($userDTO, $profileDTO, $type = null, $isAdmin = false) {
        $oRoles = new Roles(CONNECTION_STRING);

        $return = [
            'id' => '',
            'error' => ''
        ];

        $isValid = $this->model->validateUserFields($profileDTO, $isAdmin);

        if ($isValid !== true) {
            $return['error'] = $isValid;
            return $return;
        }

        $user = $this->model->newEntity((array) $userDTO);
        $user->role_id->Value = $oRoles->getRoleIdByUserType($type);
        $user->activationtoken->Value = substr($this->generateHash(), 0, 250);
        $user->status->Value = isset($profileDTO->pes_ativo) ? $profileDTO->pes_ativo : 0;
        $user->pwd->Value = password_hash($user->pwd->Value, PASSWORD_BCRYPT);

        $id = $this->model->createUser($user);

        if ($id) {
            $profileId = $this->model->createProfile($user, $id, $type, $profileDTO);
            $return['id'] = $profileId;
        }

        return $return;
    }

    public function UpdatePassword($hash, $password)
    {
       $entUser = $this->model->Entity();
       $user = $this->model->findAccountByHash($hash);
       $entUser->id->Value = $user['id'];
       $entUser->pwd->Value = password_hash($password, PASSWORD_BCRYPT);
       $accountUpdated = $this->model->Update($entUser);

       $profile = $this->pessoa->Find(array('pes_idusuario = ' . $entUser->id->Value))->fetchObject();
       $entProfile = $this->pessoa->Entity();
       $entProfile->idpessoa->Value = $profile->idpessoa;
       $entProfile->pes_pwd->Value = $entUser->pwd->Value;
       $profileUpdated = $this->pessoa->Update($entProfile);

       return ($accountUpdated && $profileUpdated);
    }

    public function requestReset($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->model->detailedError[0];
        }

        $user = $this->model->findAccountByEmail($email);

        if ($user != FALSE) {
            if ($user['status'] == 1) {
                $oPostman = new Postman();
                return $oPostman->getResetPasswordMessage($email, $user['activationtoken']);
            }
            return 'INACTIVE';
        }

        return 'NOTFOUND';
    }

    private function generateHash()
    {
       return bin2hex(openssl_random_pseudo_bytes($this::UID_LEN));
    }
}