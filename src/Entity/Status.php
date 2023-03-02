<?php
namespace App\Entity;
enum Status: string
{
    case ENCOURS = "ENCOURS";
    case ANNULE = "ANNULE";
    case LIVRED = "LIVRED";
    public static function toArray(): array
    {
        return [
            self::ENCOURS,
            self::ANNULE,
            self::LIVRED,
        ];
    }
    
}