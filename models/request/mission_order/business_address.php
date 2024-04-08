<?php

namespace Models\Request\MissionOrder;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class BusinessAddress
{
    private $id;
    private $title;
    private $address;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function isAddress(?string $addressToCheck): bool
    {
        return $this->address == $addressToCheck;
    }

    public function save(): self
    {
        $this->getByTitle()
            ? $this->update()
            : $this->insert();

        return $this;
    }

    private function insert(): self
    {
        $query = "INSERT INTO `dbusiness_address` (title, address) 
            VALUES (:title, :address)";
        $params = [
            'title' => $this->getTitle(),
            'address' => $this->getAddress()
        ];

        $sql = Sql::getInstance();
        $sql->query($query, $params, false);

        return $this;
    }

    private function update(): self
    {
        $query = "UPDATE `dbusiness_address`
            SET address=:address
            WHERE title=:title
            ";
        $params = [
            'address' => $this->getAddress(),
            'title' => $this->getTitle()
        ];

        $sql = Sql::getInstance();
        $sql->query($query, $params, false);

        return $this;
    }

    private function getByTitle(): array
    {
        $query = "SELECT id, title, address
            FROM `dbusiness_address`
            WHERE `title`=:title";
        $params = [
            'title' => $this->getTitle()
        ];

        $sql = Sql::getInstance();

        return $sql->query($query, $params);
    }

    public static function getCollection(): array
    {
        $businessAddresses = [];

        $query = "SELECT id, title, address 
            FROM `dbusiness_address`
            ORDER BY title ASC";

        $sql = Sql::getInstance();
        $results = $sql->query($query);

        foreach ($results as $row) {
            $businessAddress = new BusinessAddress();
            $businessAddress
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setAddress($row['address']);

            $businessAddresses[] = $businessAddress;
        }

        return $businessAddresses;
    }
}
