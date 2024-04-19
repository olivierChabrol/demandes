<?php

namespace Models\Request\Ticket;

require_once('models/request/base_request.php');
require_once('models/request/purchase_order/purchase_order.php');
require_once('models/request/mission_order/mission_order.php');
require_once('models/user/user.php');
require_once 'components/dompdf/autoload.inc.php';

use Models\Request\Common\File;
use Models\Request\BaseRequest;
use Models\Request\MissionOrder\MissionOrder;
use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\User\User;

use Dompdf\Dompdf;


class Ticket extends BaseRequest
{
    const TECHNICIAN_NO_ATTRIBUTE = 0;
    const SUBCATEGORY_NO_ATTRIBUTE = 0;
    const CATEGORY_PURCHASE_ORDER = 1;
    const CATEGORY_MISSION_ORDER = 2;
    const CATEGORY_INVITATION = 4;
    const TEMPLATE_HTML_PURCHASE_ORDER = "template/request/ticket/purchase_order.php";
    const TEMPLATE_HTML_MISSION_ORDER = "template/request/ticket/mission_order.php";

    private $user;
    private $technician;
    private $description;
    private $category;
    private $subCategory;
    private $dateCreate;
    private $dateStart;
    private $creator;
    private $files;
    private $observers;

    public function __construct()
    {
        $this->description = '';
        $this->dateCreate = new \DateTime();
        $this->files = [];
        $this->observers = [];

        parent::__construct();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTechnician(): User
    {
        return $this->technician;
    }

    public function setTechnician(User $technician): self
    {
        $this->technician = $technician;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getSubCategory(): int
    {
        return $this->subCategory;
    }

    public function setSubCategory(int $subCategory): self
    {
        $this->subCategory = $subCategory;
        return $this;
    }

    public function getDateCreate(): \DateTime
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTime $dateCreate): self
    {
        $this->dateCreate = $dateCreate;
        return $this;
    }


    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function setDateStart($dateStart): self
    {
        $this->dateStart = $dateStart;
        return $this;
    }


    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }

    public function addFile(File $file): self
    {
        $this->files[] = $file;
        return $this;
    }

    public function getObservers(): array
    {
        return $this->observers;
    }

    public function setObservers(array $observers): self
    {
        $this->observers = $observers;
        return $this;
    }

    public function createByRequest($request)
    {
        // init an empty technician
        $technician = new User();
        $technician->setId(self::TECHNICIAN_NO_ATTRIBUTE);

        $this
            ->setUser($request->getOwner())
            ->setTitle($request->getTitle())
            ->setService($request->getService())
            ->setCreator($request->getOwner())
            ->setTechnician($technician)
            ->setObservers($request->getValidators());

        if ($request->isPurchaseOrder()) {
		$this->createByPurchaseOrder($request);
		$request->setIncidentId($this->getId());
        } else if ($request->isMissionOrder()) {
                $this->createByMissionOrder($request);
		$request->setIncidentId($this->getId());
	}

	

        $this
            ->deleteObservers()
            ->updateObservers();

        $request->setTicket($this);

        return $this;
    }

    private function createByPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        $html = $this->buildHtml($purchaseOrder);
        $this
            ->setDescription($html)
            ->setCategory(self::CATEGORY_PURCHASE_ORDER)
            ->setSubCategory($purchaseOrder->getBudgetData()->getId())
            ->setDateStart(new \DateTime());

        $this->insert();

        $purchaseOrder->setIncidentId($this->getId());
        $purchaseOrder->save();

