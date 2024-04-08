<?php

namespace Models\Tool;

class Sql
{
    private static $instance = null;
    private $lastInsertId = null;
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }

    public function setLastInsertId(?int $lastInsertId): self
    {
        $this->lastInsertId = $lastInsertId;
        return $this;
    }

    public static function getInstance($db = null) {
        if(is_null(self::$instance)) {
            self::$instance = new Sql($db);
        }

        return self::$instance;
    }

    public function query(string $query, array $params = [], $fetch = true, $lastId = false): array
    {
        $results = [];

        try {
            $qry = $this->db->prepare($query);
            $qry->execute($params);

            if ($fetch) {
                $results = $qry->fetchAll();
            }
            if ($lastId) {
                $this->setLastInsertId($this->db->lastInsertId());
            }

            $qry->closeCursor();
        } catch(\Exception $e) {
            echo DisplayMessage('error', $e->getMessage());
        }

        return $results;
    }
}