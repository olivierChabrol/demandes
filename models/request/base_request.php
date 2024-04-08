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

class BaseRequest
{
    const STATUS_WAITING_VALIDATION = 1;
    const STATUS_MODIFY = 2;
    const STATUS_REJECT = 3;
    const STATUS_VALID = 4;
    const STATUS_CANCEL = 5;
    const TEMPLATE_MAIL_NEW = "template/request/mail/new_request.php";
    const TEMPLATE_MAIL_MODIFY = "template/request/mail/request_modify.php";
    const TEMPLATE_MAIL_MODIFY_DONE = "template/request/mail/request_modify_done.php";
    const TEMPLATE_MAIL_REJECTED = "template/request/mail/request_rejected.php";
    const TEMPLATE_MAIL_VALIDATED = "template/request/mail/request_validated.php";

    private $id;
    private $title;
    private $additionalBudgetInformation;
    private $comment;
    private $owner;
    private $budgetData;
    private $currentUser;
    private $validators;
    private $service;
    private $date;
    private $datealerte;
    private $models;
    private $isModel;
    private $statusOld;
    private $status;
    private $mailer;
    private $parameters;
    protected $sql;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getAdditionalBudgetInformation(): ?string
    {
        return $this->additionalBudgetInformation;
    }

    public function setAdditionalBudgetInformation(?string $additionalBudgetInformation)
    {
        $this->additionalBudgetInformation = $additionalBudgetInformation;
        return $this;
    }

    public function getBudgetData(): BudgetData
    {
        return $this->budgetData;
    }

    public function setBudgetData(BudgetData $budgetData): self
    {
        $this->budgetData = $budgetData;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function isOwner(int $idUser): bool
    {
        return $this->getOwner()->getId() == $idUser;
    }

    public function getCurrentUser(): int
    {
        return $this->currentUser;
    }

    public function setCurrentUser(int $currentUser): self
    {
        $this->currentUser = $currentUser;
        return $this;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    public function setValidators(array $validators): self
    {
        $this->validators = $validators;
        return $this;
    }

    public function addValidator(User $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function hasValidator(int $idValidatorToCheck): bool
    {
        foreach ($this->getValidators() as $validator) {
            if ($validator->getId() == $idValidatorToCheck) {
                return true;
            }
        }

        return false;
    }

    public function getValidatorHasString(string $separator = ';'): string
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

    public function getService(): Service
    {
        return $this->service;
    }

    public function setService(Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getDateAlerte(): string
    {
        return $this->datealerte;
    }

    public function setDateAlerte(string $date): self
    {
        $this->datealerte = $date;
        return $this;
    }

    public function getModels(): array
    {
        return $this->models;
    }

    public function setModels(array $models): self
    {
        $this->models = $models;
        return $this;
    }

    public function addModel(Model $modelToAdd): self
    {
        $this->models[] = $modelToAdd;
        return $this;
    }

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

    public function setIsModel(?bool $isModel): self
    {
        $this->isModel = $isModel;
        return $this;
    }

    public function getStatusOld()
    {
        return $this->statusOld;
    }

    public function setStatusOld($statusOld): void
    {
        $this->statusOld = $statusOld;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status, bool $updateStatusOld = false): self
    {
        if ($updateStatusOld) {
            $this->statusOld = $this->status;
        }

        $this->status = $status;
        return $this;
    }

    public function isPurchaseOrder()
    {
        return $this instanceof PurchaseOrder;
    }

    public function isMissionOrder()
    {
        return $this instanceof MissionOrder;
    }

    public function isTicket()
    {
        return $this instanceof Ticket;
    }

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
        }
        

        return $this;
    }

    private function getMailTemplate(string $template, array $extraDatas = []): string
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
