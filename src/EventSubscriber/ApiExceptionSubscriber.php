<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$this->isApiRequest($event)) {
            return;
        }

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse($this->createValidationResponse($exception));

            return;
        }

        if ($exception->getPrevious() !== null && $exception->getPrevious() instanceof ValidationFailedException) {
            $event->setResponse($this->createValidationResponse($exception->getPrevious()));

            return;
        }

        if ($exception instanceof UniqueConstraintViolationException) {
            $event->setResponse($this->createErrorResponse(
                message: 'Resource already exists.',
                statusCode: Response::HTTP_CONFLICT,
            ));

            return;
        }

        if ($exception instanceof AccessDeniedException) {
            $event->setResponse($this->createErrorResponse(
                message: 'Access denied.',
                statusCode: Response::HTTP_FORBIDDEN,
            ));

            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse($this->createErrorResponse(
                message: $this->getHttpExceptionMessage($exception),
                statusCode: $exception->getStatusCode(),
            ));

            return;
        }

        $event->setResponse($this->createErrorResponse(
            message: 'Internal server error.',
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        ));
    }

    private function isApiRequest(ExceptionEvent $event): bool
    {
        return str_starts_with($event->getRequest()->getPathInfo(), '/v1/api');
    }

    private function createValidationResponse(ValidationFailedException $exception): JsonResponse
    {
        $errors = [];

        foreach ($exception->getViolations() as $violation) {
            $propertyPath = $violation->getPropertyPath();

            if ($propertyPath === '') {
                $propertyPath = 'general';
            }

            $errors[$propertyPath][] = $violation->getMessage();
        }

        return new JsonResponse(
            [
                'message' => 'Validation failed.',
                'errors' => $errors,
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    private function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => $message,
            ],
            $statusCode,
        );
    }

    private function getHttpExceptionMessage(HttpExceptionInterface $exception): string
    {
        $message = $exception->getMessage();

        if ($message !== '') {
            return $message;
        }

        return match ($exception->getStatusCode()) {
            Response::HTTP_BAD_REQUEST => 'Bad request.',
            Response::HTTP_UNAUTHORIZED => 'Unauthorized.',
            Response::HTTP_FORBIDDEN => 'Access denied.',
            Response::HTTP_NOT_FOUND => 'Resource not found.',
            Response::HTTP_METHOD_NOT_ALLOWED => 'Method not allowed.',
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported media type.',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Validation failed.',
            default => 'Request error.',
        };
    }
}
