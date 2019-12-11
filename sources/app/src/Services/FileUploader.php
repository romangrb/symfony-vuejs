<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 26.11.2019
 * Time: 0:48
 */
namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * Target directory
     *
     * @prop $targetDirectory
     */
    private $targetDirectory;

    /**
     * Initiate properties
     *
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Upload file return filename
     *
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw $e;
        }

        return $fileName;
    }

    /**
     * Get target directory
     *
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
