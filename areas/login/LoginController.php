<?php

/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Guilherme Xavier <guilherme.xavier@conexperience.com.br>, September 2024
 */
namespace Areas\Login;

use Areas\User\User;
use Areas\User\UserService;
use Lib\Framework\Core\Controller;
use Lib\Framework\Core\ResourceManager;

class LoginController extends Controller
{
    const TOKENLEN = 250;

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['AccountLock'])) {
            $_SESSION['AccountLock'] = 0;
        }

        $routes = array(
            'signin'    => array('url' => BASE_URI . 'login/signin', 'method' => 'Access'),
            'retry'     => array('url' => BASE_URI . 'access/signin', 'method' => 'Access'),
            'index'     => array('url' => BASE_URI . 'login', 'method' => 'Index'),
            'new'       => array('url' => BASE_URI . 'Account/new','method'    => ''),
            'logout'    => array('url' => BASE_URI . 'login/signout','method'    => 'logout'),
            'forgot'    => array('url' => BASE_URI . 'login/forgot','method'    => 'Forgot'),
            'reset'     => array('url' => BASE_URI . 'login/reset','method'    => 'Reset'),

            'sendpackage' => array('url' => BASE_URI . 'access/sendpackage', 'method' => 'sendPackageToClient'),
            'userisnotcompliant' => array('url' => BASE_URI . 'access/userisnotcompliant', 'method' => 'userIsNotCompliant'),
            'formactions' => array('url' => BASE_URI . 'access/formactions', 'method' => 'getFormActions'),
        );
        $this->addAllRoutes($routes);
    }

   /**
    * Public methods
    */

   /**
    * @GET
    */
    public function Index()
    {
        $this->Render('access', 'login.php');
    }
   /**
    * @GET
    */
    public function Logout()
    {
        session_unset();
        session_destroy();
        unset($_SESSION);
        header('Location:' . BASE_URI . 'access/signin');
        exit(0);
        // $this->Render('access', 'login.php');
    }
   /**
    * @POST
    */
    public function Access()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->Render('signin');
        } else {
            $this->clearRequest();
            $retLogin = $this->SignIn($_POST);
            if ($retLogin === true) {
                $userInfo = unserialize($_SESSION['user']);
                // $this->saveLoginAttempt(true, $_POST['login']);

                if (isset($userInfo['l']) && !is_null($userInfo['l']) && ($userInfo['l'] === '0' || $userInfo['t'] == '3')) {
                    // header('Location:' . BASE_URI . 'Session/dashboard');
                    header('Location:' . BASE_URI . 'user/read');
                } else {
                    if (isset($userInfo['t']) && !is_null($userInfo['t']) && $userInfo['t'] === 1) {
                        header('Location:' . BASE_URI . 'ProfessionalView/index');
                    } else {
                        if (isset($_SESSION['cameFromPackage'])) {
                            $packageId = $_SESSION['cameFromPackage'];
                            
                            header('Location:' . BASE_URI . 'ClientView/sendpackage' . '/' . $packageId);
                            exit(0);
                        }

                        // header('Location:' . BASE_URI . 'ClientView/index');
                        header('Location:' . BASE_URI . 'pessoa/profile');
                    }
                }
            } else {
                // $this->saveLoginAttempt(false, $_POST['login']);

                $this->Render('LoginError', 'loginView.php', $retLogin);
            }
        }
    }

    public function signIn($request)
    {
        $oUser = new User(CONNECTION_STRING);
        $login = $oUser->loginUser($request['login'], $request['pwd']);
        return $login;
    }

    public function Forgot()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->Render('forgotPassword');
        } else {
            $userService = new UserService();

            $ret = $userService->requestReset($_POST['login']);

            if ($ret !== 'INACTIVE' || $ret !== 'NOTFOUND') {
                // $this->Render('ResetRequestConfirmation', $ret);
                $this->Render('showPasswordLink', $ret);
            } else {
                if ($ret === 'INACTIVE') {
                    $this->Render('ErrorResetRequestConfirmation', 'layout.php');
                } else if ($ret === 'NOTFOUND') {
                    $this->Render('NotFoundResetRequestConfirmation', 'layout.php');
                }
                echo $this->showHttpMsg(406);
            }
        }
    }

    public function Reset($token = null)
    {
        if ($this->isGet()) {
            if (is_null($token) || strlen($token) < $this::TOKENLEN) {
                echo $this->showHttpMsg(401);
                exit();
            }

            $oUser = new User(CONNECTION_STRING);
            $user = $oUser->findAccountByHash($token);
            if (!$user) {
                $error = 'Link inválido';
                $this->Render('ErrorActivationConfirmed', 'layout.php', $error);
                exit(0);
            }

            $this->Render('changePassword', $token);
        } else {
            $userService = new UserService();
            $hash = $_POST['hash'];
            $password = $_POST['pwd'];
            $password2 = $_POST['newpwd'];

            if ($password !== $password2) {
                $error = "As senhas não conferem!";
                $this->Render('ErrorActivationConfirmed', 'layout.php', $error);
                exit(0);
            }

            if ($userService->UpdatePassword($hash, $password) !== FALSE) {
                $this->Render('ChangePasswordConfirmation');
            }
        }
    }

    // Função para enviar pacote para usuário não logado
    public function sendPackageToClient($packageId) {
        $_SESSION['cameFromPackage'] = $packageId;

        return $this->Access();
    }

    protected function clearRequest()
    {
        if (isset($_POST['__authstamp'])) {
            unset($_POST['__authstamp']);
        }

        if (isset($_POST['btn-salvar-cliente'])) {
            unset($_POST['btn-salvar-cliente']);
        }
    }

    protected function Render($viewName, $data = null)
    {
        $this->init();

        if ($viewName == 'signin') {
            $viewBag = array(
                'title' => 'Login',
                'mode' => $viewName,
            );
        }

        if ($viewName == 'forgotPassword') {
            $viewBag = array(
                'title' => ResourceManager::TextFor('RECUPERAR_SENHA_LABEL_TITULO'),
                'mode' => $viewName,
            );
        }

        if ($viewName == 'showPasswordLink') {
            $viewBag = array(
                'title' => ResourceManager::TextFor('RESEND_LINK_LABEL_EMAIL_TITULO'),
                'data' => $data,
                'mode' => $viewName,
            );
        }

        if ($viewName == 'changePassword') {
            $viewBag = array(
                'title' => ResourceManager::TextFor('NOVA_SENHA_LABEL_NOVASENHA_TITULO'),
                'hash' => $data,
                'mode' => $viewName,
            );
        }

        if ($viewName == 'ChangePasswordConfirmation') {
            $viewBag = array(
                'title' => ResourceManager::TextFor('NOVA_SENHA_LABEL_NOVASENHA_TITULO'),
                'mode' => $viewName,
            );
        }

        $this->view($viewBag);
    }

    private function init()
    {
        $this->view->master(dirname(__DIR__) . '/index.php');
        $this->view->partial(__DIR__ . '/LoginView.php');
    }
}
