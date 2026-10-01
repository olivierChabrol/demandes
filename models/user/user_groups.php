<?php

namespace Models\User;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class User_Groups
{
    private $id;
    private $user_id;
    private $group_id;
    private $sql;
    private string $table_name = 'duser_groups';

    public function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    public function get_groups_ids_by_user_id($user_id): array
    {
        $query = "
            SELECT group_id
            FROM `" . $this->table_name . "`
            WHERE user_id = :user_id
        ";
        $params = [
            'user_id' => $user_id
        ];

        $results = $this->sql->query($query, $params);

        $group_ids = [];
        foreach ($results as $row) {
            $group_ids[] = $row['group_id'];
        }

        return $group_ids;
    }

    public function get_groups_by_user_id($user_id): array
    {
        $query = "
            SELECT dwp_scientist_group.id, dwp_scientist_group.name
            FROM `" . $this->table_name . "`
            LEFT JOIN `dwp_scientist_group` ON " . $this->table_name . ".group_id = dwp_scientist_group.id
            WHERE " . $this->table_name . ".user_id = :user_id
        ";
        $params = [
            'user_id' => $user_id
        ];

        $results = $this->sql->query($query, $params);

        $groups = [];
        foreach ($results as $row) {
            $group = new Group();
            $group
                ->setId($row['id'])
                ->setName($row['name']);

            $groups[$group->getId()] = $group;
        }

        return $groups;
    }

    public function group_exists_for_user($group_id, $user_id): bool
    {
        $query = "
            SELECT COUNT(*) as count
            FROM `" . $this->table_name . "`
            WHERE group_id = :group_id AND user_id = :user_id
        ";
        $params = [
            'group_id' => $group_id,
            'user_id' => $user_id
        ];

        $results = $this->sql->query($query, $params);

        return isset($results[0]['count']) && $results[0]['count'] > 0;
    }

    public function add_group($group_id, $user_id) {
        
        if ($this->group_exists_for_user($group_id, $user_id)) {
            return; // Group already exists for the user, no need to add it again
        }
        $query = "
            INSERT INTO `" . $this->table_name . "` (group_id, user_id)
            VALUES (:group_id, :user_id)
        ";
        $params = [
            'group_id' => $group_id,
            'user_id' => $user_id
        ];

        $this->sql->query($query, $params, false, true);
    }

    public function get_users_ids_by_group_id($group_id): array
    {
        $query = "
            SELECT user_id
            FROM `" . $this->table_name . "`
            WHERE group_id = :group_id
        ";
        $params = [
            'group_id' => $group_id
        ];

        $results = $this->sql->query($query, $params);

        $user_ids = [];
        foreach ($results as $row) {
            $user_ids[] = $row['user_id'];
        }

        return $user_ids;
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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    public function setGroupId(?int $group_id): self
    {
        $this->group_id = $group_id;
        return $this;
    }
}
