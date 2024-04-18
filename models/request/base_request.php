<?php

namespace Models\Request;

require_once('models/request/common/model.php');
require_once('models/request/ticket/ticket.php');
require_once('models/user/service.php');
require_once('models/request/common/budget_data.php');
require_once('models/tool/sql.php');
require_once('models/tool/mailer.php');
require_once('models/tool/parameters.php');

use Models\Request\Common\Model;
use Models\Request\Common\Service;
use Models\User\User;
use Models\Tool\Sql;
use Models\Request\Common\BudgetData;
use Models\Request\MissionOrder\MissionOrder;
use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\Request\Ticket\Ticket;
use Models\Tool\Mailer;
use Models\Tool\Parameters;

/**
 * Class BaseRequest
 *
 * This class represents a base request.
 *
 * @package Models\Request
 */
class BaseRequest
{
    /** @var int The status of the request waiting for validation. */
    const STATUS_WAITING_VALIDATION = 1;

    /** @var int The status of the request being modified. */
    const STATUS_MODIFY = 2;

    /** @var int The status of the rejected request. */
    const STATUS_REJECT = 3;

    /** @var int The status of the validated request. */
    const STATUS_VALID = 4;

    /** @var int The status of the cancelled request. */
    const STATUS_CANCEL = 5;

    /** @var string The path of the mail template for a new request. */
    const TEMPLATE_MAIL_NEW = "template/request/mail/new_request.php";

    /** @var string The path of the mail template for a modified request. */
    const TEMPLATE_MAIL_MODIFY = "template/request/mail/request_modify.php";

    /** @var string The path of the mail template for a finished modified request. */
    const TEMPLATE_MAIL_MODIFY_DONE = "template/request/mail/request_modify_done.php";

    /** @var string The path of the mail template for a rejected request. */
    const TEMPLATE_MAIL_REJECTED = "template/request/mail/request_rejected.php";

    /** @var string The path of the mail template for a validated request. */
    const TEMPLATE_MAIL_VALIDATED = "template/request/mail/request_validated.php";

    /** @var string The path of the mail template for a guest notification. */
    const TEMPLATE_MAIL_GUEST = "template/request/mail/guest_notification.php";

    /** @var int|null The identifier of the request. */
    private $id;

    /** @var string The title of the request. */
    private $title;

    /** @var string|null The additional budget information of the request. */
    private $additionalBudgetInformation;

    /** @var string|null The comment of the request. */
    private $comment;

    /** @var User The user owner of the request. */
    private $owner;

    /** @var BudgetData The budget data of the request. */
    private $budgetData;

    /** @var bool|null Indicates if the request is a model. */
    private $isModel;

    /** @var Service The service associated with the request. */
    private $service;

    /** @var User[] The validators of the request. */
    private $validators;

    /** @var string The date of the request. */
    private $date;

    /** @var string The alert date of the request. */
    private $datealerte;

    /** @var Model[] The models associated with the request. */
    private $models;

    /** @var int|null The previous status of the request. */
    private $statusOld;

    /** @var int The current status of the request. */
    private $status;

    /** @var Mailer The mail service to send emails. */
    private $mailer;

    /** @var Parameters The parameters of the application. */
    protected $parameters;

    /** @var Sql The database manager. */
    protected $sql;

    /**
     * Constructor of the BaseRequest class.
     */
    public function __construct()
    {
        $this->owner = new User();
        $this->title = '';
        $this->budgetData = new BudgetData();
        $this->isModel = false;
        $this->service = new Service();
        $this->validators = [];
        $this->models = [];
        $this->date = '';
        $this->datealerte = '';
        $this->status = self::STATUS_WAITING_VALIDATION;
        $this->sql = Sql::getInstance();
        $this->mailer = Mailer::getInstance();
        $this->parameters = Parameters::getInstance();
    }

    /**
     * Get the identifier of the request.
     *
     * @return int|null The identifier of the request.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the identifier of the request.
     *
     * @param int|null $id The identifier of the request.
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the title of the request.
     *
     * @return string The title of the request.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title of the request.
     *
     * @param string $title The title of the request.
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the comment of the request.
     *
     * @return string|null The comment of the request.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set the comment of the request.
     *
     * @param string|null $comment The comment of the request.
     * @return self
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get the additional budget information of the request.
     *
     * @return string|null The additional budget information of the request.
     */
    public function getAdditionalBudgetInformation(): ?string
    {
        return $this->additionalBudgetInformation;
    }

