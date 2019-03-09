<?php declare(strict_types=1);

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class ImageListProcess
 */
class ImageListProcess
{
    /**
     * @var array
     */
    private $imageParameters;

    /**
     * ImageListProcess constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->imageParameters = $parameterBag->get('image');
    }

    /**
     * @return array
     */
    public function getStoredImageList(): array
    {
        $fileList = [];
        $fileFinder = new Finder();
        $fileFinder->files()->in($this->imageParameters['imageStorePath']);
        foreach ($fileFinder as $file) {
            $fileList[] = sprintf('%s%s', $this->imageParameters['webPath'], $file->getFilename());
        }

        return $fileList;
    }
}
