<?php

namespace Models\Request\Common;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class BudgetData
{
    const CATEGORY_PURCHASE_ORDER = 1;
    const CATEGORY_MISSION_ORDER = 2;

    private $id;
    private $name;
    private $category;

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

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getDatas(array $datas): self
    {
        $this->setId($datas['budget-data']);

        return $this;
    }

    public static function getCollection(): array
    {
        $budgetDatas = [];
        $sql = Sql::getInstance();

        $query = "SELECT `id`,`name`,`cat` FROM `tsubcat` ORDER BY `name` ASC";


        $results = $sql->query($query);

        foreach ($results as $row) {
            $budgetData = new BudgetData();
            $budgetData
                ->setId($row['id'])
                ->setName($row['name'])
                ->setCategory($row['cat']);
            $budgetDatas[$budgetData->getId()] = $budgetData;
        }

        return $budgetDatas;
    }
}