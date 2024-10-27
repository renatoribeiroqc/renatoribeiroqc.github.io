<?php

namespace Areas\Pessoa;

use Areas\User\User;
use Areas\User\UserService;
use Areas\User\Dto\CreateUserDTO;
use Areas\Pessoa\Dto\CreateClientDTO;
use Lib\Framework\Core\Controller;
use PDO;

class PessoaController extends Controller
{
    const PROFESSIONAL = 1;
    const CLIENT = 2;
    const CONSULTOR = 3;

    public function createClient()
    {
        $this->init();

        if ($this->isGet()) {
            $viewBag = array(
                'title' => 'Criar usuário',
                'mode' => 'createClient',
            );

            $this->view($viewBag);
        }

        if ($this->isPost()) {
            $userService = new UserService();
            $userDTO = CreateUserDTO::fromUser($_POST);
            $personDTO = CreateClientDTO::fromClient($_POST);

            if (!($userDTO instanceof CreateUserDTO) || !($personDTO instanceof CreateClientDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $userService->createAccount($userDTO, $personDTO, $this::CLIENT, $this->user['t']);

            if (!empty($result['error'])) {
                $viewBag = array(
                    'title' => 'Erro ao criar usuário',
                    'mode' => 'errorAddUser',
                    'error' => $result['error']
                );

                return $this->view($viewBag);
            }

            $this->Read();
        }
    }

    public function Read()
    {
        if (!$this->isGet()){
            return false;
        }

        $this->init();
        $dataset = $this->model->GetAll()->fetchAll(PDO::FETCH_ASSOC);
        $viewBag = array(
            "title" => "Hello World!",
            "model" => $dataset,
            "mode" => "read",
        );
        $this->view($viewBag);
    }

    public function updateClient($id = null)
    {
        if (!$this->isPost()){ 
            return false;
        }
        $this->init();
    }

    public function Delete($id = null)
    {
        if (!$this->isPost()){ 
            return false;        
        }
        $this->init();
    }

    public function Profile() {
        $this->init();

        if ($this->isGet()) {
            $oPessoa = new Pessoa();
            $oUser = new User();
            $user = $oUser->findAccountByHash($this->user['i']);
            $profile = $oPessoa->getProfileByHash($user['activationtoken']);

            $viewBag = array(
                "title" => "meu perfil",
                "model" => $profile,
                "mode" => "profile",
            );

            $this->view($viewBag);
        }
    }

    public function Async()
    {
        $this->model = new Pessoa();
        $dataset = $this->model->GetAll()->fetchAll(PDO::FETCH_ASSOC);
        $this->AsJson($dataset);
    }

    private function init()
    {
        $this->view->master(dirname(__DIR__) . '/index.php');
        $this->view->partial(__DIR__ . '/PessoaView.php');
        $this->model = new Pessoa();
    }
}
