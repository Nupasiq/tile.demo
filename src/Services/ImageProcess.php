<?php declare(strict_types=1);

namespace App\Services;

use App\Form\Dto\ImageFormDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageProcess
 */
class ImageProcess
{
    /**
     * @var UploadedFile
     */
    private $file;
    /**
     * @var array
     */
    private $imageParameters;

    /**
     * @var \Imagick
     */
    private $iMagic;

    /**
     * ImageProcess constructor.
     * @param ParameterBagInterface $parameterBag
     *
     * @throws \ImagickException
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->imageParameters = $parameterBag->get('image');
        $this->iMagic = new \Imagick();
    }

    /**
     * @param ImageFormDto $dto
     *
     * @throws \ImagickException
     */
    public function upload(ImageFormDto $dto): void
    {
        $this->file = $dto->getFile();
        $this->resize();
        $this->moveImage();
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return sprintf('%s%s', $this->imageParameters['webPath'], $this->file->getFilename());
    }

    /**
     * @throws \ImagickException
     */
    private function resize(): void
    {
        $this->iMagic->readImage($this->file->getRealPath());
        $this->iMagic->cropThumbnailImage(
            $this->imageParameters['resizeHeight'],
            $this->imageParameters['resizeWidth'],
            false
        );
        $this->iMagic->annotateImage($this->getDraw(), 10, 20, 0, $this->imageParameters['waterMark']);
        $this->iMagic->writeImage($this->file->getRealPath());
    }

    /**
     * @return \ImagickDraw
     */
    private function getDraw(): \ImagickDraw
    {
        $draw = new \ImagickDraw();
        $draw->setFillColor('blue');
        $draw->setFont('Font/ArialRegular.ttf');
        $draw->setFontSize(20);

        return $draw;
    }

    /**
     * @throws \Exception
     */
    private function moveImage()
    {
        $fileName = sprintf('%s.%s', bin2hex(random_bytes(20)), $this->file->guessExtension());
        $this->file = $this->file->move($this->imageParameters['imageStorePath'], $fileName);
    }
}
