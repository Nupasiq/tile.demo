<?php declare(strict_types=1);

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\ValidationException;

/**
 * Class ExceptionSubscriber
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => [['onThrowable']]];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onThrowable(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if ($exception instanceof ValidationException) {
            /**
             * @var ValidationException $exception
             */
            $errors = $exception->getErrors();

            $data = [
                'errors' => $errors,
                'message' => $exception->getMessage(),
                'code' => Response::HTTP_FORBIDDEN,
            ];

            $response = new JsonResponse($data, Response::HTTP_OK);

            $event->setResponse($response);
            $event->allowCustomResponseCode();
            $event->stopPropagation();
        }
    }
}
