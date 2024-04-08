<?php

namespace Models\Request\MissionOrder;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class AdministrativeVehicle
{
    private $id;
    private $name;
    private $numberplate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getNumberplate(): ?string
    {
        return $this->numberplate;
    }

    public function setNumberplate(?string $numberplate): self
    {
        $this->numberplate = $numberplate;
        return $this;
    }

    public function checkForm(array &$datas): bool
    {
        if (in_array(MissionOrder::TRANSPORT_CHOICE_ADMINISTRATIVE_VEHICLE, $datas['transport-choice'])) {
            if (!$datas['transport-choice-administrative-vehicle']) {
                echo DisplayMessage('error',T_("Le champ Véhicule administratif est requis"));
                return false;
            }
        } else {
            $datas['transport-choice-administrative-vehicle'] = null;
        }

        return true;
    }

    public function getDatas(array $datas): self
    {
        return $this
            ->setId($datas['transport-choice-administrative-vehicle']);
    }

    public static function getCollection(): array
    {
        $administrativeVehicles = [];

        $query = "SELECT id, name, numberplate 
            FROM `dadministrative_vehicle`
            ORDER BY name, numberplate ASC";

        $sql = Sql::getInstance();
        $results = $sql->query($query);

        foreach ($results as $row) {
            $administrativeVehicle = new AdministrativeVehicle();
            $administrativeVehicle
                ->setId($row['id'])
                ->setName($row['name'])
                ->setNumberPlate($row['numberplate']);

            $administrativeVehicles[$administrativeVehicle->getId()] = $administrativeVehicle;
        }

        return $administrativeVehicles;
    }
}
