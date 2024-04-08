<?php

namespace Models\Request\MissionOrder;

require_once('models/request/common/file.php');

use Models\Request\Common\File;

class Colloquiums
{
    private $isColloquiums;
    private $registrationFees;
    private $purchasingCard;
    private $program;
    private $missionOrder;

    public function getIsColloquiums(): ?bool
    {
        return $this->isColloquiums;
    }

    public function setIsColloquiums(?bool $colloquiums)
    {
        $this->isColloquiums = $colloquiums;
        return $this;
    }

    public function getRegistrationFees(): ?string
    {
        return $this->registrationFees;
    }

    public function setRegistrationFees(?string $registrationFees): self
    {
        $this->registrationFees = $registrationFees;
        return $this;
    }

    public function isPurchasingCard(): ?bool
    {
        return $this->purchasingCard;
    }

    public function setPurchasingCard(?bool $purchasingCard)
    {
        $this->purchasingCard = $purchasingCard;
        return $this;
    }

    public function getProgram(): ?File
    {
        return $this->program;
    }

    public function setProgram(File $program = null): self
    {
        $this->program = $program;
        return $this;
    }

    public function getMissionOrder(): MissionOrder
    {
        return $this->missionOrder;
    }

    public function setMissionOrder(MissionOrder $missionOrder)
    {
        $this->missionOrder = $missionOrder;
        return $this;
    }

    public function checkForm(array &$datas, array $files): bool
    {
        if (isset($datas['colloquiums'])) {
            if (!$datas['colloquiums-registration-fees']) {
                echo DisplayMessage('error',T_("Le champ Frais d'inscription est requis"));
                return false;
            }
            if ((!$files['colloquiums-program'] || !$files['colloquiums-program']['name'][0]) && !$this->getProgram()) {
                echo DisplayMessage('error',T_("Le champ Programme est requis"));
                return false;
            }
        } else {
            $datas['colloquiums-registration-fees'] = null;
            $datas['colloquiums-purchasing-card'] = null;

            $file = $this->getProgram();
            if ($file) {
                $file->delete();
                $this->setProgram(null);
            }
        }

        return true;
    }

    public function getDatas(array $datas)
    {
        $colloquiums = (isset($datas['colloquiums'])) ? $datas['colloquiums'] : null;
        $colloquiumsRegistrationFees = (isset($datas['colloquiums-registration-fees'])) ? $datas['colloquiums-registration-fees'] : null;
        $colloquiumsPurchasingCard = (isset($datas['colloquiums-purchasing-card'])) ? $datas['colloquiums-purchasing-card'] : null;

        return $this
            ->setIsColloquiums($colloquiums)
            ->setRegistrationFees($colloquiumsRegistrationFees)
            ->setPurchasingCard($colloquiumsPurchasingCard);
    }
}