        if ($purchaseOrder->getQuote()) {
            // copy and save quote
            foreach($purchaseOrder->getQuote() as $file)
            {
              $quote = clone($file);
              File::copy($quote, $this);
              $this->addFile($quote);

            }
            $this->insertFiles();

        }
    }

    private function createByMissionOrder(MissionOrder $missionOrder)
    {
        // init an empty subcategory in case of mission whitout fees
        $subCategory = self::SUBCATEGORY_NO_ATTRIBUTE;

        if ($missionOrder->getTypeMission() == MissionOrder::TYPE_MISSION_WITH_FEES) {
            // mission with fees, we get budget data
            $subCategory = $missionOrder->getBudgetData()->getId();
        }

        $html = $this->buildHtml($missionOrder);

        if($missionOrder->isOmForGuest())
            $this->setCategory(self::CATEGORY_INVITATION);
        else
            $this->setCategory(self::CATEGORY_MISSION_ORDER);
        $this
            ->setSubCategory($subCategory)
            ->setDescription($html)
            ->setDateStart($missionOrder->getDateStart())
	    ->insert();
	$missionOrder->setIncidentId($this->getId());
	$missionOrder->save();


        $pdf = $this->buildPdfMissionOrder($missionOrder, $html);

        $file = new File();
        $file
            ->setName($pdf)
            ->setPath($pdf);
        $this->addFile($file);

        // build list of files to copy and save
        $files = $missionOrder->getRibAndSupplementarySheet();
        $program = $missionOrder->getColloquiums()->getProgram();

        if ($program) {
            $files[] = $program;
        }

        foreach ($files as $file) {
            // copy and add to ticket each files in list
            $fileToCopy = clone($file);
            File::copy($fileToCopy, $this);
            $this->addFile($fileToCopy);
        }

        $this->insertFiles();
    }

    private function buildHtml(BaseRequest $request): string
    {
        ob_start();

        $template = "";

        // get template in function of type of request
        if ($request->isPurchaseOrder()) {
            $template = self::TEMPLATE_HTML_PURCHASE_ORDER;
        } else if ($request->isMissionOrder()) {
            $template = self::TEMPLATE_HTML_MISSION_ORDER;
        }

        include($template);

        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }

    private function buildPdfMissionOrder(MissionOrder $missionOrder, string $html): string
    {
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $pdfString = $dompdf->output();
        $date = new \DateTime();
        $filename = 'recap_'.$missionOrder->getOwner()->getFullName('_').'_'.$missionOrder->getId().'_'.$date->format('YmdHi').'.pdf';
        $path = File::TARGET_FILE_TICKET.'/'.$filename;

        file_put_contents($path, $pdfString);

        return $filename;
    }

    public function loadObservers(): self
    {
        $query = "
            SELECT id_observer
            FROM dticket_observers
            WHERE id=:id
        ";
        $params = [
            'id' => $this->getId()
        ];

        $results = $this->sql->query($query, $params);

        $observers = [];
        foreach ($results as $result) {
            $observer = new User();
            $observer->setId($result['id_observer']);
            $observers[] = $observer;
        }
        $this->setObservers($observers);

        return $this;
    }

    public function deleteObservers(): self
    {
        $query = "
            DELETE FROM dticket_observers
            WHERE id=:id
        ";
        $params = [
            "id" => $this->getId()
        ];
        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateObservers(): self
    {
        foreach ($this->getObservers() as $observer) {
            $query = "
                INSERT INTO `dticket_observers` (`id`, `id_observer`)
                VALUES (:id,:id_observer)
            ";
            $params = [
                "id" => $this->getId(),
                "id_observer" => $observer->getId()
            ];

            $this->sql->query($query, $params, false);
        }

        return $this;
    }

    public function hasObserver(int $idObserverToCheck): bool
    {
        foreach ($this->getObservers() as $observer) {
            if ($observer->getId() == $idObserverToCheck) {
                return true;
            }
        }

        return false;
    }

    public function getObserversMail()
    {
        $obsmails=[];
      foreach ($this->getObservers() as $observer) {
        $obsmails[]=$observer
            ->load()
            ->getEmail();
      }

      return $obsmails;
    }

    public function insert(): self
    {
        $query = "
            INSERT INTO `tincidents` (
                `user`,`technician`,`title`,`description`,`state`,`u_service`,`category`,
                `subcat`,`date_create`,`creator`,`criticality`,`techread`,`date_start`
            )
            VALUES (
                :user, :technician, :title, :description, '5', :service, :category,
                :subcat, :date_create,:creator, '0', '0', :date_start
            )";
        $datestart = $this->getDateStart() ? $this->getDateStart()->format('Y-m-d') : null;
        $params = [
            'user' => $this->getUser()->getId(),
            'technician' => $this->getTechnician()->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'service' => $this->getService()->getId(),
            'category' => $this->getCategory(),
            'subcat' => $this->getSubCategory(),
            'date_create' => $this->getDateCreate()->format('Y-m-d H-i-s'),
            'creator' => $this->getCreator()->getId(),
            'date_start' => $datestart
        ];

        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());

        return $this;
    }

    public function insertFiles()
    {
        foreach ($this->getFiles() as $file) {
            $query = "INSERT INTO `tattachments` (
                    `uid`,`ticket_id`,`storage_filename`,`real_filename`
                )
                VALUES (
                    :uid,:ticket_id,:storage_filename,:real_filename
                )
            ";
            $params = [
                'uid' => md5(uniqid()),
                'ticket_id' => $this->getId(),
                'storage_filename' => $file->getPath(),
                'real_filename' => $file->getName(),
            ];

            $this->sql->query($query, $params, false);
        }

        return $this;
    }
}
