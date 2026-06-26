<?php

namespace Models\Tool;

class AppContext
{
    private $isGuest = false;
    private $guestToken = null;

    public function __construct()
    {
        $this->setGuest(false);
    }

    public function setGuestToken(?string $token): self
    {
        if ($token === null || trim($token) === '') {
            $this->guestToken = null;
            $this->setGuest(false);
            return $this;
        }
        else {
                $this->guestToken = $token;
                $this->setGuest(true);
        }

        return $this;
    }
    public function getGuestToken(): ?string
    {
        return $this->guestToken;
    }
    public function isGuest(): bool
    {
        return $this->isGuest;
    }
    public function isLabUser(): bool
    {
        return !$this->isGuest;
    }
    public function setGuest(?bool $isGuest): self
    {
        $this->isGuest = $isGuest;
        return $this;
    }




}