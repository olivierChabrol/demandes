<?php

namespace Models\Request\MissionOrder;

class PrivateStay
{
    private $isPrivateStay;
    private $dateBegin;
    private $dateEnd;
    private $place;


    public function __construct()
    {
        $this->dateBegin = new \DateTime();
        $this->dateEnd = new \DateTime();
    }

    public function getIsPrivateStay(): ?bool
    {
        return $this->isPrivateStay;
    }

    public function setIsPrivateStay(?bool $privateStay): self
    {
        $this->isPrivateStay = $privateStay;
        return $this;
    }

    public function getDateBegin(string $format)
    {
        return ($this->dateBegin)
            ? $this->dateBegin->format($format)
            : $this->dateBegin;
    }

    public function setDateBegin(?string $privateStayDateBegin, string $format = 'Y-m-d H:i:s'): self
    {
        $this->dateBegin = ($privateStayDateBegin)
            ? \DateTime::createFromFormat($format, $privateStayDateBegin)
            : null;
        return $this;
    }

    public function getDateEnd(string $format)
    {
        return ($this->dateEnd)
            ? $this->dateEnd->format($format)
            : $this->dateEnd;
    }

    public function setDateEnd(?string $privateStayDateEnd, string $format = 'Y-m-d H:i:s'): self
    {
        $this->dateEnd = ($privateStayDateEnd)
            ? \DateTime::createFromFormat($format, $privateStayDateEnd)
            : null;
        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $privateStayPlace): self
    {
        $this->place = $privateStayPlace;
        return $this;
    }

    public function checkForm(array &$datas): bool
    {
        if (isset($datas['private-stay'])) {
            if (!$datas['private-stay-date-begin']) {
                echo DisplayMessage('error',T_("Le champ Date de début du séjour privé est requis"));
                return false;
            }
            if (!$datas['private-stay-date-end']) {
                echo DisplayMessage('error',T_("Le champ Date de fin du séjour privé est requis"));
                return false;
            }
            if (!$datas['private-stay-place']) {
                echo DisplayMessage('error',T_("Le champ Lieu de séjour est requis"));
                return false;
            }

            $datePrivateStayBegin = \DateTime::createFromFormat('d/m/Y', $datas['private-stay-date-begin']);
            $datePrivateStayEnd = \DateTime::createFromFormat('d/m/Y', $datas['private-stay-date-end']);

            if ($datePrivateStayBegin > $datePrivateStayEnd) {
                echo DisplayMessage('error',T_("Le champ Date de fin du séjour privé doit être après le champ Date de début du séjour privé"));
                return false;
            }
        } else {
            $datas['private-stay-date-begin'] = null;
            $datas['private-stay-date-end'] = null;
            $datas['private-stay-place'] = null;
        }

        return true;
    }

    public function getDatas(array $datas)
    {
        $privateStay = (isset($datas['private-stay'])) ? $datas['private-stay'] : null;
        $dateBegin = (isset($datas['private-stay-date-begin'])) ? $datas['private-stay-date-begin'] : null;
        $dateEnd = (isset($datas['private-stay-date-end'])) ? $datas['private-stay-date-end'] : null;
        $place = (isset($datas['private-stay-place'])) ? $datas['private-stay-place'] : null;

        return $this
            ->setIsPrivateStay($privateStay)
            ->setDateBegin($dateBegin, 'd/m/Y')
            ->setDateEnd($dateEnd, 'd/m/Y')
            ->setPlace($place);
    }
}
