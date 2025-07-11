<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    public function __construct(
        private ParameterBagInterface $params
    ) {}

    public function square(UploadedFile $picture, ?string $folder = '', ?int $width = 50, ?string $oldFile = null): string
    {


        // New name for the picture
        $file = md5(uniqid(rand(), true)) . '.webp';

        // Get picture info
        $pictureInfos = getimagesize($picture);

        if ($pictureInfos === false) {
            throw new Exception('Format d\'image incorrect');
        }

        // Verify type mime
        switch ($pictureInfos['mime']) {
            case 'image/png':
                $sourcePicture = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $sourcePicture = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $sourcePicture = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image incorrect');
        }

        // Resize picture
        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = ($imageHeight - $imageWidth) / 2;
                break;

            case 0: // square
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = 0;
                break;

            case 1: // landscape
                $squareSize = $imageHeight;
                $srcX = ($imageWidth - $imageHeight) / 2;
                $srcY = 0;
                break;
        }

        // Create new blank picture
        $resizedPicture = imagecreatetruecolor($width, $width);

        // Generate picture
        imagecopyresampled($resizedPicture, $sourcePicture, 0, 0, $srcX, $srcY, $width, $width, $squareSize, $squareSize);

        // Create folder path
        $path = $this->params->get('uploads_directory') . $folder;


        // if folder don't exist
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        // if file already exist we can delete it
        if ($oldFile && file_exists($path . '/' . $oldFile)) {
            unlink($path . '/' . $oldFile); // Delete
        }

        // Save the resize picture
        imagewebp($resizedPicture, $path . '/mini/' . $width . 'x' . $width . '-' . $file);


        // save the original picture
        $picture->move($path . '/', $file);



        return $file;
    }
}
