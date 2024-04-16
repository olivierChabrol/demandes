<?php

namespace Models\Request\MissionOrder;

require_once('models/request/base_request.php');
require_once('models/request/mission_order/private_stay.php');
require_once('models/request/mission_order/administrative_vehicle.php');
require_once('models/request/mission_order/personal_vehicle.php');
require_once('models/request/mission_order/colloquiums.php');
require_once('models/request/mission_order/transport_choice.php');
require_once('models/request/common/file.php');
require_once('models/request/common/model.php');
require_once('models/request/ticket/ticket.php');
require_once('models/user/user.php');

use Models\Request\Common\File;
use Models\Request\BaseRequest;
use Models\Request\Ticket\Ticket;
use Models\User\User;

/**
 * Class MissionOrder
 *
 * This class represents a mission order in the system. It extends the BaseRequest class and
 * contains properties and methods specific to a mission order.
 *
 * @package Models\Request\MissionOrder
 */
class MissionOrder extends BaseRequest
{
    /** @var int Represents a mission order with fees. */
    const TYPE_MISSION_WITH_FEES = 1;

    /** @var int Represents a mission order without fees. */
    const TYPE_MISSION_WITHOUT_FEES = 2;

    /** @var int Represents a standing mission order. */
    const TYPE_MISSION_STANDING_MISSION_ORDER = 3;

    /** @var int Represents the choice of personal vehicle for transport. */
    const TRANSPORT_CHOICE_PERSONAL_VEHICLE = 3;

    /** @var int Represents the choice of administrative vehicle for transport. */
    const TRANSPORT_CHOICE_ADMINISTRATIVE_VEHICLE = 5;

    /** @var bool $firstMissionRequest Indicates if this is the first mission request.*/
    private $firstMissionRequest;

    /** @var bool $omForGuest Indicates if the mission order is for a guest.*/
    private $omForGuest;

    /** @var string $guestName The name of the guest.*/
    private $guestName;

    /** @var string $guestMail The mail of the guest.*/
    private $guestMail;

    /** @var string $guestPhoneNumber The phone number of the guest.*/
    private $guestPhoneNumber;

    /** @var \DateTime $guestBirthDate The birthdate of the guest.*/
    private $guestBirthDate;

    /** @var string $guestLabo The labo of the guest.*/
    private $guestLabo;

    /** @var string $guestCountry The country of the guest.*/
    private $guestCountry;

    /** @var bool $collectiveMission Indicates if the mission is collective.*/
    private $collectiveMission;

    private $listPeopleInvolvedAssignment;

    /** @var array $ribAndSupplementarySheet The rib and supplementary sheet files.*/
    private $ribAndSupplementarySheet;

    /** @var int $typeMission The type of the mission.*/
    private $typeMission;

    /** @var string $careOrganization The care organization.*/
    private $careOrganization;

    private $guardianShip;

    /** @var string $reasonForMission The reason for the mission.*/
    private $reasonForMission;
    private $standingMissionOrder;

    /** @var \DateTime $dateStart The date and time of the start of the mission.*/
    private $dateStart;

    /** @var string $placeStart The place of the start of the mission.*/
    private $placeStart;

    /** @var \DateTime $dateReturn The date and time of the return of the mission.*/
    private $dateReturn;

    /** @var bool $placeReturnDifferent Indicates if the place of return is different.*/
    private $placeReturnDifferent;

    /** @var string $placeReturn The place of the return of the mission.*/
    private $placeReturn;
    private $cityStay;
    private $countryStay;
    private $privateStay;
    private $offMarketAccomodation;
    private $advanceRequest;
    private $transportMarket;
    private $transportMarketJustification;
    private $transportChoices;
    private $administrativeVehicle;
    private $personalVehicle;
    private $otherFees;
    private $colloquiums;
    private $ticket;
    private $uploadDir;
    private $fileType;
    private $amountMax;
    private $estimatedAmount;
    private $realAmount;
    private $incidentId;

    public function __construct()
    {
        $this->guardianShip = '';
        $this->privateStay = new PrivateStay();
        $this->administrativeVehicle = new AdministrativeVehicle();
        $this->personalVehicle = new PersonalVehicle();
        $this->colloquiums = new Colloquiums();
        $this->dateStart = new \DateTime();
        $this->dateReturn = new \DateTime();
        $this->ribAndSupplementarySheet = [];
        $this->reasonForMission = '';
        $this->cityStay = '';
        $this->countryStay = '';
        $this->transportMarket = true;
        $this->transportChoices = [];
        $this->ticket = new Ticket();
	    $this->fileType = "rib-and-supplementary-sheet";
	    $this->amountMax = 0.0;
	    $this->estimatedAmount = 0.0;
	    $this->realAmount = 0.0;
	    $this->missionType = 0;
        parent::__construct();
    }

    public function getType()
    {
      return $this->fileType;
    }

    public function setUploadDir()
    {
      $this->uploadDir=time();
    }

    public function getUploadDir(): ?string
    {
      return $this->uploadDir;
    }

    public function isFirstMissionRequest(): ?bool
    {
        return $this->firstMissionRequest;
    }

    public function setFirstMissionRequest(?bool $firstMissionRequest): self
    {
        $this->firstMissionRequest = $firstMissionRequest;
        return $this;
    }

    public function isOmForGuest(): ?bool
    {
        return $this->omForGuest;
    }

    public function setOmForGuest(?bool $omForGuest): self
    {
        $this->omForGuest = $omForGuest;
        return $this;
    }

    public function getGuestName(): ?string//TEST
    {
        return $this->guestName;
    }

    public function setGuestName(?string $guestName): self//TEST
    {
        $this->guestName = $guestName;
        return $this;
    }

    public function getGuestBirthDate(): ?\DateTime
    {
        return $this->guestBirthDate;
    }

    public function setGuestBirthDate(string $guestBirthDate, string $format = 'Y-m-d'): self
    {
        if($guestBirthDate != null) $this->guestBirthDate = \DateTime::createFromFormat($format,$guestBirthDate);
        else $this->guestBirthDate = null;
        return $this;
    }


    public function getGuestMail(): ?string//TEST
    {
        return $this->guestMail;
    }

    public function setGuestMail(?string $guestMail): self//TEST
    {
        $this->guestMail = $guestMail;
        return $this;
    }

    public function getGuestPhoneNumber(): ?string
    {
        return $this->guestPhoneNumber;
    }

    public function setGuestPhoneNumber(string $guestPhoneNumber): self
    {
        $this->guestPhoneNumber = $guestPhoneNumber;
        return $this;
    }


    public function getGuestLabo(): ?string//TEST
    {
        return $this->guestLabo;
    }

