<?php
/**
 * Auth : helpers de verification de permission.
 *
 * Deux familles de methodes :
 *   - requireX() : si la check echoue, redirige sur /connexion et exit().
 *                  A utiliser en tete de methodes de controleurs classiques.
 *   - isX()      : retourne un bool, ne fait rien d'autre.
 *                  A utiliser dans les endpoints AJAX qui veulent renvoyer
 *                  du JSON personnalise au lieu d'un redirect HTML.
 *
 * Convention des profils (table `profils`) :
 *   1 = Administrateur
 *   2 = Restaurateur
 *   3 = Client
 * Plus le chiffre est petit, plus le profil a de droits.
 */
class Auth
{
    private const REDIRECT_URL = '/connexion';

    // ------------------------------------------------------------------
    // Methodes booleennes (pour AJAX / verification custom)
    // ------------------------------------------------------------------

    public static function isConnected(): bool
    {
        return isset($_SESSION['connected']) && $_SESSION['connected'] === true;
    }

    public static function isAdmin(): bool
    {
        return self::isConnected() && (int)($_SESSION['user']['profil'] ?? 99) <= 1;
    }

    public static function isRestaurateur(): bool
    {
        return self::isConnected() && (int)($_SESSION['user']['profil'] ?? 99) <= 2;
    }

    public static function hasExactProfile(int $profil): bool
    {
        return self::isConnected() && (int)($_SESSION['user']['profil'] ?? 0) === $profil;
    }

    // ------------------------------------------------------------------
    // Methodes "require" (redirect + exit si echec)
    // ------------------------------------------------------------------

    public static function requireConnected(): void
    {
        if (!self::isConnected()) self::reject();
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) self::reject();
    }

    public static function requireRestaurateur(): void
    {
        if (!self::isRestaurateur()) self::reject();
    }

    public static function requireExactProfile(int $profil): void
    {
        if (!self::hasExactProfile($profil)) self::reject();
    }

    // ------------------------------------------------------------------
    // Privee
    // ------------------------------------------------------------------

    private static function reject(): void
    {
        header('Location: ' . (APP_URL ?? '') . self::REDIRECT_URL);
        exit();
    }
}
