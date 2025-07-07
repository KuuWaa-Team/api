<?php

namespace App\Service;

class EventAdminRoles
{
    public static string $VERIFY_INVITATIONS     = "VERIFY_INVITATIONS";
    public static string $SEND_INVITATIONS       = "SEND_INVITATIONS";
    public static string $ADD_ADMINS             = "ADD_ADMINS";
    public static string $MANAGE_TABLES          = "MANAGE_TABLES";
    public static string $MANAGE_DRINKS          = "MANAGE_DRINKS";
    public static string $MANAGE_GUESTBOOK       = "MANAGE_GUESTBOOK";
    public static string $MANAGE_PHOTO_ALBUM     = "MANAGE_PHOTO_ALBUM";
    public static string $VIEW_MUSIC_REQUESTS    = "VIEW_MUSIC_REQUESTS";
    public static string $MANAGE_INTERACTIVE_GAME = "MANAGE_INTERACTIVE_GAME";

    /**
     * Retourne tous les rôles disponibles pour un Event Admin
     */
    public static function all(): array
    {
        return [
            self::$VERIFY_INVITATIONS,
            self::$SEND_INVITATIONS,
            self::$ADD_ADMINS,
            self::$MANAGE_TABLES,
            self::$MANAGE_DRINKS,
            self::$MANAGE_GUESTBOOK,
            self::$MANAGE_PHOTO_ALBUM,
            self::$VIEW_MUSIC_REQUESTS,
            self::$MANAGE_INTERACTIVE_GAME,
        ];
    }
}