    public function setGuestLabo(?string $guestLabo): self//TEST
    {
        $this->guestLabo = $guestLabo;
        return $this;
    }

    public function getGuestCountry(): ?string//TEST
    {
        return $this->guestCountry;
    }

    public function setGuestCountry(?string $guestCountry): self//TEST
    {
        $this->guestCountry = $guestCountry;
        return $this;
    }

    public function isCollectiveMission(): ?bool
    {
        return $this->collectiveMission;
    }

    public function setCollectiveMission(?bool $collectiveMission): self
    {
        $this->collectiveMission = $collectiveMission;
        return $this;
    }

    public function getListPeopleInvolvedAssignment(): ?string
    {
        return $this->listPeopleInvolvedAssignment;
    }

    public function setListPeopleInvolvedAssignment(?string $listPeopleInvolvedAssignment): self
    {
        $this->listPeopleInvolvedAssignment = $listPeopleInvolvedAssignment;
        return $this;
    }

    public function getRibAndSupplementarySheet(): array
    {
        return $this->ribAndSupplementarySheet;
    }

    public function getRibAndSupplementarySheetById(?int $idRibAndSupplementarySheetToGet): ?File
    {
        foreach ($this->ribAndSupplementarySheet as $fileRibAndSupplementarySheet) {
            if ($fileRibAndSupplementarySheet->getId() == $idRibAndSupplementarySheetToGet) {
                return $fileRibAndSupplementarySheet;
            }
        }

        return null;
    }

    public function setRibAndSupplementarySheet(array $ribAndSupplementarySheet): self
    {
        $this->ribAndSupplementarySheet = $ribAndSupplementarySheet;
        return $this;
    }

    public function addRibAndSupplementarySheet(File $fileToAdd): self
    {
        $this->ribAndSupplementarySheet[] = $fileToAdd;
        return $this;
    }

    public function removeRibAndSupplementarySheet(?File $fileToRemove): self
    {
        $ribAndSUpplementarySheet = [];

        foreach ($this->ribAndSupplementarySheet as $file) {
            if ($file->getId() == $fileToRemove->getId()) {
                $file->delete();
                continue;
            }

            $ribAndSUpplementarySheet[] = $file;
        }
        $this->setRibAndSupplementarySheet($ribAndSUpplementarySheet);

        return $this;
    }

    public function getTypeMission(): ?int
    {
        return $this->typeMission;
    }

    public function setTypeMission(int $typeMission): self
    {
        $this->typeMission = $typeMission;
        return $this;
    }

    public function getCareOrganization(): ?string
    {
        return $this->careOrganization;
    }

    public function setCareOrganization(?string $careOrganization): self
    {
        $this->careOrganization = $careOrganization;
        return $this;
    }

    public function getGuardianShip(): string
    {
        return $this->guardianShip;
    }

    public function setGuardianShip(string $guardianShip): self
    {
        $this->guardianShip = $guardianShip;
        return $this;
    }

    public function getReasonForMission(): string
    {
        return $this->reasonForMission;
    }

    public function setReasonForMission(string $reasonForMission): self
    {
        $this->reasonForMission = $reasonForMission;
        return $this;
    }

    public function isStandingMissionOrder(): ?bool
    {
        return $this->standingMissionOrder;
    }

    public function setStandingMissionOrder(?bool $standingMissionOrder): self
    {
        $this->standingMissionOrder = $standingMissionOrder;
        return $this;
    }

    public function getDateStart(): \DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(string $dateStart, string $format = 'Y-m-d H:i:s'): self
    {
        $this->dateStart = \DateTime::createFromFormat($format, $dateStart);
        return $this;
    }

    public function getEstimatedAmount() : float
    {
        return $this->estimatedAmount;
    }

    public function setEstimatedAmount(float $amount): self
    {
            $this->estimatedAmount = $amount;
            return $this;
    }

    public function getRealAmount() : float
    {
        return $this->realAmount;
    }

    public function setRealAmount(float $amount): self
    {
            $this->realAmount = $amount;
            return $this;
    }

    public function getAmountMax() : float
    {
	    return $this->amountMax;
    }

    public function setAmountMax( float $amount): self
    {
	    $this->amountMax = $amount;
	    return $this;
    }

    public function setInvitationToken( string $invitationToken): self
    {
	    $this->invitation_token=$invitationToken;
	    return $this;
    }

    public function getInvitationToken(): string
    {
	    return $this->invitation_token;
    }

    public function setMissionType( int $missionType): self
    {
	   $this->missionType = $missionType;
           return $this;
    }

    public function getMissionType(): int
    {
	return $this->missionType;
    }

    public function getIncidentId(): ?int
    {
        return $this->incidentId;
    }

    public function setIncidentId(?int $id): self
    {
        $this->incidentId = $id;
        return $this;
    }

    public function getPlaceStart(): ?string
    {
        return $this->placeStart;
    }

    public function setPlaceStart(?string $placeStart): self
    {
        $this->placeStart = $placeStart;
        return $this;
    }

    public function getDateReturn(): \DateTime
    {
        return $this->dateReturn;
    }

    public function setDateReturn(string $dateReturn, string $format = 'Y-m-d H:i:s'): self
    {
        $this->dateReturn = \DateTime::createFromFormat($format, $dateReturn);
        return $this;
    }

    public function isPlaceReturnDifferent(): ?bool
    {
        return $this->placeReturnDifferent;
    }

    public function setPlaceReturnDifferent(?bool $placeReturnDifferent): self
    {
        $this->placeReturnDifferent = $placeReturnDifferent;
        return $this;
    }

    public function getPlaceReturn(): ?string
    {
        return $this->placeReturn;
    }

    public function setPlaceReturn(?string $placeReturn)
    {
        $this->placeReturn = $placeReturn;
        return $this;
    }

    public function getCityStay(): string
    {
        return $this->cityStay;
    }

    public function setCityStay(string $cityStay)
    {
        $this->cityStay = $cityStay;
        return $this;
    }

    public function getCountryStay(): string
    {
        return $this->countryStay;
    }

    public function setCountryStay(string $countryStay): self
    {
        $this->countryStay = $countryStay;
        return $this;
    }

    public function getPrivateStay(): PrivateStay
    {
        return $this->privateStay;
    }

    public function setPrivateStay(PrivateStay $privateStay): self
    {
        $this->privateStay = $privateStay;
        return $this;
    }

    public function isOffMarketAccomodation(): ?bool
    {
        return $this->offMarketAccomodation;
    }

    public function setOffMarketAccomodation(?bool $offMarketAccomodation): self
    {
        $this->offMarketAccomodation = $offMarketAccomodation;
        return $this;
    }

