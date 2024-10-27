<?php

namespace Lib\Framework\Core;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    protected $emailHeaders;
    protected $emailClient;
    protected $emailProfessional;
    protected $emailSysAdmin;
    private $recipientRule;
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(TRUE);
        $this->initEmailSettings();
    }

    private function initEmailSettings()
    {
        $this->mail->isSMTP();
        $this->mail->Host = _EMAIL_HOST;
        $this->mail->SMTPAuth = _EMAIL_SMTPAuth;
        $this->mail->Username = _EMAIL_Username;
        $this->mail->Password = _EMAIL_Password;
        $this->mail->SMTPSecure = _EMAIL_SMTPSecure;
        $this->mail->Port = _EMAIL_Port;
    }

    public function prepareMessageAndSend($messageData, $recipientData, $actionLink, $resetPasswordLink, $unlockAccountLink)
    {
        $peopleToBeAdvised = $this->getPeopleToBeAdvised($messageData['ruleId']);
        $subject = $messageData['subject'];
        if ($messageData['id'] == 10) {
            $actionLink = $resetPasswordLink;
        }

        if ($messageData['id'] == 18) {
            $actionLink = $unlockAccountLink;
        }

        $message = '<html><body>' . html_entity_decode(
            sprintf(
                $messageData['message'],
                sprintf($actionLink, $recipientData['hash']),
                $recipientData['userId'],
                $recipientData['pwd']
            )
        ) . '</body></html>';

        return $this->sendMessage($peopleToBeAdvised, $message, $subject);
    }

    public function sendMessage($emailTo, $message, $subject)
    {
        $this->mail->CharSet = 'UTF-8';
        $this->mail->Encoding = 'base64';
        $this->mail->setFrom(_EMAIL_Username, 'Equipe Conexperience Psicologia');
        $this->mail->addReplyTo(_EMAIL_Username, 'Equipe Conexperience Psicologia');

        foreach ($emailTo as $address) {
            $this->mail->addAddress($address);
        }

        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->AltBody = htmlspecialchars(trim(strip_tags($message)));

        try {
            if (!$this->mail->send()) {
                echo 'Contato não pode ser enviado.';
                echo 'Erro: ' . $this->mail->ErrorInfo;
                return $this->mail->ErrorInfo;
            } else {
                return TRUE;
            }
        } catch (Exception $exc) {
            echo $this->mail->ErrorInfo . " \r\n" . $exc->getTraceAsString();
        }
    }

    private function getPeopleToBeAdvised($ruleId)
    {
        $email_admin = $this->emailSysAdmin;
        $email_psicologo = $this->emailProfessional;
        $email_destinatario = $this->emailClient;
    
        $this->recipientRule = array(
            1 => array($email_destinatario),
            2 => array($email_psicologo),
            3 => array($email_destinatario, $email_psicologo),
            4 => array($email_admin),
            5 => array($email_admin, $email_destinatario),
            6 => array($email_admin, $email_psicologo),
            7 => array($email_admin, $email_psicologo, $email_destinatario)
        );
    
        return $this->recipientRule[$ruleId];
    }
}
