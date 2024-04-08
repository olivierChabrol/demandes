<?php

namespace Models\Tool;

require_once('components/PHPMailer/src/PHPMailer.php');
require_once('components/PHPMailer/src/SMTP.php');
require_once('components/PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    const IS_HTML_DEFAULT = true;
    const TIMEOUT_DEFAULT = 30;

    private static $instance = null;
    public $mailer;
    private $sslCheck;

    public function __construct($parameters = [])
    {
        $this->mailer = new PHPMailer(true);
        $this->sslCheck = false;

        if (isset($parameters['charset'])) {
            $this->setCharset($parameters['charset']);
        }
        if (isset($parameters['mail_smtp_class']) && $parameters['mail_smtp_class']) {
            if ($parameters['mail_smtp_class'] == 'IsSendMail()') {
                $this->isSendMail();
            } else {
                $this->isSMTP();
            }
        }
        if (isset($parameters['mail_secure'])) {
            if ($parameters['mail_secure'] == 'SSL') {
                $this->setHost("ssl://$parameters[mail_smtp]");
            } else if ($parameters['mail_secure'] == 'TLS') {
                $this->setHost("tls://$parameters[mail_smtp]");
            } else if(isset($parameters['mail_smtp'])) {
                $this->setHost($parameters['mail_smtp']);
            }
        }
        if (isset($parameters['mail_auth'])) {
            $this->setSMTPAuth($parameters['mail_auth']);
        }
        if (isset($parameters['debug'])) {
            $this->setSMTPDebug($parameters['debug']);
        }
        if (isset($parameters['mail_secure']) && $parameters['mail_secure'] != 0) {
            $this->setSMTPSecure($parameters['mail_secure']);
        }
        if (isset($parameters['mail_port']) && $parameters['mail_port'] != 25) {
            $this->setSMTPPort($parameters['mail_port']);
        }
        if (isset($parameters['mail_username'])) {
            $this->setUsername($parameters['mail_username']);
        }
        if (isset($parameters['mail_password'])) {
            if(preg_match('/gs_en/',$parameters['mail_password'])) {
                $parameters['mail_password'] = gs_crypt($parameters['mail_password'], 'd' , $parameters['server_private_key']);
            }
            $this->setPassword($parameters['mail_password']);
        }
        $this->setIsHtml(self::IS_HTML_DEFAULT);
        $this->setTimeout(self::TIMEOUT_DEFAULT);
        if ($parameters['mail_from_name']) {
            $this
                ->setFromName($parameters['mail_from_name']);
        }
        $this->mailer->XMailer = ' ';
        if (isset($parameters['mail_ssl_check']) && $parameters['mail_ssl_check']) {
            $this->setSSLCheck($parameters['mail_ssl_check']);

        }
    }

    public static function getInstance($parameters = []) {
        if(is_null(self::$instance)) {
            self::$instance = new Mailer($parameters);
        }

        return self::$instance;
    }

    public function setCharset(string $charset): self
    {
        $this->mailer->CharSet = $charset;
    }

    public function isSendMail(): self
    {
        $this->mailer->IsSendMail();
        return $this;
    }

    public function isSMTP(): self
    {
        $this->mailer->IsSMTP();
        return $this;
    }

    public function setHost(string $host): self
    {
        $this->mailer->Host = $host;
        return $this;
    }

    public function setSMTPAuth(string $auth): self
    {
        $this->mailer->SMTPAuth = $auth;
        return $this;
    }

    public function setSMTPDebug(int $debug): self
    {
        $this->mailer->SMTPDebug = $debug;
        return $this;
    }

    public function setSMTPSecure(bool $isSecure): self
    {
        $this->mailer->SMTPSecure = $isSecure;
        return $this;
    }

    public function setSMTPPort(int $port): self
    {
        $this->mailer->Port = $port;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->mailer->Username = $username;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->mailer->Password = $password;
        return $this;
    }

    public function setIsHtml(bool $isHtml): self
    {
        $this->mailer->IsHTML = $isHtml;
        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->mailer->Timeout = $timeout;
        return $this;
    }

    public function setSSLCheck(bool $sslCheck): self
    {
	    $this->sslCheck = $sslCheck;
	    return $this;
    }

    public function isSSLCheck()
    {
        return $this->sslCheck;
    }

    public function setFrom($from): self
    {
        $this->mailer->From = $from;
        return $this;
    }

    public function setFromName(string $fromName): self
    {
        $this->mailer->FromName = $fromName;
        return $this;
    }

    public function addAddress(string $to): self
    {
        $this->mailer->addAddress($to);
        return $this;
    }

    public function addReplyTo(string $replyTo): self
    {
        $this->mailer->AddReplyTo($replyTo);
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->mailer->Subject = utf8_decode($subject);
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->mailer->Body = utf8_decode($body);
        return $this;
    }

    public function send(): self
    {
        if (!$this->isSSLCheck()) {
            $this->smtpConnect();
        }
        $this->mailer->IsHTML(true);
        $this->mailer->send();
        $this->mailer->SmtpClose();
        return $this;
    }

    private function smtpConnect(): self
    {
        $this->mailer->smtpConnect([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        return $this;
    }
}
