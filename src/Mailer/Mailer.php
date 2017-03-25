<?php

namespace App\Mailer;

class Mailer
{
    protected $sender;
    protected $smtp;
    protected $username;
    protected $password;
    protected $message;

    function __construct()
    {
        require_once 'app/parameters.php';
        $this->smtp = SM_SMTP;
        $this->username = SM_USER;
        $this->password = SM_PASSWORD;

    }

    public function sendMail($user, $password, $view)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->smtp, 465, 'ssl')
                ->setUsername($this->username)
                ->setPassword($this->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance()
            ->setSubject('Modification de votre mot de passe')
            ->setFrom(array($this->username => 'Blog'))
            ->setTo(array($user->getMail() => $user->getUsername()))
            ->setBody($view, 'text/html' );

        $mailer->send($message);
    }

}