<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Compteur
{
    #[MongoDB\Id]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected string $name;

    #[MongoDB\Field(type: 'int')]
    protected int $compteur;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): string
    {
        $this->name = $name;

        return $this->name;
    }
    
    public function getCompteur(): int
    {
        return $this->compteur;
    }

    public function setCompteur(int $compteur): string
    {
        $this->compteur = $compteur;

        return $this->compteur;
    }
}