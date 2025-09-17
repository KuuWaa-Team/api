<?php

namespace App\Service;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DateService extends AbstractController{

    public static $DAYS = [
        "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"
    ];

    public static $MONTHS = [
        "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre",
    ];

    public function getFullDate(
        ?\DateTimeInterface $startDate,
        bool $dayName = false,
        bool $year = false,
        bool $showDay = true,
        ?\DateTimeInterface $endDate = null,
    ): string {
        if (!$startDate) {
            return "";
        }

        $formatDate = function (\DateTimeInterface $date) use ($dayName, $year, $showDay): string {
            $str = "";
            if ($dayName) {
                $str .= self::$DAYS[(int) $date->format("N") - 1] . " ";
            }
            if ($showDay) {
                $str .= $date->format("d") . " ";
            }
            $str .= self::$MONTHS[(int) $date->format("n") - 1];
            if ($year) {
                $str .= " " . $date->format("Y");
            }
            return $str;
        };

        if ($endDate && $endDate->getTimestamp() - $startDate->getTimestamp() >= 86400) {
            return "Du " . $formatDate($startDate) . " au " . $formatDate($endDate);
        }

        return $formatDate($startDate);
    }


    public function imageToBase64($path) {
        try {
            $path = $path;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        } catch (Exception $e) {
            
        }
    } 
}