    /**
     * Set the additional budget information of the request.
     *
     * @param string|null $additionalBudgetInformation The additional budget information of the request.
     * @return self
     */
    public function setAdditionalBudgetInformation(?string $additionalBudgetInformation)
    {
        $this->additionalBudgetInformation = $additionalBudgetInformation;
        return $this;
    }

    /**
     * Get the budget data of the request.
     *
     * @return BudgetData The budget data of the request.
     */
    public function getBudgetData(): BudgetData
    {
        return $this->budgetData;
    }

    /**
     * Set the budget data of the request.
     *
     * @param BudgetData $budgetData The budget data of the request.
     * @return self
     */
    public function setBudgetData(BudgetData $budgetData): self
    {
        $this->budgetData = $budgetData;
        return $this;
    }

    /**
     * Get the user owner of the request.
     *
     * @return User The user owner of the request.
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * Set the user owner of the request.
     *
     * @param User $owner The user owner of the request.
     * @return self
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Check if the user is the owner of the request.
     *
     * @param int $idUser The identifier of the user to check.
     * @return bool True if the user is the owner of the request, false otherwise.
     */
    public function isOwner(int $idUser): bool
    {
        return $this->getOwner()->getId() == $idUser;
    }

    /**
     * Get the current user identifier.
     *
     * @return int The current user identifier.
     */
    public function getCurrentUser(): int
    {
        return $this->currentUser;
    }

    /**
     * Set the current user identifier.
     *
     * @param int $currentUser The current user identifier.
     * @return self
     */
    public function setCurrentUser(int $currentUser): self
    {
        $this->currentUser = $currentUser;
        return $this;
    }