    public function isadvanceRequest(): ?bool
    {
        return $this->advanceRequest;
    }

    public function setAdvanceRequest(?bool $advanceRequest): self
    {
        $this->advanceRequest = $advanceRequest;
        return $this;
    }

    public function isTransportMarket(): ?bool
    {
        return $this->transportMarket;
    }

    public function setTransportMarket(?bool $transportMarket): self
    {
        $this->transportMarket = $transportMarket;
        return $this;
    }

    public function getTransportMarketJustification(): ?string
    {
        return $this->transportMarketJustification;
    }

    public function setTransportMarketJustification(?string $transportMarketJustification): self
    {
        $this->transportMarketJustification = $transportMarketJustification;
        return $this;
    }

    public function getTransportChoices(): array
    {
        return $this->transportChoices;
    }

    public function setTransportChoices(array $transportChoices): self
    {
        $this->transportChoices = $transportChoices;
        return $this;
    }

    public function hasTransportChoices(int $idTransportChoiceCheck): bool
    {
        foreach ($this->getTransportChoices() as $transportChoice) {
            if ($transportChoice->getId() == $idTransportChoiceCheck) {
                return true;
            }
        }

        return false;
    }

    public function getTransportChoicesHasString(string $separator = ';'): string
    {
        $transportChoicesString = '';

        foreach ($this->transportChoices as $transportChoice) {
            if (strlen($transportChoicesString) > 0) {
                $transportChoicesString .= ' '.$separator.' ';
            }
            $transportChoicesString .= $transportChoice->getName();
        }

        return $transportChoicesString;
    }

    public function getAdministrativeVehicle(): AdministrativeVehicle
    {
        return $this->administrativeVehicle;
    }

    public function setAdministrativeVehicle(AdministrativeVehicle $administrativeVehicle): self
    {
        $this->administrativeVehicle = $administrativeVehicle;
        return $this;
    }

    public function getPersonalVehicle(): PersonalVehicle
    {
        return $this->personalVehicle;
    }

    public function setPersonalVehicle(PersonalVehicle $personalVehicle): self
    {
        $this->personalVehicle = $personalVehicle;
        return $this;
    }

    public function getOtherFees(): ?string
    {
        return $this->otherFees;
    }

    public function setOtherFees(?string $otherFees): self
    {
        $this->otherFees = $otherFees;
        return $this;
    }

    public function getColloquiums(): Colloquiums
    {
        return $this->colloquiums;
    }

    public function setColloquiums(Colloquiums $colloquiums): self
    {
        $this->colloquiums = $colloquiums;
        return $this;
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
	$invitation = (isset($datas['om-for-guest'])) ? True : False;
        if (!parent::checkForm($datas, $files)) {
            return false;
        }
        if (!$this->checkCollectiveMission($datas)) {
            return false;
        }
        if (!$this->checkTypeMission($datas)) {
            return false;
        }
        if (!($datas['reason-for-mission'])) {
            echo DisplayMessage('error',T_("Le champ Motif de la mission est requis"));
            return false;
        }
        if (!$datas['date-start']) {
            echo DisplayMessage('error',T_("Le champ Date et heure de départ est requis"));
            return false;
	}
	if (!$invitation) {
          if (!$datas['place-start-input']) {
              echo DisplayMessage('error',T_("Le champ Lieu de départ est requis"));
              return false;
          }
          if (!$this->checkPlaceReturn($datas)) {
              return false;
          }
          if (!$datas['city-stay']) {
              echo DisplayMessage('error',T_("Le champ Ville de séjour est requis"));
              return false;
          }
          if (!$datas['country-stay']) {
              echo DisplayMessage('error',T_("Le champ Pays de séjour est requis"));
              return false;
          }
          if (!$this->getPrivateStay()->checkForm($datas)) {
              return false;
          }
          if (!$this->checkTransportMarket($datas)) {
              return false;
          }
          if (!$this->checkTransportChoice($datas)) {
              return false;
          }
          if (!$this->getColloquiums()->checkForm($datas, $files)) {
              return false;
	  }
	}

        return true;
    }

    private function checkCollectiveMission(array &$datas): bool
    {
        if (isset($datas['collective-mission']) && !$datas['list-people-involved-assignment']) {
            echo DisplayMessage('error', T_("Le champ Liste des personnes concernées pour la mission est requis"));
            return false;
        } else if (!isset($datas['collective-mission']) || !$datas['collective-mission']) {
            $datas['list-people-involved-assignment'] = null;
        }

        return true;
    }

    private function checkTypeMission(array &$datas): bool
    {
        if (!isset($datas['type-mission'])) {
            echo DisplayMessage('error',T_("Le champ Type de mission est requis"));
            return false;
        }
        // check mission with fees
        if ($datas['type-mission'] == self::TYPE_MISSION_WITH_FEES && !$datas['budget-data']) {
            echo DisplayMessage('error', T_("Le champ Données budgétaires est requis"));
            return false;
        } else if ($datas['type-mission'] != self::TYPE_MISSION_WITH_FEES) {
            $datas['budget-data'] = null;
        }
        // check mission without fees
        if ($datas['type-mission'] == self::TYPE_MISSION_WITHOUT_FEES && !$datas['care-organization']) {
            echo DisplayMessage('error', T_("Le champ Organisme de prise en charge est requis"));
            return false;
        } else if ($datas['type-mission'] != self::TYPE_MISSION_WITHOUT_FEES) {
            $datas['care-organization'] = null;
        }

        return true;
    }

    private function checkPlaceReturn(array &$datas): bool
    {
        if (!$datas['date-return']) {
            echo DisplayMessage('error',T_("Le champ Date et heure de retour est requis"));
            return false;
        }
        if (isset($datas['place-return-different'])) {
            if (!$datas['place-return-input']) {
                echo DisplayMessage('error', T_("Le champ Lieu de retour est requis"));
                return false;
            }
        } else {
            $datas['place-return-input'] = null;
        }

        return true;
    }

    private function checkTransportMarket(array &$datas): bool
    {
        if (!isset($datas['transport-market'])) {
            if (!isset($datas['transport-market-justification'])) {
                echo DisplayMessage('error', T_("Le champ Justification est requis"));
                return false;
            }
        } else {
            $datas['transport-market-justification'] = null;
        }

        return true;
    }

    private function checkTransportChoice(array &$datas): bool
    {
        if (!$datas['transport-choice']) {
            echo DisplayMessage('error',T_("Le champ Choix du transport est requis"));
            return false;
        }

        if (!$this->getAdministrativeVehicle()->checkForm($datas)) {
            return false;
        }

        if (!$this->getPersonalVehicle()->checkForm($datas)) {
            return false;
        }

        return true;
    }

