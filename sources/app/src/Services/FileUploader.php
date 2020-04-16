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
     * Target public directory
     *
     * @prop $targetDirectory
     */
    private $targetPublicDirectoryPath;

    /**
     * Initiate properties
     *
     * @param $targetDirectory
     * @param $targetPublicDirectoryPath
     */
    public function __construct($targetDirectory, $targetPublicDirectoryPath)
    {
        $this->targetDirectory = $targetDirectory;
        $this->targetPublicDirectoryPath = $targetPublicDirectoryPath;
    }

    /**
     * Upload file return filepath
     *
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $fileName = sprintf('%s.%s', md5(uniqid()), $file->guessExtension());
        $target_directory = $this->getTargetDirectory();
        $file_path = sprintf('%s/%s', $this->getTargetPublicDirectory(), $fileName);

        try {
            $file->move($target_directory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw $e;
        }

        return $file_path;
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

    /**
     * Get target directory
     *
     * @return string
     */
    public function getTargetPublicDirectory()
    {
        return $this->targetPublicDirectoryPath;
    }
}
