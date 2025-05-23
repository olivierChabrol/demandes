<?php

namespace Models\Request\Common;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class File
{
    const TYPE_RIB_AND_SUPPLEMENTARY_SHEET = 'rib-and-supplementary-sheet';
    const TYPE_COLLOQUIUMS_PROGRAM = 'colloquiums-program';
    const TYPE_QUOTE = 'quote';
    const TARGET_FILE_MISSION_ORDER = './upload/mission-order';
    const TARGET_FILE_PURCHASE_ORDER = './upload/purchase-order';
    const TARGET_FILE_TICKET = './upload/ticket';

    private $id;
    private $idMissionOrder;
    private $idPurchaseOrder;
    private $name;
    private $path;
    private $type;
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

    public function getIdMissionOrder(): ?int
    {
        return $this->idMissionOrder;
    }

    public function setIdMissionOrder(?int $idMissionOrder): self
    {
        $this->idMissionOrder = $idMissionOrder;
        return $this;
    }

    public function getIdPurchaseOrder(): ?int
    {
        return $this->idPurchaseOrder;
    }

    public function setIdPurchaseOrder(?int $idPurchaseOrder): self
    {
        $this->idPurchaseOrder = $idPurchaseOrder;
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public static function moveUploadedFile(object $object, string $uploadDir)
    {

      $targetFile = self::getTargetFileByObject($object);
      $origdir =$uploadDir;
      $filesorig=scandir($origdir);//contient les noms des fichiers pour la BDD
      //on copie les fichiers vers le dossier définitif
      if(file_exists($targetFile))
      {
        foreach($filesorig as $fi)
        {
          if($fi!='.' && $fi!='..')
          {
            if(rename($origdir."/".$fi,$targetFile."/".$fi)){
              $file = new File();
              $file
                  ->setName($fi)
                  ->setPath($targetFile."/".$fi)
                  ->setType($object->getType());

              $object->uploaded($file);
            }
          }
        }
        rmdir($origdir);
      }
      else {
        if(rename($origdir,$targetFile)){
          //print_r($filesorig);
          //print_r($targetFile);

          foreach($filesorig as $fi)
          {
            if($fi!='.' && $fi!='..')
            {
              $file = new File();
              $file
                  ->setName($fi)
                  ->setPath($targetFile."/".$fi)
                  ->setType($object->getType());

              $object->uploaded($file);
            }

          }

        }
      }



    }

    //TODO - enlever l'ancienne methode en dessous
    /*public static function upload(object $object, array $files)
    {
        foreach ($files as $type => $filesByType) {
            self::uploadFileByType($object, $filesByType, $type);
        }
    }

    private static function uploadFileByType(object $object, array $files, string $type)
    {
        $filesLength = count($files['tmp_name']);

        for($i=0; $i <= $filesLength; $i++) {
            if (!isset($files['size'][$i]) || $files['size'][$i] == 0) {
                continue;
            }

            $targetFile = self::getTargetFileByObject($object);

            //create purchase order directory if not exist
            if (!is_dir($targetFile))  {
                mkdir($targetFile, 0777, true);
            }
            $real_filename=preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $files['name'][$i]);
            if(CheckFileExtension($real_filename)==true) {
                //create upload folder if not exist
                $target_folder=$targetFile.'/';
                //generate storage filename
                $storage_filename=$object->getId().'_'.md5(uniqid()).'_'.$real_filename;

                if (move_uploaded_file($files['tmp_name'][$i], $target_folder.$storage_filename)) {
                    //content check
                    $file_content = file_get_contents($target_folder.$storage_filename, true);

                    if (preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
                        unlink($target_folder.$storage_filename); //remove file
                        echo DisplayMessage('error',T_("Fichier interdit"));
                    } else {
                        $file = new File();
                        $file
                            ->setName($real_filename)
                            ->setPath($target_folder.$storage_filename)
                            ->setType($type);

                        $object->uploaded($file);
                    }
                } else {
                    echo DisplayMessage('error',T_("Transfert impossible"));
                }
            } else {
                echo DisplayMessage('error',T_("Fichier interdit"));
                if ($rparameters['log'] && is_numeric($_GET['id'])) {
                    logit('security','Blacklisted file "'.$real_filename.'" blocked on ticket '.$_GET['id'],$_SESSION['user_id']);
                }
            }
        }
    }*/
    //fin enlever
    public static function copy(File $file, object $object)
    {
        $targetFile = self::getTargetFileByObject($object);

        //create upload folder if not exist
        $targetFolder= $targetFile.'/';
        //generate storage filename
        $storageFilename= $object->getId().'_'.md5(uniqid()).'_'.$file->getName();

        if (!is_dir($targetFolder))  {
            mkdir($targetFolder, 0777, true);
        }

        copy($file->getPath(), $targetFolder.$storageFilename);
        $file->setPath(self::getTargetPathFileByObject($targetFolder, $storageFilename,$object));
    }

    private static function getTargetFileByObject($object): string
    {
        $target = '';

        if ($object->isMissionOrder()) {
            $target =  self::TARGET_FILE_MISSION_ORDER;
            $target .= '/'.$object->getId();
        } else if ($object->isPurchaseOrder()) {
            $target =  self::TARGET_FILE_PURCHASE_ORDER;
            $target .= '/'.$object->getId();
        } else if ($object->isTicket()) {
            $target =  self::TARGET_FILE_TICKET;
        }

        return $target;
    }

    private static function getTargetPathFileByObject(string $targetFolder, string $storageFilename, object $object): string
    {
        $path = '';

        if ($object->isMissionOrder() || $object->isPurchaseOrder()) {
            $path =  $targetFolder.$storageFilename;
        } else if ($object->isTicket()) {
            $path =  $storageFilename;
        }

        return $path;
    }

    public static function loadFiles(?int $idMissionOrder = null, ?int $idPurchaseOrder = null): array
    {
        $files = [];
        $sql = Sql::getInstance();
        if ($idMissionOrder === null && $idPurchaseOrder === null) {
            return $files;
        }

        $query = "SELECT `id`, `id_mission_order`,`name`,`path`,`type`
            FROM `dfiles`
            WHERE 1=1";
        $params = [];

        if ($idMissionOrder) {
            $query .= " AND id_mission_order=:id_mission_order";
            $params['id_mission_order'] = $idMissionOrder;
        }

        if ($idPurchaseOrder) {
            $query .= " AND id_purchase_order=:id_purchase_order";
            $params['id_purchase_order'] = $idPurchaseOrder;
        }

        $results = $sql->query($query, $params);

        foreach ($results as $row) {
            $file = new File();
            $file
                ->setId($row['id'])
                ->setIdMissionOrder($row['id_mission_order'])
                ->setName($row['name'])
                ->setPath($row['path'])
                ->setType($row['type']);
            $files[] = $file;
        }

        return $files;
    }

    public function delete(): self
    {
        if(file_exists($this->getPath()))
        {
            unlink($this->getPath());
        }

        $request = "DELETE FROM `dfiles`
            WHERE id=:id";
        $params = array(
            'id' => $this->getId()
        );
        $this->sql->query($request, $params, false);

        return $this;
    }

    public function add(): self
    {
        $query = "INSERT INTO `dfiles` (`id_mission_order`, `id_purchase_order`,`name`,`path`,`type`)
            VALUES (
                :id_mission_order,
                :id_purchase_order,
                :name,
                :path,
                :type
                )";
        $params = [
            'id_mission_order' => $this->getIdMissionOrder(),
            'id_purchase_order' => $this->getIdPurchaseOrder(),
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'type' => $this->getType(),
        ];
        $this->sql->query($query, $params, false, true);

        $this->setId($this->sql->getLastInsertId());

        return $this;
    }
}