    public function getDatas(array $datas): self
    {
        parent::getDatas($datas);

        $firstMissionRequest = (isset($datas['first-mission-request'])) ? $datas['first-mission-request'] : null;
        $omForGuest = (isset($datas['om-for-guest'])) ? $datas['om-for-guest'] : null;
        $collectiveMission = (isset($datas['collective-mission'])) ? $datas['collective-mission'] : null;
        $placeReturnDifferent = (isset($datas['place-return-different'])) ? $datas['place-return-different'] : null;
        $offMarketAccommodation = (isset($datas['off-market-accommodation'])) ? $datas['off-market-accommodation'] : null;
        $advanceRequest = (isset($datas['advance-request'])) ? $datas['advance-request'] : null;
        $transportMarket = (isset($datas['transport-market'])) ? $datas['transport-market'] : null;

        $this
            ->setFirstMissionRequest($firstMissionRequest)
            ->setOmForGuest($omForGuest)
            ->setGuestName($datas['guest-name'])
            ->setGuestMail($datas['guest-mail'])
            ->setGuestBirthDate($datas['guest-birthdate'])
            ->setGuestPhoneNumber($datas['guest-phonenumber'])
            ->setGuestLabo($datas['guest-labo'])
            ->setGuestCountry($datas['guest-country'])
            ->setCollectiveMission($collectiveMission)
            ->setListPeopleInvolvedAssignment($datas['list-people-involved-assignment'])
            ->setTypeMission($datas['type-mission'])
            ->setCareOrganization($datas['care-organization'])
            ->setReasonForMission($datas['reason-for-mission'])
            ->setDateStart($datas['date-start'], 'd/m/Y H:i:s')
            ->setDateReturn($datas['date-return'], 'd/m/Y H:i:s')
            ->setMissionType($datas['mission_type'])
            ->setAmountMax($datas['amount_max'])
            ->setEstimatedAmount($datas['amount_estimated'])
            ->setRealAmount($datas['amount_real']);
	if ($omForGuest == null) {
		$this->setPlaceStart($datas['place-start-input'])
                ->setPlaceReturnDifferent($placeReturnDifferent)
                ->setPlaceReturn($datas['place-return-input'])
		->setCityStay($datas['city-stay'])
                ->setCountryStay($datas['country-stay'])
                ->setOffMarketAccomodation($offMarketAccommodation)
                ->setAdvanceRequest($advanceRequest)
                ->setTransportMarket($transportMarket)
                ->setTransportMarketJustification($datas['transport-market-justification'])
		->setOtherFees($datas['other-fees']);

        $this
            ->getPrivateStay()
            ->getDatas($datas);

        $this
            ->getAdministrativeVehicle()
            ->getDatas($datas);

        $this
            ->getPersonalVehicle()
            ->getDatas($datas);

        $this
            ->getColloquiums()
            ->getDatas($datas);

        $transportChoices = [];

        foreach ($datas['transport-choice'] as $idTransportChoice) {
            $transportChoice = new TransportChoice();
            $transportChoice->setId($idTransportChoice);
            $transportChoices[] = $transportChoice;
        }
        $this->setTransportChoices($transportChoices);
	}
	else {
		$this->setPlaceStart("");
		$this->setCityStay("Marseille");
		$this->setCountryStay("France");
	}

        $this->getBudgetData()->getDatas($datas);


        return $this;
    }

    public function uploaded(File $file): self
    {
        $file->setIdMissionOrder($this->getId());
        switch ($file->getType()) {
            case File::TYPE_RIB_AND_SUPPLEMENTARY_SHEET:
                $file->add();
                $this->addRibAndSupplementarySheet($file);
                break;
            case File::TYPE_COLLOQUIUMS_PROGRAM:
                $currentFile = $this->getColloquiums()->getProgram();
                if ($currentFile) {
                    $currentFile->delete();
                }
                $file
                    ->add();
                $this
                    ->getColloquiums()
                    ->setProgram($file);
                break;
        }

        return $this;
    }

