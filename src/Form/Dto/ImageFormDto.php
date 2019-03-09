<?php declare(strict_types=1);

namespace App\Form\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageFormDto
 */
class ImageFormDto
{
    /**
     * @var UploadedFile
     *
     * @Assert\Image(
     *     minWidth = 480,
     *     minHeight = 640,
     *     mimeTypes = {"image/jpeg", "image/png"},
     * )
     *
     */
    private $file;

    /**
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return self
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }
}
