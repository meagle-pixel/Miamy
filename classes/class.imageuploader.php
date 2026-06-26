<?php

class ImageUploader
{
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    public const MAX_SIZE_BYTES     = 5_000_000; // 5 Mo

    /** Sous-dossier sous assets/img/ (ex: 'plats', 'restaurants') */
    private string $folder;

    /** Message d'erreur en cas d'echec (null si tout va bien). */
    public ?string $error = null;

    public function __construct(string $folder)
    {
        $this->folder = trim($folder, '/');
    }

    /**

     * @param array  $fileEntry  Le tableau $_FILES['key']
     * @param string $basename   Nom de base sans extension (ex: "entrecote-1234567890")
     * @return string|null       Le nom complet (basename.ext) ou null
     */
    public function upload(array $fileEntry, string $basename): ?string
    {
        // Pas de fichier ou erreur upload PHP : on retourne null sans erreur
        // (le caller decide quoi faire — par ex. garder l'image existante).
        if (!isset($fileEntry['error']) || $fileEntry['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($fileEntry['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            $this->error = "Format d'image non supporté (JPG, PNG, WebP uniquement).";
            return null;
        }

        if ($fileEntry['size'] >= self::MAX_SIZE_BYTES) {
            $this->error = "L'image est trop volumineuse (maximum 5 Mo).";
            return null;
        }

        $filename  = $basename . '.' . $ext;
        $uploadDir = $this->resolveUploadDir();

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($fileEntry['tmp_name'], $uploadDir . $filename)) {
            $this->error = "Erreur technique lors du téléchargement de l'image.";
            return null;
        }

        return $filename;
    }

    /**
     * Retourne le chemin filesystem du dossier d'upload, en tenant compte
     * du flag APP_DEV (qui ajoute le prefixe /Miamy/ en local).
     */
    private function resolveUploadDir(): string
    {
        $base = APP_DEV
            ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/'
            : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/';
        return $base . $this->folder . '/';
    }
}
