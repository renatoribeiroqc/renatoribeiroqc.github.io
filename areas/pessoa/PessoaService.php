<?php

namespace Areas\Pessoa;

use Areas\User\User;
use Areas\Pessoa\Dto\CreateClientDTO;

class PessoaService
{
    public $model;
    public $user;

    const PROFESSIONAL = 1;
    const CLIENT = 2;
    const CONSULTOR = 3;

    public function __construct() 
    {
        $this->model = new Pessoa();
        $this->user = new User();
    }

    public function getDTOByUserType($type, $data) {
        if ($type == 1) {
            // Criar dto de profissional
        } else if ($type == 2) {
            $dtoData = CreateClientDTO::fromClient($data);
        } else {
            // Criar dto de consultor
        }

        return $dtoData;
    }
}