<?php

namespace Areas\User;

use Areas\Pessoa\Dto\CreateClientDTO;
use Areas\User\Dto\CreateUserDTO;
use Lib\Framework\Core\Controller;
use Lib\Framework\Core\ResourceManager;
use PDO;

class UserController extends Controller
{
    const PROFESSIONAL = 1;
    const CLIENT = 2;
    const CONSULTOR = 3;

    public function signUp()
    {
        $this->init();

        if ($this->isGet()) {
            $viewBag = array(
                'title' => ResourceManager::TextFor('CADASTRO_LABEL_TITULO'),
                'mode' => 'signUp',
            );

            $this->view($viewBag);
        }

        if ($this->isPost()) {
            $userDTO = CreateUserDTO::fromUser($_POST, true);
            $personDTO = CreateClientDTO::fromClient($_POST, true);

            if (!($userDTO instanceof CreateUserDTO) || !($personDTO instanceof CreateClientDTO)) {
                return $this->sendResponse('failed', null, 'bad request');
            }

            $result = $this->service->createAccount($userDTO, $personDTO, $this::CLIENT);

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
        $this->init();
        $dataset = $this->model->GetAll()->fetchAll(PDO::FETCH_ASSOC);
        $viewBag = array(
            'title' => ResourceManager::TextFor('USERS_LABEL_TITLE'),
            'model' => $dataset,
            'mode' => 'read',
        );
        $this->view($viewBag);
    }

    public function Update($id = null)
    {
        if (!$this->isPost()) {
            return false;
        }
        $this->init();
    }

    public function Delete($id = null)
    {
        if (!$this->isPost()) {
            return false;
        }
        $this->init();
    }

    public function Async()
    {
        $this->model = new User();
        $dataset = $this->model->GetAll()->fetchAll(PDO::FETCH_ASSOC);
        $this->AsJson($dataset);
    }

    private function init()
    {
        $this->view->master(dirname(__DIR__) . '/index.php');
        $this->view->partial(__DIR__ . '/UserView.php');
        $this->model = new User();
        $this->service = new UserService();
    }
}