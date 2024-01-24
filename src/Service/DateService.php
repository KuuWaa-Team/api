<?php

namespace App\Service;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DateService extends AbstractController{

    public static $DAYS = [
        "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"
    ];

    public static $MONTHS = [
        "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre",
    ];

    public function getFullDate(?\DateTimeInterface $date, bool $dayName = false, bool $year = false, bool $showDay = true): String
    {
        $new_date = "";
        if ($dayName) {
            $new_date .= self::$DAYS[(int) $date->format("N") - 1]
                . " ";
        }

        if ($showDay) {
            $new_date .= $date->format("d") . " ";
        }

        $new_date .= self::$MONTHS[(int) $date->format("n") - 1];

        if ($year) {
            $new_date .= " " . $date->format("Y");
        }

        return $new_date;
    }
}