    public function load(): self
    {
        /*$query = "
            SELECT dm.`id_owner`, dm.`title`, dm.`id_service`, dm.`first_mission_request`, dm.`om_for_guest`, dm.`collective_mission`, dm.`list_people_involved_assignment`, dm.`type_mission`,
                dm.`care_organization`, dm.`guardianship`, dm.`budget_data`, dm.additional_budget_information, dm.`reason_for_mission`,
                dm.`date_start`, dm.`place_start`, dm.`date_return`, dm.`place_return_different`, dm.`place_return`, dm.`city_stay`, dm.`country_stay`, dm.`private_stay`,
                dm.`private_stay_date_begin`, dm.`private_stay_date_end`, dm.`private_stay_place`, dm.`off_market_accomodation`, dm.`advance_request`, dm.`transport_market`,
                dm.`transport_market_justification`, dm.`id_administrative_vehicle`, dm.`personal_vehicle_numberplate`, dm.`personal_vehicle_horsepower`, dm.`personal_vehicle_trip_mileage`, dm.`other_fees`, dm.`colloquiums`,
                dm.`colloquiums_registration_fees`, dm.`colloquiums_purchasing_card`, dm.`comment`, dm.`status`,
                ts.`name` as name_service, tscat.`name` as name_budget_data, tscat.`cat` as category_budget_data,
                dav.`name` as name_administrative_vehicle, dav.`numberplate` as numberplate_administrative_vehicle,
                dm.`id_owner`, tu1.`firstname` as firstname_owner, tu1.`lastname` as lastname_owner, tu1.`mail` as mail_owner, tu1.`custom1` as custom1_owner,
                dmv.`id_validator`, tu2.`firstname` as firstname_validator, tu2.`lastname` as lastname_validator, tu2.`mail` as email_validator
            FROM `dmission_order` dm
            LEFT JOIN `dmission_order_validators` dmv ON dm.id = dmv.id_mission_order
            LEFT JOIN `tservices` ts ON ts.id = dm.id_service
            LEFT JOIN `tsubcat` tscat ON tscat.id = dm.budget_data
            LEFT JOIN `dadministrative_vehicle` dav ON dav.id = dm.id_administrative_vehicle
            LEFT JOIN `tusers` tu1 ON tu1.id = dm.id_owner
            LEFT JOIN `tusers` tu2 ON tu2.id = dmv.id_validator

            WHERE dm.id=:id";*/
            /*TEST*/$query = "
            SELECT dm.`id_owner`, dm.`title`, dm.`id_service`, dm.`first_mission_request`, dm.`om_for_guest`, dm.`guest_name`, dm.`guest_mail`, dm.`guest_birthdate`, dm.`guest_phone_number`, dm.`guest_labo`, dm.`guest_country`, dm.`collective_mission`, dm.`list_people_involved_assignment`, dm.`type_mission`, dm.`amount_max`, dm.`incident_id`, dm.`amount_real`, dm.`amount_estimated`, dm.`mission_type`,
                dm.`care_organization`, dm.`guardianship`, dm.`budget_data`, dm.additional_budget_information, dm.`reason_for_mission`,
                dm.`date_start`, dm.`place_start`, dm.`date_return`, dm.`place_return_different`, dm.`place_return`, dm.`city_stay`, dm.`country_stay`, dm.`private_stay`,
                dm.`private_stay_date_begin`, dm.`private_stay_date_end`, dm.`private_stay_place`, dm.`off_market_accomodation`, dm.`advance_request`, dm.`transport_market`,
                dm.`transport_market_justification`, dm.`id_administrative_vehicle`, dm.`personal_vehicle_numberplate`, dm.`personal_vehicle_horsepower`, dm.`personal_vehicle_trip_mileage`, dm.`other_fees`, dm.`colloquiums`,
                dm.`colloquiums_registration_fees`, dm.`colloquiums_purchasing_card`, dm.`comment`, dm.`status`,
                ts.`name` as name_service, tscat.`name` as name_budget_data, tscat.`cat` as category_budget_data,
                dav.`name` as name_administrative_vehicle, dav.`numberplate` as numberplate_administrative_vehicle,
                dm.`id_owner`, tu1.`firstname` as firstname_owner, tu1.`lastname` as lastname_owner, tu1.`mail` as mail_owner, tu1.`custom1` as custom1_owner,
                dmv.`id_validator`, tu2.`firstname` as firstname_validator, tu2.`lastname` as lastname_validator, tu2.`mail` as email_validator, dm.`invitation_token` as invitation_token
            FROM `dmission_order` dm
            LEFT JOIN `dmission_order_validators` dmv ON dm.id = dmv.id_mission_order
            LEFT JOIN `tservices` ts ON ts.id = dm.id_service
            LEFT JOIN `tsubcat` tscat ON tscat.id = dm.budget_data
            LEFT JOIN `dadministrative_vehicle` dav ON dav.id = dm.id_administrative_vehicle
            LEFT JOIN `tusers` tu1 ON tu1.id = dm.id_owner
            LEFT JOIN `tusers` tu2 ON tu2.id = dmv.id_validator

            WHERE dm.id=:id";

        if($_SESSION['profile_id']!=4)//si l'utilisateur n'est pas admin alors on check si il est owner ou validator pour afficher la demande
        {
              $query.="
              AND (
                  dm.id_owner=:id_owner OR
                  dmv.id_validator=:id_validator
              )";
              if ($this->getCurrentUser() == null) {
	      }
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

            // load Mission Order
            $this
                ->setTitle($row['title'])
                ->setFirstMissionRequest($row['first_mission_request'])
                ->setOmForGuest($row['om_for_guest'])
                ->setGuestName($row['guest_name'])//TEST
                ->setGuestMail($row['guest_mail'])//TEST
                ->setGuestBirthDate($row['guest_birthdate'])
                ->setGuestPhoneNumber($row['guest_phone_number'])
                ->setGuestLabo($row['guest_labo'])//TEST
                ->setGuestCountry($row['guest_country'])//TEST
                ->setInvitationToken($row['invitation_token'])
                ->setCollectiveMission($row['collective_mission'])
                ->setListPeopleInvolvedAssignment($row['list_people_involved_assignment'])
                ->setTypeMission($row['type_mission'])
                ->setAdditionalBudgetInformation($row['additional_budget_information'])
                ->setCareOrganization($row['care_organization'])
                ->setReasonForMission($row['reason_for_mission'])
                ->setDateStart($row['date_start'])
                ->setPlaceStart($row['place_start'])
                ->setDateReturn($row['date_return'])
                ->setPlaceReturnDifferent($row['place_return_different'])
                ->setPlaceReturn($row['place_return'])
                ->setCityStay($row['city_stay'])
                ->setCountryStay($row['country_stay'])
                ->setOffMarketAccomodation($row['off_market_accomodation'])
                ->setAdvanceRequest($row['advance_request'])
                ->setTransportMarket($row['transport_market'])
                ->setTransportMarketJustification($row['transport_market_justification'])
                ->setOtherFees($row['other_fees'])
                ->setGuardianShip($row['guardianship'])
		->setComment($row['comment'])
		->setAmountMax($row['amount_max'])
	        ->setEstimatedAmount($row['amount_estimated'])
		->setRealAmount($row['amount_real'])
	        ->setMissionType($row['mission_type'])
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

            if ($this->getTypeMission() == self::TYPE_MISSION_WITH_FEES) {
                $this
                    ->getBudgetData()
                    ->setId($row['budget_data'])
                    ->setName($row['name_budget_data'])
                    ->setCategory($row['category_budget_data']);
            }

            $this
                ->getPrivateStay()
                ->setIsPrivateStay($row['private_stay'])
                ->setDateBegin($row['private_stay_date_begin'])
                ->setDateEnd($row['private_stay_date_end'])
                ->setPlace($row['private_stay_place']);

            $this
                ->getAdministrativeVehicle()
                ->setId($row['id_administrative_vehicle'])
                ->setName($row['name_administrative_vehicle'])
                ->setNumberplate($row['numberplate_administrative_vehicle']);

            $this
                ->getPersonalVehicle()
                ->setNumberplate($row['personal_vehicle_numberplate'])
                ->setHorsepower($row['personal_vehicle_horsepower'])
                ->setTripMileage($row['personal_vehicle_trip_mileage'])
                ->loadPassengers($this);

            $this
                ->getColloquiums()
                ->setIsColloquiums($row['colloquiums'])
                ->setRegistrationFees($row['colloquiums_registration_fees'])
                ->setPurchasingCard($row['colloquiums_purchasing_card'])
                ->setMissionOrder($this);
        }

        $this
            ->setValidators($validators)
            ->loadFiles()
            ->loadTransportChoices();

        return $this;
    }

