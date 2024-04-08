<?php

namespace Models\Request\MissionOrder;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class TransportChoice
{
    private $id;
    private $name;

    public function __construct()
    {
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public static function getCollection(): array
    {
        $transportChoices = [];

        $query = "SELECT id, name 
            FROM `dtransport_choice`
            ORDER BY name ASC";

        $sql = Sql::getInstance();
        $results = $sql->query($query);

        foreach ($results as $row) {
            $transportChoice = new TransportChoice();
            $transportChoice
                ->setId($row['id'])
                ->setName($row['name']);

            $transportChoices[$transportChoice->getId()] = $transportChoice;
        }

        return $transportChoices;
    }
}
