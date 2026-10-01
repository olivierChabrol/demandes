<?php

namespace Models\User;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class User_WP
{
    private $id;
    private $user_id;
    private $wp_user_id;
    private $sql;
    private string $table_name = 'dwp_user_id';

    public function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    public function getWpUserId(): ?int
    {
        return $this->wp_user_id;
    }

    public function setWpUserId(?int $wp_user_id): self
    {
        $this->wp_user_id = $wp_user_id;
        return $this;
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

}
