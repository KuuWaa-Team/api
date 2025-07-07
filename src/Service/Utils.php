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
}
