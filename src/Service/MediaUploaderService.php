<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaUploaderService
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $subFolder = ''): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $uploadDir = $this->getTargetDirectory() . '/' . $subFolder;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $file->move($uploadDir, $newFilename);

        return ($subFolder ? "/$subFolder/" : '/') . $newFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
