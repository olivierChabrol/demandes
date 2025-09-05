<?php

namespace Models\User;

require_once('models/user/service.php');
require_once('models/tool/sql.php');

use Models\Request\Common\Service;
use Models\Tool\Sql;

class UserRequest
{
    private $id;
    private $personalAddress;
    private $personalAddressDefault;
    private $businessAddressDefault;
    private $services;
    private $custom1;
    private $sql;

    public function __construct()
    {
        $this->sql = Sql::getInstance();
        $this->personalAddress = '';
        $this->personalAddressDefault = false;
        $this->businessAddressDefault = null;
        $this->services = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getPersonalAddress(): string
    {
        return $this->personalAddress;
    }

    public function setPersonalAddress(?string $personalAddress): self
    {
        $this->personalAddress = $personalAddress;
        return $this;
    }

    public function isPersonalAddress(?string $addressToCheck): bool
    {
        return $this->personalAddress == $addressToCheck;
    }

    public function getPersonalAddressDefault(): ?bool
    {
        return $this->personalAddressDefault;
    }

    public function setPersonalAddressDefault(?bool $personalAddressDefault): self
    {
        $this->personalAddressDefault = $personalAddressDefault;
        return $this;
    }

    public function getBusinessAddressDefault(): ?string
    {
        return $this->businessAddressDefault;
    }

    public function setBusinessAddressDefault(?string $businessAddressDefault): self
    {
        $this->businessAddressDefault = $businessAddressDefault;
        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(array $services): self
    {
        $this->services = $services;
        return $this;
    }

    public function getCustom1(): ?string
    {
        return $this->custom1;
    }

    public function setCustom1(?string $custom1): self
    {
        $this->custom1 = $custom1;
        return $this;
    }

    public function checkForm(array &$datas): bool
    {
        if (
            !$this->isFreeAddress($datas['place-start']) && !$this->isFreeAddress($datas['place-return']) && isset($datas['place-return-different']) &&
            isset($datas['place-start-address-default']) && $datas['place-start-address-default'] && isset($datas['place-return-address-default'])
        ) {
            if ($datas['place-start-input'] !== $datas['place-return-input']) {
                echo DisplayMessage('error',T_("Vous avez sélectionné deux adresses par défaut différentes"));
                return false;
            }
        }
        if (!isset($datas['place-return-different'])) {
            $datas['place-return-input'] = null;
            $datas['place-return-address-save'] = null;
            $datas['place-return-address-default'] = null;
            $datas['place-return'] = null;
        }

        return true;
    }

    public function getDatas(array $datas): self
    {
        // get datas for place start and place return
        $save = $this->getDatasPlace($datas, 'place-start', 'place-return');
        $this->getDatasPlace($datas, 'place-return', 'place-start', $save);

        return $this;
    }

    private function getDatasPlace(array $datas, string $from, string $target, array $save = []): array
    {
        // get datas for personal address and business address
        return array(
            'savePersonalAddress' => $this->getDatasPersonalAddress($datas, $from, $target, $save),
            'saveBusinessAddress' => $this->getDatasBusinessAddress($datas, $from, $target, $save),
        );
    }

    private function getDatasPersonalAddress(array $datas, string $from, string $target, array $save): bool
    {
        $canSaveAddressDefault = $this->canSaveAddressDefault($datas, $from, $target);
        $savePersonalAddress = false;

        if (isset($datas[$from.'-address-save']) && $datas[$from.'-address-save']) {
            // address save is set, we get data to personal address
            $this->setPersonalAddress($datas[$from.'-input']);
        }

        if (
            !isset($datas[$from.'-address-save']) &&
            (isset($save['savePersonalAddress']) && $save['savePersonalAddress']) || (isset($datas[$from]) && $datas[$from] != 'personal_address') ||
            (isset($datas[$from.'-address-default']) && $this->getPersonalAddressDefault() == $datas[$from.'-address-default']) ||
            ($this->getPersonalAddressDefault() && $this->isFreeAddress($datas[$target]))
        ) {
            // we cannot get datas for personnal address because:
            // - address is free address
            // - address was saved previously
            // - address is business address
            // - address to save is the same as now
            // - adress save is not set
            return false;
        }

        if (
            $this->getPersonalAddressDefault() &&
            !isset($datas[$from.'-address-default']) &&
            !isset($datas[$target.'-address-default'])
        ) {
            // we cannot save default address because we have a personnal address set and address start and address return is not set
            $canSaveAddressDefault = false;
        }

        if ($canSaveAddressDefault && !$this->getPersonalAddressDefault() && isset($datas[$from.'-address-default'])) {
            // we set personal address by default
            $this->setPersonalAddressDefault(true);
            $this->setBusinessAddressDefault(null);
            $savePersonalAddress = true;
        } else if ($canSaveAddressDefault && $this->getPersonalAddressDefault()) {
            // we have already a personnal address by default
            $this->setPersonalAddressDefault(false);
            $savePersonalAddress = true;
        }

        return $savePersonalAddress;
    }

    private function getDatasBusinessAddress(array $datas, string $from, string $target, array $save): bool
    {
        $canSaveAddressDefault = $this->canSaveAddressDefault($datas, $from, $target);
        $saveBusinessAddress = false;

        if (
            !$canSaveAddressDefault || (isset($save['saveBusinessAddress']) && $save['saveBusinessAddress']) || $datas[$from] == 'personal_address' ||
            $this->isFreeAddress($datas[$from]) || !isset($datas[$from]) || !$datas[$from] || (isset($datas[$from.'-address-default']) && $this->getBusinessAddressDefault() == $datas[$from.'-address-default']) ||
            ($this->getBusinessAddressDefault() && $this->isFreeAddress($datas[$target]))
        ) {
            // we cannot get datas for business address because:
            // - address is free address
            // - address was saved previously
            // - address is personal address
            // - address to save is the same as now
            // - adress save is not set
            return false;
        }

        if (isset($datas[$from.'-address-default'])) {
            // we set business address by default
            $this->setBusinessAddressDefault($datas[$from.'-address-default']);
            $this->setPersonalAddressDefault(false);
            $saveBusinessAddress = true;
        }

        return $saveBusinessAddress;
    }

    private function canSaveAddressDefault(array $datas, string $from, string $target): bool
    {
        return (
            (!$this->getPersonalAddressDefault() && !$this->getBusinessAddressDefault()) ||
            isset($datas[$from.'-address-default']) || isset($datas[$target.'-address-default'])
        );
    }

    private function isFreeAddress($data)
    {
        return (
            $data &&
            $data != 'personal_address' &&
            ($data == 'free_address' || $data == 0)
        );
    }

    public function load(): self
    {
        $query = "
            SELECT dur.`personal_address`, dur.`personal_address_default`, dur.`business_address_default`, tu.`custom1`
            FROM `duser_request` dur
            JOIN `tusers` tu ON tu.id = dur.id_user
            WHERE dur.id_user = :id"
        ;
        $params = [
            'id' => $this->getId(),
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $this
                ->setPersonalAddress($row['personal_address'])
                ->setPersonalAddressDefault($row['personal_address_default'])
                ->setBusinessAddressDefault($row['business_address_default'])
                ->setCustom1($row['custom1']);
        }
        $this->loadServices();

        return $this;
    }

    public function save(): self
    {
        $query = "
            INSERT INTO `duser_request` (`id_user`, `personal_address`, `personal_address_default`, `business_address_default`) 
            VALUES (
                :id_user,
                :personal_address,
                :personal_address_default,
                :business_address_default                       
            )
            ON DUPLICATE KEY UPDATE
                `id_user`=:id_user, 
                `personal_address`=:personal_address, 
                `personal_address_default`=:personal_address_default, 
                `business_address_default`=:business_address_default"
        ;
        $params = [
            'id_user' => $this->getId(),
            'personal_address' => $this->getPersonalAddress(),
            'personal_address_default' => $this->getPersonalAddressDefault(),
            'business_address_default' => $this->getBusinessAddressDefault(),
        ];

        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());

        return $this;
    }

    public function loadServices()
    {
        $query = "
            SELECT ts.`id`, ts.`name` 
            FROM `tusers_services` as tus
            JOIN `tservices` ts ON tus.service_id = ts.id
            WHERE tus.user_id = :user_id"
        ;
        $params = [
            'user_id' => $this->getId(),
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $service = new Service();
            $service
                ->setId($row['id'])
                ->setName($row['name']);

            $this->services[$service->getId()] = $service;
        }

        return $this;
    }
}
