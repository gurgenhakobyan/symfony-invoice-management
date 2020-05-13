<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private $targetDirectory;
    private $slugger;

    /**
     * FileManager constructor.
     * @param string $targetDirectory
     * @param SluggerInterface $slugger
     */
    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     * @param string|null $format
     * @return array
     */
    public function upload(UploadedFile $file, ?string $format)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $extension = ($format != null ? $format : $file->guessExtension());
        $fileName = $safeFilename.'-'.uniqid().'.'. $extension;

        try {
            $file->move($this->getUploadTargetDirectory(), $fileName);

            $returnArray = [
                'success' => true,
                'message' => 'The file has been successfully uploaded!',
                'originalFileName' => $originalFilename . '.' . $extension,
                'fileName' => $fileName,
                'fileNameFull' => $this->targetDirectory . '/'. $fileName
            ];

        } catch (FileException $e) {
            $returnArray = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $returnArray;
    }

    /**
     * @return string
     */
    public function getUploadTargetDirectory()
    {
        return $this->targetDirectory;
    }
}