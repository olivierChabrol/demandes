<?php

namespace Models\Request\PurchaseOrder;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class CodeNacre
{
    const ID_UNKNOW = 1;

    private $id;
    private $code;
    private $wording;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getWording(): string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;
        return $this;
    }

    public function save(): self
    {
        $this->getByCode()
            ? $this->update()
            : $this->insert();

        return $this;
    }

    private function insert(): self
    {
        $query = "INSERT INTO `dcode_nacre` (code, wording)
            VALUES (:code, :wording)
            ";
        $params = [
            'code' => $this->getCode(),
            'wording' => $this->getWording()
        ];

        $sql = Sql::getInstance();
        $sql->query($query, $params, false);

        return $this;
    }

    private function update(): self
    {
        $query = "UPDATE `dcode_nacre`
            SET wording=:wording
            WHERE code=:code
            ";
        $params = [
            'code' => $this->getCode(),
            'wording' => $this->getWording()
        ];

        $sql = Sql::getInstance();
        $sql->query($query, $params, false);

        return $this;
    }

    private function getByCode(): array
    {
        $query = "SELECT id, code, wording
            FROM `dcode_nacre`
            WHERE `code`=:code";
        $params = [
          'code' => $this->getCode()
        ];

        $sql = Sql::getInstance();

        return $sql->query($query, $params);
    }

    public static function getCollection(): array
    {
        $codeNacreList = [];

        $query = "SELECT id, code, wording
            FROM `dcode_nacre`
            WHERE id != :id_unknow
            ORDER BY code ASC";
        $params = [
            'id_unknow' => self::ID_UNKNOW
        ];

        $sql = Sql::getInstance();
        $results = $sql->query($query, $params);

        foreach ($results as $row) {
            $codeNacre = new CodeNacre();
            $codeNacre
                ->setId($row['id'])
                ->setCode($row['code'])
                ->setWording($row['wording']);
            $codeNacreList[$codeNacre->getId()] = $codeNacre;
        }

        return $codeNacreList;
    }
}
