<?php

namespace Models\Request\PurchaseOrder;

require_once('models/request/base_request.php');
require_once('models/request/common/file.php');
require_once('models/request/common/model.php');
require_once('models/request/ticket/ticket.php');
require_once('models/user/user.php');
require_once('models/request/purchase_order/code_nacre.php');

use Models\Request\Common\File;
use Models\Request\BaseRequest;
use Models\Request\Ticket\Ticket;
use Models\User\User;

class PurchaseOrder extends BaseRequest
{
    private $quote=[];
    private $additionalOrderingInformation;
    private $supplierContact;
    private $deliveryAddress;
    private $purchaseCard;
    private $codeNacre;
    private $ticket;
    private $uploadDir;
    private $fileType;

    public function __construct()
    {
        $this->codeNacre = [];
        $this->ticket = new Ticket();
        $this->fileType = "quote";
        parent::__construct();
    }

    public function getType()
    {
      return $this->fileType;
    }

    public function getQuote(): array
    {
        return $this->quote;
    }

    public function setQuote(?File $quote): self
    {
      if($quote==null)//on reinit les quote
      {
        $this->quote=array();
      }
      if(!empty($quote))
      {
        $this->quote[] = $quote;

      }
        return $this;
    }

    public function setUploadDir()
    {
      $this->uploadDir=time();
    }

    public function getUploadDir(): ?string
    {
      return $this->uploadDir;
    }

    public function getAdditionalOrderingInformation(): ?string
    {
        return $this->additionalOrderingInformation;
    }

    public function setAdditionalOrderingInformation(?string $additionalOrderingInformation) {
        $this->additionalOrderingInformation = $additionalOrderingInformation;
        return $this;
    }

    public function getSupplierContact(): ?string
    {
        return $this->supplierContact;
    }

