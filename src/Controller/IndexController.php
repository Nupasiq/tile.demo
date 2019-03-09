<?php declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use App\Form\Type\ImageFormType;
use App\Services\ImageListProcess;
use App\Services\ImageProcess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param ImageListProcess $process
     *
     * @return Response
     */
    public function indexAction(ImageListProcess $process): Response
    {
        $fileForm = $this->createForm(ImageFormType::class);

        return $this->render('base.html.twig', [
            'fileForm' => $fileForm->createView(),
            'uploadedList' => $process->getStoredImageList(),
        ]);
    }

    /**
     * @Route("/upload/image", name="upload_image")
     *
     * @param Request            $request
     * @param ValidatorInterface $validator
     * @param ImageProcess       $process
     *
     * @return Response
     *
     * @throws \ImagickException
     * @throws ValidationException
     */
    public function imageFormEndpointAction(Request $request, ValidatorInterface $validator, ImageProcess $process): Response
    {
        if ($request->isXmlHttpRequest()) {
            $fileForm = $this->createForm(ImageFormType::class);
            $imageDto = $fileForm->handleRequest($request)->getData();
            /**
             * @var ConstraintViolationList $errors
             */
            $errors = $validator->validate($imageDto);
            if ($errors->count() > 0) {
                throw new ValidationException($errors);
            }
            if ($fileForm->isSubmitted() && $fileForm->isValid()) {
                $process->upload($imageDto);

                return $this->json(['image' => $process->getUri()], Response::HTTP_OK);
            }
        }
    }
}