    /**
     * Get the validators of the request.
     *
     * @return User[] The validators of the request.
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * Set the validators of the request.
     *
     * @param User[] $validators The validators of the request.
     * @return self
     */
    public function setValidators(array $validators): self
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * Add a validator to the request.
     *
     * @param User $validator The validator to add.
     * @return self
     */
    public function addValidator(User $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Check if the request has a specific validator.
     *
     * @param int $idValidatorToCheck The identifier of the validator to check.
     * @return bool True if the request has the validator, false otherwise.
     */
    public function hasValidator(int $idValidatorToCheck): bool
    {
        foreach ($this->getValidators() as $validator) {
            if ($validator->getId() == $idValidatorToCheck) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validators of the request as a string.
     *
     * @param string $separator The separator to use between validators.
     * @return string The validators of the request as a string.
     */
    public function getValidatorAsString(string $separator = ';'): string
    {
        $validatorsString = '';

        foreach ($this->validators as $validator) {
            if (strlen($validatorsString) > 0) {
                $validatorsString .= ' '.$separator.' ';
            }
            $validatorsString .= $validator->getFullName();
        }

        return $validatorsString;
    }

    /**
     * Get the service associated with the request.
     *
     * @return Service The service associated with the request.
     */
    public function getService(): Service
    {
        return $this->service;
    }

    /**
     * Set the service associated with the request.
     *
     * @param Service $service The service associated with the request.
     * @return self
     */
    public function setService(Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get the date of the request.
     *
     * @return string The date of the request.
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Set the date of the request.
     *
     * @param string $date The date of the request.
     * @return self
     */
    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get the alert date of the request.
     *
     * @return string The alert date of the request.
     */
    public function getDateAlerte(): string
    {
        return $this->datealerte;
    }

    /**
     * Set the alert date of the request.
     *
     * @param string $date The alert date of the request.
     * @return self
     */
    public function setDateAlerte(string $date): self
    {
        $this->datealerte = $date;
        return $this;
    }

    /**
     * Get the models associated with the request.
     *
     * @return Model[] The models associated with the request.
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * Set the models associated with the request.
     *
     * @param Model[] $models The models associated with the request.
     * @return self
     */
    public function setModels(array $models): self
    {
        $this->models = $models;
        return $this;
    }

    /**
     * Add a model to the request.
     *
     * @param Model $modelToAdd The model to add.
     * @return self
     */
    public function addModel(Model $modelToAdd): self
    {
        $this->models[] = $modelToAdd;
        return $this;
    }

    /**
     * Remove a model from the request.
     *
     * @param Model $modelToRemove The model to remove.
     * @return self
     */
    public function removeModel(Model $modelToRemove): self
    {
        $models = [];

        foreach ($this->models as $model) {
            if ($model->getId() == $modelToRemove->getId()) {
                continue;
            }

            $models[] = $model;
        }
        $this->setModels($models);

        return $this;
    }

    /**
     * Check if the request has a specific model.
     *
     * @param int|null $idModelToCheck The identifier of the model to check.
     * @return Model|null The model if the request has it, null otherwise.
     */
    public function hasModel(?int $idModelToCheck): ?Model
    {
        foreach ($this->getModels() as $model) {
            if ($this instanceof PurchaseOrder && $model->getPurchaseOrder()->getId() == $idModelToCheck) {
                return $model;
            }
            if ($this instanceof MissionOrder && $model->getMissionOrder()->getId() == $idModelToCheck) {
                return $model;
            }
        }

        return null;
    }

    public function getIsModel(): ?bool
    {
        return $this->isModel;
    }

    /**
     * Set if the request is a model.
     *
     * @param bool|null $isModel If the request is a model.
     * @return self
     */
    public function setIsModel(?bool $isModel): self
    {
        $this->isModel = $isModel;
        return $this;
    }

    /**
     * Get the previous status of the request.
     *
     * @return int|null The previous status of the request.
     */
    public function getStatusOld()
    {
        return $this->statusOld;
    }

    /**
     * Set the previous status of the request.
     *
     * @param int|null $statusOld The previous status of the request.
     */
    public function setStatusOld($statusOld): void
    {
        $this->statusOld = $statusOld;
    }

    /**
     * Get the current status of the request.
     *
     * @return int The current status of the request.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the current status of the request.
     *
     * @param int $status The current status of the request.
     * @param bool $updateStatusOld If the old status should be updated.
     * @return self
     */
    public function setStatus(int $status, bool $updateStatusOld = false): self
    {
        if ($updateStatusOld) {
            $this->statusOld = $this->status;
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Check if the request is a purchase order.
     *
     * @return bool True if the request is a purchase order, false otherwise.
     */
    public function isPurchaseOrder()
    {
        return $this instanceof PurchaseOrder;
    }

    /**
     * Check if the request is a mission order.
     *
     * @return bool True if the request is a mission order, false otherwise.
     */
    public function isMissionOrder()
    {
        return $this instanceof MissionOrder;
    }

    /**
     * Check if the request is a ticket.
     *
     * @return bool True if the request is a ticket, false otherwise.
     */
    public function isTicket()
    {
        return $this instanceof Ticket;
    }

    /**
     * Check if a notification can be sent for the request and send it if applicable.
     *
     * @param array $extraDatas Additional data for the notification (optional).
     * @return self Returns the instance of the class.
     */
    public function canNotification(array $extraDatas = []): self
    {
        if ($this->hasValidator($this->getOwner()->getId()) && $this->getStatus() != self::STATUS_VALID) {
            return $this;
        }

        switch ($this->getStatus()) {
            case self::STATUS_WAITING_VALIDATION:
                ($this->statusOld == self::STATUS_MODIFY)
                    ? $this->sendNotificationModifyDone()
                    : $this->sendNotificationNew();
                break;
            case self::STATUS_MODIFY:
                $this->sendNotificationModify($extraDatas);
                break;
            case self::STATUS_REJECT:
                $this->sendNotificationRejected();
                break;
            case self::STATUS_VALID:
                $this->sendNotificationValidated();
                break;
        }
        return $this;
    }

    /**
     * Send a notification for a new request.
     *
     * @return self Returns the instance of the class.
     */
    private function sendNotificationNew(): self
    {
        $toList = [];

        foreach ($this->getValidators() as $validator) {
            $toList[] = $validator->getEmail();
        }

        if (count($toList)) {
            $title = '';
            if ($this->isMissionOrder()) {
                $title .= T_('Nouvel Ordre de mission attribué');
            } else if ($this->isPurchaseOrder()) {
                $title .= T_('Nouveau Bon de commande attribué');
            }
            $title .= ' - ';
            $title .= $this->getTitle();
            $title .= ' - ';
            $title .= $this->getOwner()->getFullName();
            $this->sendMail($this->parameters->getMailFromAddress(), $toList, $title, $this->getMailTemplate(self::TEMPLATE_MAIL_NEW));
        }

        return $this;
    }

    /**
     * Send a reminder notification for a request awaiting validation.
     *
     * @return self Returns the instance of the class.
     */
    public function sendNotificationRappel(): self
    {
        $toList = [];

        foreach ($this->getValidators() as $validator) {
            $toList[] = $validator->getEmail();
        }

        if (count($toList)) {
            $title = '';
            if ($this->isMissionOrder()) {
                $title .= T_('Rappel - Vous avez un Ordre de Mission à valider');
            } else if ($this->isPurchaseOrder()) {
                $title .= T_('Rappel - Vous avez un Bon de Commande à valider');
            }
            $title .= ' - ';
            $title .= $this->getTitle();
            $title .= ' - ';
            $title .= $this->getOwner()->getFullName();

            $this->sendMail($this->parameters->getMailFromAddress(), $toList, $title, $this->getMailTemplate(self::TEMPLATE_MAIL_NEW));

        }

        return $this;
    }

    /**
     * Send a notification for a request that has been modified.
     *
     * @param array $extraDatas Additional data for the notification (optional).
     * @return self Returns the instance of the class.
     */
    private function sendNotificationModify(array $extraDatas = []): self
    {
        $title = '';
        if ($this->isMissionOrder()) {
            $title .= T_('Ordre de mission à modifier');
        } else if ($this->isPurchaseOrder()) {
            $title .= T_('Bon de commande à modifier');
        }
        $title .= ' - ';
        $title .= $this->getTitle();
        $title .= ' - ';
        $title .= $this->getOwner()->getFullName();
        $this->sendMail($this->parameters->getMailFromAddress(), [$this->getOwner()->getEmail()], $title, $this->getMailTemplate(self::TEMPLATE_MAIL_MODIFY, $extraDatas));

        return $this;
    }


    private function sendNotificationModifyDone(array $extraDatas = []): self
    {
        $toList = [];

        foreach ($this->getValidators() as $validator) {
            $toList[] = $validator->getEmail();
        }

        if (count($toList)) {
            $title = '';
            if ($this->isMissionOrder()) {
                $title .= T_('Ordre de mission modifié');
            } else if ($this->isPurchaseOrder()) {
                $title .= T_('Bon de commande modifié');
            }
            $title .= ' - ';
            $title .= $this->getTitle();
            $title .= ' - ';
            $title .= $this->getOwner()->getFullName();
            $this->sendMail($this->parameters->getMailFromAddress(), $toList, $title, $this->getMailTemplate(self::TEMPLATE_MAIL_MODIFY_DONE));
        }

        return $this;
    }

    /**
     * Send a notification for a request that has been rejected.
     *
     * @return self Returns the instance of the class.
     */
    private function sendNotificationRejected(): self
    {
        $title = '';
        if ($this->isMissionOrder()) {
            $title .= T_('Ordre de mission refusé');
        } else if ($this->isPurchaseOrder()) {
            $title .= T_('Bon de commande refusé');
        }
        $title .= ' - ';
        $title .= $this->getTitle();
        $title .= ' - ';
        $title .= $this->getOwner()->getFullName();
        $this->sendMail($this->parameters->getMailFromAddress(), [$this->getOwner()->getEmail()], $title, $this->getMailTemplate(self::TEMPLATE_MAIL_REJECTED));

        return $this;
    }

    /**
     * Send a notification for a request that has been validated.
     *
     * @return self Returns the instance of the class.
     */
    private function sendNotificationValidated(): self
    {
        $toList = [];

        if (!$this->hasValidator($this->getOwner()->getId())) {
            $toList[] = $this->getOwner()->getEmail();
        }

        if ($this->parameters->isMailNewTicket() && $this->parameters->getMailNewTicketAddress()) {
            $toList[] = $this->parameters->getMailNewTicketAddress();
        }

        $title = '';
        if ($this->isMissionOrder()) {
            $title .= T_('Ordre de mission validé');
        } else if ($this->isPurchaseOrder()) {
            $title .= T_('Bon de commande validé');
        }
        $title .= ' - ';
        $title .= $this->getTitle();
        $title .= ' - ';
        $title .= $this->getOwner()->getFullName();

        $this->sendMail($this->parameters->getMailFromAddress(), $toList, $title, $this->getMailTemplate(self::TEMPLATE_MAIL_VALIDATED));

        return $this;
    }

    /**
     * Send an email.
     *
     * @param string $from The sender's email address.
     * @param array $toList An array of recipient email addresses.
     * @param string $subject The email subject.
     * @param string $body The email body.
     * @return self Returns the instance of the class.
     */
    public function sendMail(string $from, array $toList, string $subject, string $body): self
    {
        if($body!=""){
            if (!$from || count($toList) == 0) {
                return $this;
            }
    
            foreach ($toList as $to) {
                $this->mailer->addAddress($to);
            }
    
            $this->mailer
                ->setFrom($from)
                ->addReplyTo($from)
                ->setSubject($subject)
                ->setBody($body)
                ->send();

            $this->mailer->mailer->clearAddresses();
        }
        

        return $this;
    }

    /**
     * Get the HTML content of an email template.
     *
     * @param string $template The path to the email template file.
     * @param array $extraDatas Additional data for the template (optional).
     * @return string The HTML content of the email template.
     */
    protected function getMailTemplate(string $template, array $extraDatas = []): string
    {
        ob_start();

        $request = $this;
        include_once($template);

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function canValidate($idUser)
    {
        if (!$this->hasValidator($idUser) && $_SESSION['profile_id']!=4) {
            return false;
        }
        if ($this->getStatus() != self::STATUS_WAITING_VALIDATION) {
            return false;
        }
        return true;
    }

    /**
     * Check if the request can be saved.
     * @return bool
     */
    public function canSave()
    {
        $statusCanSave = [self::STATUS_WAITING_VALIDATION, self::STATUS_MODIFY];

        if (!$this->getIsModel() && !in_array($this->getStatus(), $statusCanSave)) {
            return false;
        }

        return true;
    }

    protected function checkForm(array &$datas, array $files): bool
    {
        if (!$this->canSave()) {
            echo DisplayMessage('error',T_("Vous ne pouvez pas modifier une demande refusée ou validée"));
            return false;
        }

        if (!$datas['title']) {
            echo DisplayMessage('error',T_("Le champ Titre est requis"));
            return false;
        }
        if (!$datas['service']) {
            echo DisplayMessage('error',T_("Le champ Service est requis"));
            return false;
        }

        return true;
    }

    protected function getDatas(array $datas)
    {
        $validators = [];

        foreach ($datas['validators'] as $idValidator) {
            $validator = new User();
            $validator->setId($idValidator);
            $validators[] = $validator;
        }

        $this
            ->setTitle($datas['title'])
            ->setAdditionalBudgetInformation($datas['additional-budget-information'])
            ->setComment($datas['comment'])
            ->setValidators($validators);

        $this
            ->getService()
            ->getDatas($datas);
    }

    //uniquement les requêtes a valider ou a modifier
    public static function getAllRequest()
    {
        $queryMissionOrder = "
            SELECT dm.id, 'mission_order' as type, dm.title, dm.id_owner, tu1.firstname as firstname_owner, tu1.lastname as lastname_owner,
                   dmv.id_validator, tu2.firstname as firstname_validator, tu2.lastname as lastname_validator, tu2.mail as email_validator,
                   dm.status, dm.date_demande, dm.date_alerte
            FROM dmission_order dm
            LEFT JOIN dmission_order_validators dmv ON dm.id= dmv.id_mission_order
            LEFT JOIN tusers tu1 ON tu1.id = dm.id_owner
            LEFT JOIN tusers tu2 ON tu2.id = dmv.id_validator
            WHERE dm.status!=4 AND dm.status!=3 AND dm.status!=5
        ";
        $queryPurchaseOrder = "
            SELECT dp.id, 'purchase_order' as type, dp.title, dp.id_owner, tu1.firstname as firstname_owner, tu1.lastname as lastname_owner,
                   dpv.id_validator, tu2.firstname as firstname_validator, tu2.lastname as lastname_validator, tu2.mail as email_validator,
                   dp.status, dp.date_demande, dp.date_alerte
            FROM dpurchase_order dp
            LEFT JOIN dpurchase_order_validators dpv ON dp.id= dpv.id_purchase_order
            LEFT JOIN tusers tu1 ON tu1.id = dp.id_owner
            LEFT JOIN tusers tu2 ON tu2.id = dpv.id_validator
            WHERE dp.status!=4 AND dp.status!=3 AND dp.status!=5
        ";

        $sql = Sql::getInstance();
        $resultMO = $sql->query($queryMissionOrder);
        $resultPO = $sql->query($queryPurchaseOrder);

        $retMO=[];
        $retPO=[];

        foreach ($resultMO as $row) {
            // add new validator to Mission Order
            $validator = new User();
            $validator
                ->setId($row['id_validator'])
                ->setFirstName($row['firstname_validator'])
                ->setLastName($row['lastname_validator'])
                ->setEmail($row['email_validator']);

            if (isset($retMO['mo'.$row['id']])) {
                $retMO['mo'.$row['id']]->addValidator($validator);
                continue;
            }

            $user = new User();
            $user->setId($row['id_owner']);

            $missionOrder = new MissionOrder();
            $missionOrder
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setDate($row['date_demande'])
                ->setDateAlerte($row['date_alerte'])
                ->setOwner($user)
                ->setStatus($row['status'])
                ->addValidator($validator);

            $retMO['mo'.$missionOrder->getId()] = $missionOrder;
        }

        foreach($resultPO as $row){
            // add new validator to Purchase Order
            $validator = new User();
            $validator
                ->setId($row['id_validator'])
                ->setFirstName($row['firstname_validator'])
                ->setLastName($row['lastname_validator'])
                ->setEmail($row['email_validator']);

            if (isset($retPO['po'.$row['id']])) {
                $retPO['po'.$row['id']]->addValidator($validator);
                continue;
            }

            $user = new User();
            $user->setId($row['id_owner']);

            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setDate($row['date_demande'])
                ->setDateAlerte($row['date_alerte'])
                ->setOwner($user)
                ->setStatus($row['status'])
                ->addValidator($validator);

            $retPO['po'.$purchaseOrder->getId()] = $purchaseOrder;
        }


        return array('MO' => $retMO , 'PO' => $retPO);

    }

    public static function getCollection(array $datas, $page = 1, $limit = 14): array
    {
        $page = 1;
        $results = [
            'results' => [],
            'count' => 0
        ];
        $requests = [];

        $orderValueWhiteList = ['id', 'type', 'title', 'lastname_owner', 'lastname_validator', 'status'];
        $orderDirectionWhiteList = ['ASC', 'DESC'];
        $orderValue = 'id';
        $orderDirection = 'DESC';

        if (in_array($datas['order-value'], $orderValueWhiteList)) {
            $orderValue = $datas['order-value'];
        }
        if (in_array($datas['order-direction'], $orderDirectionWhiteList)) {
            $orderDirection = $datas['order-direction'];
        }

        if ($datas['request-page']) {
            $page = $datas['request-page'];
        }

        $where = " AND status != :status_cancel";
        $params = [
            'status_cancel' => self::STATUS_CANCEL
        ];
        $limitQuery = " LIMIT ".$limit." OFFSET ".$limit * ($page - 1);
        $queryMissionOrder = "
            SELECT dm.id, dm.date_start as date_start, 'mission_order' as type, dm.title, dm.id_owner, tu1.firstname as firstname_owner, tu1.lastname as lastname_owner,
                   dmv.id_validator, tu2.firstname as firstname_validator, tu2.lastname as lastname_validator,
                   dm.status, dm.date_demande, dm.date_alerte
            FROM dmission_order dm
            LEFT JOIN dmission_order_validators dmv ON dm.id= dmv.id_mission_order
            LEFT JOIN tusers tu1 ON tu1.id = dm.id_owner
            LEFT JOIN tusers tu2 ON tu2.id = dmv.id_validator
        ";
        $queryPurchaseOrder = "
            SELECT dp.id, dp.date_demande as date_start, 'purchase_order' as type, dp.title, dp.id_owner, tu1.firstname as firstname_owner, tu1.lastname as lastname_owner,
                   dpv.id_validator, tu2.firstname as firstname_validator, tu2.lastname as lastname_validator,
                   dp.status, dp.date_demande, dp.date_alerte
            FROM dpurchase_order dp
            LEFT JOIN dpurchase_order_validators dpv ON dp.id= dpv.id_purchase_order
            LEFT JOIN tusers tu1 ON tu1.id = dp.id_owner
            LEFT JOIN tusers tu2 ON tu2.id = dpv.id_validator
        ";

        if ($datas['filter-id']) {
            $where .= " AND id = :id";
            $params['id'] = $datas['filter-id'];
        }
        if ($datas['filter-type']) {
            $where .= " AND type = :type";
            $params['type'] = $datas['filter-type'];
        }
        if ($datas['filter-title']) {
            $where .= " AND title like :title";
            $params['title'] = '%'.$datas['filter-title'].'%';
        }
        if ($datas['filter-datealerte']) {
            $where .= " AND date_alerte like :date_alerte";
            $params['date_alerte'] = '%'.$datas['filter-datealerte'].'%';
        }
        if (isset($datas['filter-datedemande'])) {
            $where .= " AND date_demande like :date_demande";
            $params['date_demande'] = '%'.$datas['filter-datedemande'].'%';
        }
        if ($datas['filter-owner']) {
            $where .= " AND id_owner = :owner";
            $params['owner'] = $datas['filter-owner'];
        } else {
            if($_SESSION['profile_id']!=4)
            {
              $where .= " AND (id_owner = :id_owner OR id_validator= :id_validator)";
              $params['id_owner'] = $_SESSION['user_id'];
              $params['id_validator'] = $_SESSION['user_id'];
            }
        }
        if ($datas['filter-validator']) {
            $where .= " AND id_validator = :validator";
            $params['validator'] = $datas['filter-validator'];
        }
        if ($datas['filter-status']) {
            $where .= " AND status = :status";
            $params['status'] = $datas['filter-status'];
        }

        $order = " ORDER BY ".$orderValue.' '.$orderDirection;
        $subQuery = $queryMissionOrder .' UNION '. $queryPurchaseOrder;

        $query = "
            SELECT id, type, title, id_owner, firstname_owner, lastname_owner, firstname_validator, lastname_validator, id_validator, status, date_demande, date_alerte, date_start
            FROM (".$subQuery.") request
            WHERE 1=1".$where.$order.$limitQuery;

        $sql = Sql::getInstance();
        $resultsQuery = $sql->query($query, $params);

        foreach ($resultsQuery as $row) {

            switch ($row['type']) {
                case 'mission_order':
                    // add new validator to Mission Order
                    $validator = new User();
                    $validator
                        ->setId($row['id_validator'])
                        ->setFirstName($row['firstname_validator'])
                        ->setLastName($row['lastname_validator']);

                    if (isset($requests['mo'.$row['id']])) {
                        $requests['mo'.$row['id']]->addValidator($validator);
                        continue 2;
                    }

                    $user = new User();
                    $user->setId($row['id_owner']);

                    $missionOrder = new MissionOrder();
                    $missionOrder
                        ->setId($row['id'])
                        ->setTitle($row['title'])
                        ->setOwner($user)
                        ->setStatus($row['status'])
                        ->setDate($row['date_demande'])
			->setDateAlerte($row['date_alerte'])
		        ->setDateStart($row['date_start'])
                        ->addValidator($validator);

                    $requests['mo'.$missionOrder->getId()] = $missionOrder;
                    break;
                case 'purchase_order':
                    // add new validator to Purchase Order
                    $validator = new User();
                    $validator
                        ->setId($row['id_validator'])
                        ->setFirstName($row['firstname_validator'])
                        ->setLastName($row['lastname_validator']);

                    if (isset($requests['po'.$row['id']])) {
                        $requests['po'.$row['id']]->addValidator($validator);
                        continue 2;
                    }

                    $user = new User();
                    $user->setId($row['id_owner']);

                    $purchaseOrder = new PurchaseOrder();
                    $purchaseOrder
                        ->setId($row['id'])
                        ->setTitle($row['title'])
                        ->setOwner($user)
                        ->setStatus($row['status'])
                        ->setDate($row['date_demande'])
                        ->setDateAlerte($row['date_alerte'])
                        ->addValidator($validator);

                    $requests['po'.$purchaseOrder->getId()] = $purchaseOrder;
                    break;
            }
        }
        $results['results'] = $requests;

        $query2 = "
            SELECT COUNT(*) as cpt
            FROM (".$subQuery.") request
            WHERE 1=1".$where.$order;

        $resultsQuery = $sql->query($query2, $params);

        foreach ($resultsQuery as $row) {
            $cpt = (float)$row['cpt'];
            $results['count'] = ceil($cpt / $limit);
        }

        return $results;
    }
}