    public function setSupplierContact(?string $supplierContact): self
    {
        $this->supplierContact = $supplierContact;
        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function isPurchaseCard(): bool
    {
        return ($this->purchaseCard) ? true : false;
    }

    public function setPurchaseCard(?bool $purchaseCard): self
    {
        $this->purchaseCard = $purchaseCard;
        return $this;
    }

    public function getCodeNacre(): array
    {
        return $this->codeNacre;
    }

    public function setCodeNacre(array $codeNacre) {
        $this->codeNacre = $codeNacre;
        return $this;
    }

    public function hasCodeNacre(int $idCodeNacreToCheck): bool
    {
        foreach ($this->getCodeNacre() as $codeNacre) {
            if ($codeNacre->getId() == $idCodeNacreToCheck) {
                return true;
            }
        }

        return false;
    }

    public function getCodeNacreHasString(string $separator = ';'): string
    {
        $codeNacreString = '';

        foreach ($this->codeNacre as $codeNacre) {
            if (strlen($codeNacreString) > 0) {
                $codeNacreString .= ' '.$separator.' ';
            }
            $codeNacreString .= $codeNacre->getCode();
            if ($codeNacre->getWording()) {
                $codeNacreString .= ' - '.$codeNacre->getWording();
            }
        }

        return $codeNacreString;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function checkForm(array &$datas, array $files): bool
    {
        if (!parent::checkForm($datas, $files)) {
            return false;
        }
        if (!isset($datas['validators']) || !$datas['validators']) {
            echo DisplayMessage('error', T_("Le champ Responsables de la ligne budgétaire est requis"));
            return false;
        }
        if (!$datas['budget-data']) {
            echo DisplayMessage('error',T_("Le champ Données budgétaires est requis"));
            return false;
        }
        if (!isset($datas['code-nacre'])) {
            $datas['code-nacre'] = [];
        }

        return true;
    }

    public function getDatas(array $datas): self
    {
        parent::getDatas($datas);

        $codeNacreList = [];

        foreach ($datas['code-nacre'] as $idCodeNacre) {
            $codeNacre = new CodeNacre();
            $codeNacre->setId($idCodeNacre);
            $codeNacreList[] = $codeNacre;
        }

        $purchaseCard = (isset($datas['purchase-card'])) ? $datas['purchase-card'] : false;
        $this
            ->setAdditionalOrderingInformation($datas['additional-ordering-information'])
            ->setSupplierContact($datas['supplier-contact'])
            ->setDeliveryAddress($datas['delivery-address'])
            ->setPurchaseCard($purchaseCard)
            ->setCodeNacre($codeNacreList)
            ->setComment($datas['comment']);

        $this
            ->getBudgetData()
            ->getDatas($datas);

        return $this;
    }

    public function uploaded(File $file): self
    {
        $file->setIdPurchaseOrder($this->getId());
        $currentFiles = $this->getQuote();
        if ($currentFiles) {
          foreach($currentFiles as $currentFile)
          {
            //$currentFile->delete();//garde un seul fichier
          }
        }
        $file
            ->add();
        $this->setQuote($file);

        return $this;
    }

    public function loadFiles(): self
    {
        $this->setQuote(null);

        $files = File::loadFiles(null, $this->getId());

        foreach ($files as $file) {
            $this->setQuote($file);
        }

        return $this;
    }

    public function load(): self
    {
        $query = "
            SELECT dp.`title`, dp.`id_service`, dp.`budget_data`, dp.`additional_ordering_information`,
                dp.`budget_data`, dp.`additional_budget_information`, dp.`supplier_contact`, dp.`delivery_address`,
                dp.`purchase_card`, dp.`comment`, dp.`status`, ts.name as name_service, tscat.name as name_budget_data, tscat.cat as category_budget_data,
                dp.`id_owner`, tu1.`firstname` as firstname_owner, tu1.`lastname` as lastname_owner, tu1.`mail` as mail_owner, tu1.`custom1` as custom1_owner,
                dpv.`id_validator`, tu2.`firstname` as firstname_validator, tu2.`lastname` as lastname_validator, tu2.`mail` as email_validator
            FROM `dpurchase_order` dp
            LEFT JOIN `dpurchase_order_validators` dpv ON dp.id = dpv.id_purchase_order
            LEFT JOIN `tservices` ts ON ts.id = dp.id_service
            LEFT JOIN `tsubcat` tscat ON tscat.id = dp.budget_data
            LEFT JOIN `tusers` tu1 ON tu1.id = dp.id_owner
            LEFT JOIN `tusers` tu2 ON tu2.id = dpv.id_validator
            WHERE dp.id=:id";

        if($_SESSION['profile_id']!=4)//si l'utilisateur n'est pas admin alors on check si il est owner ou validator pour afficher la demande
        {
          $query.="
          AND (
              dp.id_owner=:id_owner OR
              dpv.id_validator=:id_validator
          )";

          $params = [
              'id' => $this->getId(),
              'id_owner' => $this->getCurrentUser(),
              'id_validator' => $this->getCurrentUser()
          ];
        }
        else {
          $params = [
              'id' => $this->getId()
          ];
        }



        $results = $this->sql->query($query, $params);

        if (count($results) == 0) {
            $this->setId(null);
        }

        $validators = [];

        foreach ($results as $id => $row) {
            // add new validator to Mission Order
            $validator = new User();
            $validator
                ->setId($row['id_validator'])
                ->setFirstName($row['firstname_validator'])
                ->setLastName($row['lastname_validator'])
                ->setEmail($row['email_validator']);
            $validators[] = $validator;

            if ($id > 0) {
                // we have yet loaded Mission Order so we pass to next row
                continue;
            }

            $this
                ->setTitle($row['title'])
                ->setAdditionalOrderingInformation($row['additional_ordering_information'])
                ->setAdditionalBudgetInformation($row['additional_budget_information'])
                ->setSupplierContact($row['supplier_contact'])
                ->setDeliveryAddress($row['delivery_address'])
                ->setPurchaseCard($row['purchase_card'])
                ->setComment($row['comment'])
                ->setStatus($row['status']);

            $owner = new User();
            $owner
                ->setId($row['id_owner'])
                ->setFirstName($row['firstname_owner'])
                ->setLastName($row['lastname_owner'])
                ->setEmail($row['mail_owner'])
                ->setCustom1($row['custom1_owner']);

            $this->setOwner($owner);

            $this
                ->getService()
                ->setId($row['id_service'])
                ->setName($row['name_service']);

            $this
                ->getBudgetData()
                ->setId($row['budget_data'])
                ->setName($row['name_budget_data'])
                ->setCategory($row['category_budget_data']);
        }

        $this
            ->setValidators($validators)
            ->loadCodeNacre()
            ->loadFiles();

        return $this;
    }

    public function loadCodeNacre(): self
    {
        $codeNacreList = [];

        $query = "SELECT dpocn.`id_purchase_order`, dpocn.`id_code_nacre`, dcn.`code` , dcn.`wording`
            FROM `dpurchase_order_code_nacre` dpocn
            JOIN `dcode_nacre` dcn ON dcn.id = dpocn.id_code_nacre
            WHERE dpocn.id_purchase_order=:id_purchase_order
        ";
        $params = [
            'id_purchase_order' => $this->getId(),
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $codeNacre = new CodeNacre();
            $codeNacre
                ->setId($row['id_code_nacre'])
                ->setCode($row['code'])
                ->setWording($row['wording']);

            $codeNacreList[] = $codeNacre;
        }
        $this->setCodeNacre($codeNacreList);

        return $this;
    }

    public function save(): self
    {
        if ($this->getId() && !$this->getIsModel()) {
            $this->update();
        } else {
            $this->insert();
        }

        $this
            ->deleteValidators()
            ->updateValidators()
            ->deleteCodeNacre()
            ->updateCodeNacre();

        if ($this->getIsModel()) {
            $this
                ->updateFiles()
                ->setIsModel(false);
        }

        echo DisplayMessage('success', T_("Demande enregistrée"));

        return $this;
    }

    public function insert(): self
    {
        $query = "
            INSERT INTO `dpurchase_order` (
                `id_owner`, `title`, `id_service`, `additional_ordering_information`, `budget_data`, `additional_budget_information`,
                `supplier_contact`, `delivery_address`, `purchase_card`, `comment`, `status`
            )
            VALUES (
                :id_owner, :title, :id_service, :additional_ordering_information, :budget_data, :additional_budget_information,
                :supplier_contact, :delivery_address, :purchase_card, :comment, :status
            )
        ";
        $params = [
            'id_owner' => $this->getOwner()->getId(),
            'title' => $this->getTitle(),
            'id_service' => $this->getService()->getId(),
            'additional_ordering_information' => $this->getAdditionalOrderingInformation(),
            'budget_data' => $this->getBudgetData()->getId(),
            'additional_budget_information' => $this->getAdditionalBudgetInformation(),
            'supplier_contact' => $this->getSupplierContact(),
            'delivery_address' => $this->getDeliveryAddress(),
            'purchase_card' => $this->isPurchaseCard(),
            'comment' => $this->getComment(),
            'status' => $this->getStatus()
        ];

        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());

        return $this;
    }

    public function update(): self
    {
        $query = "
            UPDATE `dpurchase_order`
            SET
                `title`=:title,
                `id_service`=:id_service,
                `additional_ordering_information`=:additional_ordering_information,
                `budget_data`=:budget_data,
                `additional_budget_information`=:additional_budget_information,
                `supplier_contact`=:supplier_contact,
                `delivery_address`=:delivery_address,
                `purchase_card`=:purchase_card,
                `comment`=:comment,
                `status`=:status
            WHERE id=:id";

        $params = [
            'additional_ordering_information' => $this->getAdditionalOrderingInformation(),
            'title' => $this->getTitle(),
            'id_service' => $this->getService()->getId(),
            'budget_data' => $this->getBudgetData()->getId(),
            'additional_budget_information' => $this->getAdditionalBudgetInformation(),
            'supplier_contact' => $this->getSupplierContact(),
            'delivery_address' => $this->getDeliveryAddress(),
            'purchase_card' => $this->isPurchaseCard(),
            'comment' => $this->getComment(),
            'status' => $this->getStatus(),
            'id' => $this->getId(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateDateAlerte(): self
    {
        $query = "
            UPDATE `dpurchase_order`
            SET
                `date_alerte`=:date
            WHERE id=:id";
        $params = [
            'date' => $this->getDateAlerte(),
            'id' => $this->getId()
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function deleteValidators(): self
    {
        $query = "DELETE FROM `dpurchase_order_validators`
            WHERE id_purchase_order=:id_purchase_order"
        ;
        $params = [
            'id_purchase_order' => $this->getId(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateValidators(): self
    {
        foreach($this->getValidators() as $validator) {
            $query = "INSERT INTO `dpurchase_order_validators` (`id_purchase_order`,`id_validator`)
                VALUES (:id_purchase_order,:id_validator)
            ";
            $params = [
                'id_purchase_order' => $this->getId(),
                'id_validator' => $validator->getId(),
            ];

            $this->sql->query($query, $params, false);
        }

        return $this;
    }

    public function deleteCodeNacre(): self
    {
        $query = "DELETE FROM `dpurchase_order_code_nacre`
            WHERE id_purchase_order=:id_purchase_order";
        $params = [
            'id_purchase_order' => $this->getId(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateCodeNacre(): self
    {
        foreach($this->getCodeNacre() as $codeNacre) {
            $query = "INSERT INTO `dpurchase_order_code_nacre` (`id_purchase_order`,`id_code_nacre`)
                VALUES (:id_purchase_order,:id_code_nacre)
            ";
            $params = [
                'id_purchase_order' => $this->getId(),
                'id_code_nacre' => $codeNacre->getId(),
            ];

            $this->sql->query($query, $params, false);
        }
        return $this;
    }

    public function updateFiles(): self
    {
        $quote = $this->getQuote();
        if ($quote) {
            File::copy($quote, $this);
            $quote
                ->setIdPurchaseOrder($this->getId())
                ->add();
        }

        return $this;
    }
}
