<?php

namespace Models\Tool;

require_once('models/tool/sql.php');

class Parameters
{
    private static $instance = null;
    private $notificationState;
    private $notificationEnable;
    private $alerteDemande;
    private $intervalAlerteDemande;
    private $mailNewTicket;
    private $mailNewTicketAddress;
    private $mailFromAddress;
    private $sql;
    private $fixedValidators;

    public function __construct()
    {
        $this->notificationState = null;
        $this->notificationEnable = true;
        $this->alerteDemande = false;
        $this->intervalAlerteDemande = '';
        $this->mailNewTicket = false;
        $this->mailNewTicketAddress = '';
        $this->sql = Sql::getInstance();
        $this->fixedValidators = false;
    }

    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new Parameters();
            self::$instance->load();
        }

        return self::$instance;
    }

    public function getNotificationState(): ?string
    {
        return $this->notificationState;
    }

    public function setNotificationState(?string $notificationState): self
    {
        $this->notificationState = $notificationState;
        return $this;
    }

    public function isNotificationEnable(): ?bool
    {
        return $this->notificationEnable;
    }

    public function setNotificationEnable(?bool $notificationEnable): self
    {
        $this->notificationEnable = $notificationEnable;
        return $this;
    }

    public function isAlerteDemande(): ?bool
    {
        return $this->alerteDemande;
    }

    public function setAlerteDemande(?bool $alerteDemande): self
    {
        $this->alerteDemande = $alerteDemande;
        return $this;
    }

    public function getIntervalAlerteDemande(): string
    {
        return $this->intervalAlerteDemande;
    }

    public function setIntervalAlerteDemande(string $intervalAlerteDemande): self
    {
        $this->intervalAlerteDemande = $intervalAlerteDemande;
        return $this;
    }

    public function isMailNewTicket(): bool
    {
        return $this->mailNewTicket;
    }

    public function setMailNewTicket(bool $mailNewTicket): self
    {
        $this->mailNewTicket = $mailNewTicket;
        return $this;
    }

    public function getMailNewTicketAddress(): string
    {
        return $this->mailNewTicketAddress;
    }

    public function setMailNewTicketAddress(string $mailNewTicketAddress): self
    {
        $this->mailNewTicketAddress = $mailNewTicketAddress;
        return $this;
    }

    public function getMailFromAddress(): string
    {
        return $this->mailFromAddress;
    }

    public function setMailFromAddress(string $mailFromAddress): self
    {
        $this->mailFromAddress = $mailFromAddress;
        return $this;
    }

    public function isFixedValidators(): bool
    {
        return $this->fixedValidators;
    }

    public function setFixedValidators(bool $fixedValidators): self
    {
        $this->fixedValidators = $fixedValidators;
        return $this;
    }

    public function getDatas(array $datas)
    {	
        $notificationSate = (isset($datas['notification-state'])) ? $datas['notification-state'] : null;
        $notificationEnable = (isset($datas['notification-enable'])) ? $datas['notification-enable'] : null;
        $alerteDemande = (isset($datas['alerte-demande'])) ? $datas['alerte-demande'] : null;
        $intervalAlerteDemande = (isset($datas['interval-alerte-demande'])) ? $datas['interval-alerte-demande'] : null;
        $fixedValidators = (isset($datas['fixed-validators'])) ? $datas['fixed-validators'] : null;

        return $this
            ->setNotificationState($notificationSate)
            ->setNotificationEnable($notificationEnable)
            ->setAlerteDemande($alerteDemande)
            ->setIntervalAlerteDemande($intervalAlerteDemande)
            ->setFixedValidators($fixedValidators);
    }

    public function load(): self
    {
        $query = "SELECT dp.`notification_state`, dp.`notification_enable`, dp.`alerte_demande`, dp.`interval_alerte_demande`, dp.`fixed_validators`, tp.`mail_newticket`, tp.`mail_newticket_address`, tp.`mail_from_adr` 
            FROM `dparameters` dp, `tparameters` tp";

        $results = $this->sql->query($query);

        if (isset($results[0])) {
            $parameters = $results[0];
            $this
                ->setNotificationState($parameters['notification_state'])
                ->setNotificationEnable($parameters['notification_enable'])
                ->setAlerteDemande($parameters['alerte_demande'])
                ->setIntervalAlerteDemande($parameters['interval_alerte_demande'])
                ->setMailNewTicket($parameters['mail_newticket'])
                ->setMailNewTicketAddress($parameters['mail_newticket_address'])
                ->setMailFromAddress($parameters['mail_from_adr'])
                ->setFixedValidators($parameters['fixed_validators']);

        }

        return $this;
    }

    public function save(): self
    {
        $query = "INSERT INTO `dparameters` (`id`,`notification_state`,`notification_enable`,`alerte_demande`,`interval_alerte_demande`,`fixed_validators`)
            VALUES (1, :notification_state, :notification_enable, :alerte_demande, :interval_alerte_demande, :fixed_validators)
            ON DUPLICATE KEY UPDATE
            notification_state=:notification_state,
            notification_enable=:notification_enable,
            alerte_demande=:alerte_demande,
            interval_alerte_demande=:interval_alerte_demande,
            fixed_validators=:fixed_validators";
        $params = [
            'notification_state' => $this->getNotificationState(),
            'notification_enable' => $this->isNotificationEnable(),
            'alerte_demande' => $this->isAlerteDemande(),
            'interval_alerte_demande' => $this->getIntervalAlerteDemande(),
            'fixed_validators' => $this->isFixedValidators(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }
}