<?php

namespace App\Service;

class Utils
{
    public static string $EventAdminRoles_VERIFY_INVITATIONS     = "VERIFY_INVITATIONS";
    public static string $EventAdminRoles_SEND_INVITATIONS       = "SEND_INVITATIONS";
    public static string $EventAdminRoles_ADD_ADMINS             = "ADD_ADMINS";
    public static string $EventAdminRoles_MANAGE_TABLES          = "MANAGE_TABLES";
    public static string $EventAdminRoles_MANAGE_DRINKS          = "MANAGE_DRINKS";
    public static string $EventAdminRoles_MANAGE_GUESTBOOK       = "MANAGE_GUESTBOOK";
    public static string $EventAdminRoles_MANAGE_PHOTO_ALBUM     = "MANAGE_PHOTO_ALBUM";
    public static string $EventAdminRoles_VIEW_MUSIC_REQUESTS    = "VIEW_MUSIC_REQUESTS";
    public static string $EventAdminRoles_MANAGE_INTERACTIVE_GAME = "MANAGE_INTERACTIVE_GAME";

    /**
     * Retourne tous les rôles disponibles pour un Event Admin
     */
    public static function all(): array
    {
        return [
            self::$EventAdminRoles_VERIFY_INVITATIONS,
            self::$EventAdminRoles_SEND_INVITATIONS,
            self::$EventAdminRoles_ADD_ADMINS,
            self::$EventAdminRoles_MANAGE_TABLES,
            self::$EventAdminRoles_MANAGE_DRINKS,
            self::$EventAdminRoles_MANAGE_GUESTBOOK,
            self::$EventAdminRoles_MANAGE_PHOTO_ALBUM,
            self::$EventAdminRoles_VIEW_MUSIC_REQUESTS,
            self::$EventAdminRoles_MANAGE_INTERACTIVE_GAME,
        ];
    }

    public static function getTitle(string $role): string
    {
        return match ($role) {
            self::$EventAdminRoles_VERIFY_INVITATIONS      => "Vérifier les invitations",
            self::$EventAdminRoles_SEND_INVITATIONS        => "Envoyer les invitations",
            self::$EventAdminRoles_ADD_ADMINS              => "Gérer les administrateurs",
            self::$EventAdminRoles_MANAGE_TABLES           => "Gérer les tables",
            self::$EventAdminRoles_MANAGE_DRINKS           => "Gérer les boissons",
            self::$EventAdminRoles_MANAGE_GUESTBOOK        => "Livre d'or",
            self::$EventAdminRoles_MANAGE_PHOTO_ALBUM      => "Album photo",
            self::$EventAdminRoles_VIEW_MUSIC_REQUESTS     => "Voir les demandes musicales",
            self::$EventAdminRoles_MANAGE_INTERACTIVE_GAME => "Jeu interactif",
            default => $role,
        };
    }

    public static function getDescription(string $role): string
    {
        return match ($role) {
            self::$EventAdminRoles_VERIFY_INVITATIONS => "Contrôler et valider les invitations envoyées",
            self::$EventAdminRoles_SEND_INVITATIONS => "Envoyer les invitations aux invités",
            self::$EventAdminRoles_ADD_ADMINS => "Ajouter et gérer les administrateurs de l’événement",
            self::$EventAdminRoles_MANAGE_TABLES => "Attribuer et organiser les tables des invités",
            self::$EventAdminRoles_MANAGE_DRINKS => "Gérer la liste des boissons disponibles à l'événement",
            self::$EventAdminRoles_MANAGE_GUESTBOOK => "Superviser les messages dans le livre d’or",
            self::$EventAdminRoles_MANAGE_PHOTO_ALBUM => "Gérer l’album photo de l’événement",
            self::$EventAdminRoles_VIEW_MUSIC_REQUESTS => "Consulter les demandes musicales des invités",
            self::$EventAdminRoles_MANAGE_INTERACTIVE_GAME => "Créer et gérer le jeu interactif de l’événement",
            default => "Rôle inconnu",
        };
    }

}