    public function loadTransportChoices(): self
    {
        $transportChoices = [];

        $query = "SELECT dmotc.`id_mission_order`, dmotc.`id_transport_choice`, dtc.`name` as name_tranport_choice
            FROM `dmission_order_transport_choices` dmotc
            JOIN `dtransport_choice` dtc ON dtc.id = dmotc.id_transport_choice
            WHERE dmotc.id_mission_order=:id_mission_order";
        $params = [
            'id_mission_order' => $this->getId(),
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $transportChoice = new TransportChoice();
            $transportChoice
                ->setId($row['id_transport_choice'])
                ->setName($row['name_tranport_choice']);
            $transportChoices[] = $transportChoice;
        }
        $this->setTransportChoices($transportChoices);

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
            ->deleteTransportChoices()
            ->updateTransportChoices();
        $this
            ->getPersonalVehicle()
            ->deletePassengers($this)
            ->updatePassengers($this);

        if ($this->getIsModel()) {
            $this
                ->updateFiles()
                ->setIsModel(false);
        }

        echo DisplayMessage('success', T_("Demande enregistrée"));

        return $this;
    }

    public static function getBudgetValidator() {
      $query = "SELECT bv.*, u.firstname as firstname, u.lastname as lastname FROM dbudget_validator AS bv LEFT JOIN tusers u ON u.id = bv.user_id";
      $params = [];
      $mr = new MissionOrder();
      $resultsQuery = $mr->sql->query($query, $params);
      $retour = array();
      foreach ($resultsQuery as $row) {
	      $budgetId = $row['budget_id'];
	      //$user   = $row['firstname'] . " " . strtoupper($row['lastname']);
	      $user   = $row['user_id'];
	      $retour[$budgetId] = $user;
      }
      return $retour;
    }

