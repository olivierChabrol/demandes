<?php

namespace Models\Request\Common;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class Service
{
    private $id;
    private $name;

    public function __construct()
    {
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDatas(array $datas): self
    {
        $this->setId($datas['service']);

        return $this;
    }

    public static function getCollection(): array
    {
        $services = [];
        $sql = Sql::getInstance();

        $query = "SELECT id, name 
            FROM `tservices`
            WHERE disable = 0
            ORDER BY name ASC"
        ;

        $results = $sql->query($query);

        foreach ($results as $row) {
            $service = new Service();
            $service
                ->setId($row['id'])
                ->setName($row['name']);
            $services[$service->getId()] = $service;
        }

        return $services;
    }
}