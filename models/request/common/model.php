<?php

namespace Models\Request\Common;

require_once('models/request/purchase_order/purchase_order.php');
require_once('models/request/mission_order/mission_order.php');
require_once('models/tool/sql.php');

use Models\Request\MissionOrder\MissionOrder;
use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\Tool\Sql;

class Model
{
    const TYPE_MISSION_ORDER = 'mission_order';
    const TYPE_PURCHASE_ORDER = 'purchase_order';

    private $id;
    private $missionOrder;
    private $purchaseOrder;
    private $idUser;
    private $sql;

    public function __construct()
    {
        $this->sql = Sql::getInstance();
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

    public function getMissionOrder(): MissionOrder
    {
        return $this->missionOrder;
    }

    public function setMissionOrder(MissionOrder $missionOrder): self
    {
        $this->missionOrder = $missionOrder;
        return $this;
    }

    public function getPurchaseOrder(): PurchaseOrder
    {
        return $this->purchaseOrder;
    }

    public function setPurchaseOrder(PurchaseOrder $purchaseOrder): self
    {
        $this->purchaseOrder = $purchaseOrder;
        return $this;
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public static function load(int $idUser): array
    {
        $models = [
            self::TYPE_PURCHASE_ORDER => [],
            self::TYPE_MISSION_ORDER => [],
        ];
        $sql = Sql::getInstance();

        $query = "SELECT dm.`id`, dm.`id_mission_order`, dm.`id_purchase_order`, dmo.`title` as `title_mission_order`, dpo.`title` as `title_purchase_order`, dm.`id_user`
            FROM `dmodels` dm
            LEFT JOIN `dmission_order` dmo ON dmo.id = dm.id_mission_order
            LEFT JOIN `dpurchase_order` dpo ON dpo.id = dm.id_purchase_order
            WHERE dm.id_user=:id_user
            ORDER BY dm.id_mission_order ASC, dm.id_purchase_order ASC";
        $params = array(
            'id_user' => $idUser,
        );

        $results = $sql->query($query, $params);

        foreach ($results as $row) {
            $missionOrder = new MissionOrder($sql->db);
            if ($row['id_mission_order']) {
                $missionOrder
                    ->setId($row['id_mission_order'])
                    ->setTitle($row['title_mission_order']);
            }

            $purchaseOrder = new PurchaseOrder($sql->db);
            if ($row['id_purchase_order']) {
                $purchaseOrder
                    ->setId($row['id_purchase_order'])
                    ->setTitle($row['title_purchase_order']);
            }
            $model = new Model();
            $model
                ->setId($row['id'])
                ->setMissionOrder($missionOrder)
                ->setPurchaseOrder($purchaseOrder)
                ->setIdUser($row['id_user']);

            if ($model->getMissionOrder()->getId() > 0) {
                $type = self::TYPE_MISSION_ORDER;
            } elseif ($model->getPurchaseOrder()->getId() > 0) {
                $type = self::TYPE_PURCHASE_ORDER;
            }
            $models[$type][] = $model;
        }

        return $models;
    }

    public function save(): self
    {
        $query = "INSERT INTO `dmodels` (`id_mission_order`, `id_purchase_order`,`id_user`)
            VALUES (
                :id_mission_order,
                :id_purchase_order,
                :id_user          
            )
            ON DUPLICATE KEY UPDATE
                `id_mission_order`=:id_mission_order,
                `id_purchase_order`=:id_purchase_order,                
                `id_user`=:id_user"
        ;
        $params = [
            'id_mission_order' => $this->getMissionOrder()->getId(),
            'id_purchase_order' => $this->getPurchaseOrder()->getId(),
            'id_user' => $this->getIdUser(),
        ];

        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());

        echo DisplayMessage('success', T_("Modèle sauvegardé"));

        return $this;
    }

    public function delete(): self
    {
        $query = "DELETE FROM `dmodels`
            WHERE id = :id
              AND id_user = :id_user"
        ;
        $params = [
            'id' => $this->getId(),
            'id_user' => $this->getIdUser(),
        ];

        $this->sql->query($query, $params, false);

        echo DisplayMessage('success', T_("Modèle supprimé"));

        return $this;
    }
}