    public function insert(): self
    {
        /*$query = "
                INSERT INTO `dmission_order` (
                `id_owner`, `title`, `id_service`, `first_mission_request`, `om_for_guest`, `collective_mission`, `list_people_involved_assignment`, `type_mission`, `care_organization`, `guardianship`, `budget_data`, `additional_budget_information`, `reason_for_mission`,
                `date_start`, `place_start`, `date_return`, `place_return_different`, `place_return`, `city_stay`, `country_stay`, `private_stay`, `private_stay_date_begin`, `private_stay_date_end`,
                `private_stay_place`, `off_market_accomodation`, `advance_request`, `transport_market`, `transport_market_justification`, `id_administrative_vehicle`,
                `personal_vehicle_numberplate`, `personal_vehicle_horsepower`, `personal_vehicle_trip_mileage`, `other_fees`,
                `colloquiums`, `colloquiums_registration_fees`, `colloquiums_purchasing_card`, `comment`, `status`
            )
            VALUES (
                :id_owner, :title, :id_service, :first_mission_request, :om_for_guest, :collective_mission, :list_people_involved_assignment, :type_mission, :care_organization, :guardianship, :budget_data, :additional_budget_information, :reason_for_mission, :date_start, :place_start,
                :date_return, :place_return_different, :place_return, :city_stay, :country_stay, :private_stay, :private_stay_date_begin, :private_stay_date_end, :private_stay_place,
                :off_market_accomodation, :advance_request, :transport_market, :transport_market_justification, :id_administrative_vehicle,
                :personal_vehicle_numberplate, :personal_vehicle_horsepower, :personal_vehicle_trip_mileage, :other_fees, :colloquiums, :colloquiums_registration_fees, :colloquiums_purchasing_card, :comment,
                :status
            )";*/
            /*TEST*/$query = "
                INSERT INTO `dmission_order` (
                `id_owner`, `title`, `id_service`, `first_mission_request`, `om_for_guest`, `guest_name`, `guest_mail`,`guest_birthdate`, `guest_phone_number`, `guest_labo`, `guest_country`, `collective_mission`, `list_people_involved_assignment`, `type_mission`, `care_organization`, `guardianship`, `budget_data`, `additional_budget_information`, `reason_for_mission`,
                `date_start`, `place_start`, `date_return`, `place_return_different`, `place_return`, `city_stay`, `country_stay`, `private_stay`, `private_stay_date_begin`, `private_stay_date_end`,
                `private_stay_place`, `off_market_accomodation`, `advance_request`, `transport_market`, `transport_market_justification`, `id_administrative_vehicle`,
                `personal_vehicle_numberplate`, `personal_vehicle_horsepower`, `personal_vehicle_trip_mileage`, `other_fees`,
                `colloquiums`, `colloquiums_registration_fees`, `colloquiums_purchasing_card`, `comment`, `status`, `amount_max`, `amount_estimated`, `amount_real`, `mission_type`, `incident_id`
            )
            VALUES (
                :id_owner, :title, :id_service, :first_mission_request, :om_for_guest, :guest_name, :guest_mail, :guest_birthdate, :guest_phonenumber, :guest_labo, :guest_country, :collective_mission, :list_people_involved_assignment, :type_mission, :care_organization, :guardianship, :budget_data, :additional_budget_information, :reason_for_mission, :date_start, :place_start,
                :date_return, :place_return_different, :place_return, :city_stay, :country_stay, :private_stay, :private_stay_date_begin, :private_stay_date_end, :private_stay_place,
                :off_market_accomodation, :advance_request, :transport_market, :transport_market_justification, :id_administrative_vehicle,
                :personal_vehicle_numberplate, :personal_vehicle_horsepower, :personal_vehicle_trip_mileage, :other_fees, :colloquiums, :colloquiums_registration_fees, :colloquiums_purchasing_card, :comment,
                :status, :amount_max, :amount_estimated, :amount_real, :mission_type, :incident_id
            )";

        $params = [
            'id_owner' => $this->getOwner()->getId(),
            'title' => $this->getTitle(),
            'id_service' => $this->getService()->getId(),
            'first_mission_request' => $this->isFirstMissionRequest(),
            'om_for_guest' => $this->isOmForGuest(),
            'guest_name' => $this->getGuestName(),//TEST
            'guest_mail' => $this->getGuestMail(),//TEST
            'guest_birthdate' => $this->getGuestBirthDate()->format('Y-m-d'),
            'guest_phonenumber' => $this->getGuestPhoneNumber(),
            'guest_labo' => $this->getGuestLabo(),//TEST
            'guest_country' => $this->getGuestCountry(),//TEST
            'collective_mission' => $this->isCollectiveMission(),
            'list_people_involved_assignment' => $this->getListPeopleInvolvedAssignment(),
            'type_mission' => $this->getTypeMission(),
            'care_organization' => $this->getCareOrganization(),
            'guardianship' => $this->getGuardianShip(),
            'budget_data' => $this->getBudgetData()->getId(),
            'additional_budget_information' => $this->getAdditionalBudgetInformation(),
            'reason_for_mission' => $this->getReasonForMission(),
            'date_start' => $this->getDateStart()->format('Y-m-d H:i:s'),
            'place_start' => $this->getPlaceStart(),
            'date_return' => $this->getDateReturn()->format('Y-m-d H:i:s'),
            'place_return_different' => $this->isPlaceReturnDifferent(),
            'place_return' => $this->getPlaceReturn(),
            'city_stay' => $this->getCityStay(),
            'country_stay' => $this->getCountryStay(),
            'private_stay' => $this->getPrivateStay()->getIsPrivateStay(),
            'private_stay_date_begin' => $this->getPrivateStay()->getDateBegin('Y-m-d'),
            'private_stay_date_end' => $this->getPrivateStay()->getDateEnd('Y-m-d'),
            'private_stay_place' => $this->getPrivateStay()->getPlace(),
            'off_market_accomodation' => $this->isOffMarketAccomodation(),
            'advance_request' => $this->isadvanceRequest(),
            'transport_market' => $this->isTransportMarket(),
            'transport_market_justification' => $this->getTransportMarketJustification(),
            'id_administrative_vehicle' => $this->getAdministrativeVehicle()->getId(),
            'personal_vehicle_numberplate' => $this->getPersonalVehicle()->getNumberplate(),
            'personal_vehicle_horsepower' => $this->getPersonalVehicle()->getHorsepower(),
            'personal_vehicle_trip_mileage' => $this->getPersonalVehicle()->getTripMileage(),
            'other_fees' => $this->getOtherFees(),
            'colloquiums' => $this->getColloquiums()->getIsColloquiums(),
            'colloquiums_registration_fees' => $this->getColloquiums()->getRegistrationFees(),
            'colloquiums_purchasing_card' => $this->getColloquiums()->isPurchasingCard(),
	        'comment' => $this->getComment(),
	        'amount_max' => $this->getAmountMax(),
	        'amount_estimated' => $this->getEstimatedAmount(),
	        'amount_real' => $this->getRealAmount(),
	        'mission_type' => $this->getMissionType(),
	        'incident_id' => $this->getIncidentId(),
            'status' => $this->getStatus()
        ];

        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());
        $this->updateInvitationToken();
        return $this;
    }

    public function updateInvitationToken()
    {
	    //$token = md5(uniqid(rand()), true);
	    $token = bin2hex(random_bytes(16));;
	    $query = "UPDATE `dmission_order` set `invitation_token`=:invitation_token where `id`=:id";
	    $params = ['invitation_token' => $token, 'id' => $this->getId() ];
	    $this->sql->query($query, $params, false);
	    $this->setInvitationToken($token);
	    return $this;
    }

    public function update(): self
    {
        /*$query = "
            UPDATE `dmission_order`
            SET
                `title`=:title,
                `id_service`=:id_service,
                `first_mission_request`=:first_mission_request,
                `om_for_guest`=:om_for_guest,
                `collective_mission`=:collective_mission,
                `list_people_involved_assignment`=:list_people_involved_assignment,
                `type_mission`=:type_mission,
                `care_organization`=:care_organization,
                `guardianship`=:guardianship,
                `budget_data`=:budget_data,
                `additional_budget_information`=:additional_budget_information,
                `reason_for_mission`=:reason_for_mission,
                `date_start`=:date_start,
                `place_start`=:place_start,
                `date_return`=:date_return,
                `place_return_different`=:place_return_different,
                `place_return`=:place_return,
                `city_stay`=:city_stay,
                `country_stay`=:country_stay,
                `private_stay`=:private_stay,
                `private_stay_date_begin`=:private_stay_date_begin,
                `private_stay_date_end`=:private_stay_date_end,
                `private_stay_place`=:private_stay_place,
                `off_market_accomodation`=:off_market_accomodation,
                `advance_request`=:advance_request,
                `transport_market`=:transport_market,
                `transport_market_justification`=:transport_market_justification,
                `id_administrative_vehicle`=:id_administrative_vehicle,
                `personal_vehicle_numberplate`=:personal_vehicle_numberplate,
                `personal_vehicle_horsepower`=:personal_vehicle_horsepower,
                `personal_vehicle_trip_mileage`=:personal_vehicle_trip_mileage,
                `other_fees`=:other_fees,
                `colloquiums`=:colloquiums,
                `colloquiums_registration_fees`=:colloquiums_registration_fees,
                `colloquiums_purchasing_card`=:colloquiums_purchasing_card,
                `comment`=:comment,
                `status`=:status
            WHERE id=:id";*/
            /*TEST*/$query = "
            UPDATE `dmission_order`
            SET
                `title`=:title,
                `id_service`=:id_service,
                `first_mission_request`=:first_mission_request,
                `om_for_guest`=:om_for_guest,
                `guest_name`=:guest_name,
                `guest_mail`=:guest_mail,
                `guest_phone_number`=:guest_phonenumber,
                `guest_labo`=:guest_labo,
                `guest_mail`=:guest_mail,
                `guest_birthdate`=:guest_birthdate,
                `collective_mission`=:collective_mission,
                `list_people_involved_assignment`=:list_people_involved_assignment,
                `type_mission`=:type_mission,
                `care_organization`=:care_organization,
                `guardianship`=:guardianship,
                `budget_data`=:budget_data,
                `additional_budget_information`=:additional_budget_information,
                `reason_for_mission`=:reason_for_mission,
                `date_start`=:date_start,
                `place_start`=:place_start,
                `date_return`=:date_return,
                `place_return_different`=:place_return_different,
                `place_return`=:place_return,
                `city_stay`=:city_stay,
                `country_stay`=:country_stay,
                `private_stay`=:private_stay,
                `private_stay_date_begin`=:private_stay_date_begin,
                `private_stay_date_end`=:private_stay_date_end,
                `private_stay_place`=:private_stay_place,
                `off_market_accomodation`=:off_market_accomodation,
                `advance_request`=:advance_request,
                `transport_market`=:transport_market,
                `transport_market_justification`=:transport_market_justification,
                `id_administrative_vehicle`=:id_administrative_vehicle,
                `personal_vehicle_numberplate`=:personal_vehicle_numberplate,
                `personal_vehicle_horsepower`=:personal_vehicle_horsepower,
                `personal_vehicle_trip_mileage`=:personal_vehicle_trip_mileage,
                `other_fees`=:other_fees,
                `colloquiums`=:colloquiums,
                `colloquiums_registration_fees`=:colloquiums_registration_fees,
                `colloquiums_purchasing_card`=:colloquiums_purchasing_card,
		`comment`=:comment,
		`amount_max`= :amount_max,
		`amount_estimated`= :amount_estimated,
		`incident_id`= :incident_id,
                `status`=:status
            WHERE id=:id";
        $params = [
            'title' => $this->getTitle(),
            'id_service' => $this->getService()->getId(),
            'first_mission_request' => $this->isFirstMissionRequest(),
            'om_for_guest' => $this->isOmForGuest(),
            'guest_name' => $this->getGuestName(),//TEST
            'guest_mail' => $this->getGuestMail(),//TEST
            'guest_phonenumber' => $this->getGuestPhoneNumber(),
            'guest_birthdate' => $this->getGuestBirthDate()->format('Y-m-d'),
            'guest_labo' => $this->getGuestLabo(),//TEST
            'guest_country' => $this->getGuestCountry(),//TEST
            'collective_mission' => $this->isCollectiveMission(),
            'list_people_involved_assignment' => $this->getListPeopleInvolvedAssignment(),
            'type_mission' => $this->getTypeMission(),
            'care_organization' => $this->getCareOrganization(),
            'guardianship' => $this->getGuardianShip(),
            'budget_data' => $this->getBudgetData()->getId(),
            'additional_budget_information' => $this->getAdditionalBudgetInformation(),
            'reason_for_mission' => $this->getReasonForMission(),
            'date_start' => $this->getDateStart()->format('Y-m-d H:i:s'),
            'place_start' => $this->getPlaceStart(),
            'date_return' => $this->getDateReturn()->format('Y-m-d H:i:s'),
            'place_return_different' => $this->isPlaceReturnDifferent(),
            'place_return' => $this->getPlaceReturn(),
            'city_stay' => $this->getCityStay(),
            'country_stay' => $this->getCountryStay(),
            'private_stay' => $this->getPrivateStay()->getIsPrivateStay(),
            'private_stay_date_begin' => $this->getPrivateStay()->getDateBegin('Y-m-d'),
            'private_stay_date_end' => $this->getPrivateStay()->getDateEnd('Y-m-d'),
            'private_stay_place' => $this->getPrivateStay()->getPlace(),
            'off_market_accomodation' => $this->isOffMarketAccomodation(),
            'advance_request' => $this->isadvanceRequest(),
            'transport_market' => $this->isTransportMarket(),
            'transport_market_justification' => $this->getTransportMarketJustification(),
            'id_administrative_vehicle' => $this->getAdministrativeVehicle()->getId(),
            'personal_vehicle_numberplate' => $this->getPersonalVehicle()->getNumberplate(),
            'personal_vehicle_horsepower' => $this->getPersonalVehicle()->getHorsepower(),
            'personal_vehicle_trip_mileage' => $this->getPersonalVehicle()->getTripMileage(),
            'other_fees' => $this->getOtherFees(),
            'colloquiums' => $this->getColloquiums()->getIsColloquiums(),
            'colloquiums_registration_fees' => $this->getColloquiums()->getRegistrationFees(),
            'colloquiums_purchasing_card' => $this->getColloquiums()->isPurchasingCard(),
            'comment' => $this->getComment(),
	    'amount_max' => $this->getAmountMax(),
	    'amount_estimated' => $this->getEstimatedAmount(),
	    'incident_id' => $this->getIncidentId(),
	    'status' => $this->getStatus(),
            'id' => $this->getId()
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateDateAlerte(): self
    {

        $query = "
            UPDATE `dmission_order`
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
        $query = "DELETE FROM `dmission_order_validators`
            WHERE id_mission_order=:id_mission_order"
        ;
        $params = [
            'id_mission_order' => $this->getId(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateValidators(): self
    {
        foreach($this->getValidators() as $validator) {
            $query = "INSERT INTO `dmission_order_validators` (`id_mission_order`,`id_validator`)
                VALUES (
                    :id_mission_order,
                    :id_validator
                    )"
            ;
            $params = [
                'id_mission_order' => $this->getId(),
                'id_validator' => $validator->getId(),
            ];

            $this->sql->query($query, $params, false);
        }
        return $this;
    }

    public function deleteTransportChoices(): self
    {
        $query = "DELETE FROM `dmission_order_transport_choices`
            WHERE id_mission_order=:id_mission_order"
        ;
        $params = [
            'id_mission_order' => $this->getId(),
        ];

        $this->sql->query($query, $params, false);

        return $this;
    }

    public function updateTransportChoices(): self
    {
        foreach($this->getTransportChoices() as $transportChoice) {
            $query = "INSERT INTO `dmission_order_transport_choices` (`id_mission_order`,`id_transport_choice`)
                VALUES (
                    :id_mission_order,
                    :id_transport_choice
                    )"
            ;
            $params = [
                'id_mission_order' => $this->getId(),
                'id_transport_choice' => $transportChoice->getId(),
            ];

            $this->sql->query($query, $params, false);
        }
        return $this;
    }

    public function loadFiles(): self
    {
        $this->setRibAndSupplementarySheet([]);
        $this->getColloquiums()->setProgram(null);

        $files = File::loadFiles($this->getId());

        foreach ($files as $file) {
            switch ($file->getType()) {
                case File::TYPE_RIB_AND_SUPPLEMENTARY_SHEET:
                    $this->addRibAndSupplementarySheet($file);
                    break;
                case File::TYPE_COLLOQUIUMS_PROGRAM:
                    $this->getColloquiums()->setProgram($file);
                    break;
            }
        }

        return $this;
    }

    public function updateFiles(): self
    {
        foreach ($this->getRibAndSupplementarySheet() as $ribAndSupplementarySheet) {
            File::copy($ribAndSupplementarySheet, $this);
            $ribAndSupplementarySheet
                ->setIdMissionOrder($this->getId())
                ->add();
        }

        $program = $this->getColloquiums()->getProgram();
        if ($program) {
            File::copy($program, $this);
            $program
                ->setIdMissionOrder($this->getId())
                ->add();
        }

        return $this;
    }

    public function canNotification(array $extraDatas = []): BaseRequest
    {
        parent::canNotification($extraDatas);
        if($this->getStatus() == self::STATUS_VALID) $this->sendNotificationToGuestIfItExists();
        return $this;
    }

    /**
     * Sends a notification email to the guest if a guest email was specified.
     *
     * @return self The current instance of the MissionOrder object.
     */
    private function sendNotificationToGuestIfItExists(): self
    {
        if($this->getGuestMail()) {
            $toList = [$this->getGuestMail()];
            $title = T_("Invitation à une mission");
            $title .= ' - ';
            $title .= $this->getTitle();
            $title .= ' - ';
            $title .= $this->getOwner()->getFullName();
            $this->sendMail($this->parameters->getMailFromAddress(), $toList, $title, $this->getMailTemplate(self::TEMPLATE_MAIL_GUEST));
        }

        return $this;
    }
}
