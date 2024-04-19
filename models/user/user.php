<?php

namespace Models\User;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class User
{
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $custom1;
    private $sql;

    public function __construct()
    {
        $this->sql = Sql::getInstance();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName($separator = ' ')
    {
        return $this->firstName.$separator.strtoupper($this->lastName);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getLabo(): ?string
    {
        return $this->labo;
    }

    public function setLabo(?string $labo): self
    {
        $this->labo = $labo;
        return $this;
    }

    public function getCustom1(): ?string
    {
        return $this->custom1;
    }

    public function setCustom1(?string $custom1): self
    {
        $this->custom1 = $custom1;
        return $this;
    }

    public function load(): self
    {
        $query = "
            SELECT firstname, lastname, mail, custom1, name
            FROM `tusers`
            LEFT JOIN `tcompany` ON tusers.company = tcompany.id
            WHERE tusers.id= :id
        ";
        $params = [
            'id' => $this->getId()
        ];

        $results = $this->sql->query($query, $params);

        foreach ($results as $row) {
            $this
                ->setFirstName($row['firstname'])
                ->setLastName($row['lastname'])
                ->setEmail($row['mail'])
                ->setLabo($row['name'])
                ->setCustom1($row['custom1']);
        }

        return $this;
    }

    public static function getCollection(): array
    {
        $users = [];
        $sql = Sql::getInstance();

        $query = "
            SELECT tusers.id, firstname, lastname, mail, custom1, name
            FROM `tusers`
            LEFT JOIN `tcompany` ON tusers.company = tcompany.id
            WHERE (lastname!='' OR firstname!='')
            AND tusers.disable != 1
            ORDER BY lastname ASC, firstname ASC
        ";

        $results = $sql->query($query);

        foreach ($results as $row) {
            $user = new User();
            $user
                ->setId($row['id'])
                ->setFirstName($row['firstname'])
                ->setLastName($row['lastname'])
                ->setEmail($row['mail'])
                ->setLabo($row['name'])
                ->setCustom1($row['custom1']);

            $users[$user->getId()] = $user;
        }

        return $users;
    }
}
