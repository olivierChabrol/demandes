<?php

namespace Models\Request\MissionOrder;

require_once('models/user/user.php');
require_once('models/tool/sql.php');

use Models\User\User;
use Models\Tool\Sql;

class PersonalVehicle
{
    private $numberplate;
    private $horsepower;
    private $tripMileage;
    private $passengers;
    private $sql;

    public function __construct()
    {
        $this->sql = Sql::getInstance();
        $this->passengers = [];
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

    public function getHorsepower(): ?int
    {
        return $this->horsepower;
    }

    public function setHorsepower(?int $horsepower): self
    {
        $this->horsepower = $horsepower;
        return $this;
    }

    public function getTripMileage(): ?string
    {
        return $this->tripMileage;
    }

    public function setTripMileage(?string $tripMileage): self
    {
        $this->tripMileage = $tripMileage;
        return $this;
    }

    public function getPassengers(): array
    {
        return $this->passengers;
    }

    public function setPassengers(array $passengers): self
    {
        $this->passengers = $passengers;
        return $this;
    }

    public function hasPassenger(int $idPassengerToCheck): bool
    {
        foreach ($this->getPassengers() as $passenger) {
            if ($passenger->getId() == $idPassengerToCheck) {
                return true;
            }
        }

        return false;
    }

    public function getPassengersHasString(string $separator = ';'): string
    {
        $passengersHasString = '';

        foreach ($this->passengers as $passenger) {
            if (strlen($passengersHasString) > 0) {
                $passengersHasString .= ' '.$separator.' ';
            }
            $passengersHasString .= $passenger->getFullName();
        }

        return $passengersHasString;
    }

    public function checkForm(array &$datas): bool
    {
        if (in_array(MissionOrder::TRANSPORT_CHOICE_PERSONAL_VEHICLE, $datas['transport-choice'])) {
            if (!$datas['personal-vehicle-numberplate']) {
                echo DisplayMessage('error',T_("Le champ Plaque d'immmatriculation est requis"));
                return false;
            }
            if (!$datas['personal-vehicle-horsepower']) {
                echo DisplayMessage('error',T_("Le champ Nombre de chevaux est requis"));
                return false;
            }
            if (!$datas['personal-vehicle-trip-mileage']) {
                echo DisplayMessage('error',T_("Le champ Kilométrage du trajet est requis"));
                return false;
            }
            if (!$datas['personal-vehicle-passenger'] || count($datas['personal-vehicle-passenger']) == 0) {
                echo DisplayMessage('error',T_("Le champ Nom des passagers est requis"));
                return false;
            }
        } else {
            $datas['personal-vehicle-numberplate'] = null;
            $datas['personal-vehicle-horsepower'] = null;
            $datas['personal-vehicle-trip-mileage'] = null;
            $datas['personal-vehicle-passenger'] = [];
        }

        return true;
    }

    public function getDatas(array $datas): self
    {
        if(!isset($datas['personal-vehicle-horsepower']) || $datas['personal-vehicle-horsepower'] == null) {
          $datas['personal-vehicle-horsepower']=0;
	}
        $this
            ->setNumberplate($datas['personal-vehicle-numberplate'])
            ->setHorsepower($datas['personal-vehicle-horsepower'])
            ->setTripMileage($datas['personal-vehicle-trip-mileage']);

        $passengers = [];

        foreach ($datas['personal-vehicle-passenger'] as $idPassenger) {
            $passenger = new User();
            $passenger->setId($idPassenger);
            $passengers[] = $passenger;
        }

        $this->setPassengers($passengers);


        return $this;
    }

    public function loadPassengers(MissionOrder $missionOrder): self
    {
        $passengers = [];

        $query = "SELECT dmopvp.`id_mission_order`, dmopvp.`id_passenger`, tu.`firstname`, tu.`lastname` 
            FROM `dmission_order_personal_vehicle_passengers` dmopvp
            JOIN `tusers` tu ON tu.id = dmopvp.id_passenger
            WHERE dmopvp.id_mission_order=:id_mission_order";
        $params = [
            'id_mission_order' => $missionOrder->getId(),
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $passenger = new User();
            $passenger
                ->setId($row['id_passenger'])
                ->setFirstName($row['firstname'])
                ->setLastName($row['lastname']);
            $passengers[] = $passenger;
        }
        $this->setPassengers($passengers);

        return $this;
    }

    public function deletePassengers(MissionOrder $missionOrder): self
    {
        $query = "DELETE FROM `dmission_order_personal_vehicle_passengers`
            WHERE id_mission_order=:id_mission_order";
        $params = [
            'id_mission_order' => $missionOrder->getId(),
        ];

        $this->sql->query($query, $params, false);
        return $this;
    }

    public function updatePassengers(MissionOrder $missionOrder): self
    {
        foreach($this->getPassengers() as $passenger) {
            $query = "INSERT INTO `dmission_order_personal_vehicle_passengers` (`id_mission_order`,`id_passenger`) 
                VALUES (
                    :id_mission_order,
                    :id_passenger
                    )";
            $params = [
                'id_mission_order' => $missionOrder->getId(),
                'id_passenger' => $passenger->getId(),
            ];

            $this->sql->query($query, $params, false);
        }
        return $this;
    }
}
