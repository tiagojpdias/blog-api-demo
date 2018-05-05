<?php

namespace App\Support;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class ResponseFactory extends \Illuminate\Routing\ResponseFactory
{
    /**
     * JSON API Response.
     *
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonApiSpec($data = [], $status = 200, array $headers = [], $options = 0): JsonResponse
    {
        $headers = array_replace($headers, [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        return $this->json($data, $status, $headers, $options);
    }

    /**
     * JSON API Resource Response.
     *
     * @param Model              $resource
     * @param AbstractSerializer $serializer
     * @param array              $relationships
     * @param int                $status
     * @param array              $headers
     * @param int                $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resource(
        Model $resource,
        AbstractSerializer $serializer,
        array $relationships = [],
        $status = 200,
        array $headers = [],
        $options = 0
    ): JsonResponse {
        $resource = (new Resource($resource, $serializer))->with($relationships);
        $document = new Document($resource);

        return $this->jsonApiSpec($document->toArray(), $status, $headers, $options);
    }

    /**
     * JSON API Collection Response.
     *
     * @param IlluminateCollection $collection
     * @param AbstractSerializer   $serializer
     * @param array                $relationships
     * @param int                  $status
     * @param array                $headers
     * @param int                  $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(
        IlluminateCollection $collection,
        AbstractSerializer $serializer,
        array $relationships = [],
        $status = 200,
        array $headers = [],
        $options = 0
    ): JsonResponse {
        $collection = (new Collection($collection, $serializer))->with($relationships);
        $document = new Document($collection);

        return $this->jsonApiSpec($document->toArray(), $status, $headers, $options);
    }

    /**
     * JSON API Paginator Response.
     *
     * @param LengthAwarePaginator $paginator
     * @param AbstractSerializer   $serializer
     * @param array                $relationships
     * @param int                  $status
     * @param array                $headers
     * @param int                  $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginator(
        LengthAwarePaginator $paginator,
        AbstractSerializer $serializer,
        array $relationships = [],
        $status = 200,
        array $headers = [],
        $options = 0
    ): JsonResponse {
        $collection = (new Collection($paginator, $serializer))->with($relationships);
        $document = new Document($collection);

        $document->setLinks([
            'first' => $paginator->url(1),
            'last'  => $paginator->url($paginator->lastPage()),
            'prev'  => $paginator->previousPageUrl(),
            'next'  => $paginator->nextPageUrl(),
        ]);

        $document->setMeta([
            'per-page' => $paginator->perPage(),
            'total'    => $paginator->total(),
        ]);

        return $this->jsonApiSpec($document->toArray(), $status, $headers, $options);
    }

    /**
     * JSON API Error Response (HTTP Exceptions).
     *
     * @param HttpException $exception
     * @param array         $headers
     * @param int           $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function httpError(HttpException $exception, array $headers = [], $options = 0): JsonResponse
    {
        $errors = [
            'errors' => [
                [
                    'id'     => $exception->getCode(),
                    'detail' => $exception->getMessage() ?: Response::$statusTexts[$exception->getStatusCode()],
                ],
            ],
        ];

        return $this->jsonApiSpec($errors, $exception->getStatusCode(), $headers, $options);
    }

    /**
     * JSON API Error Response (Validation).
     *
     * @param ValidationException $exception
     * @param array               $headers
     * @param int                 $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationError(ValidationException $exception, array $headers = [], $options = 0): JsonResponse
    {
        $errors = [];

        foreach ($exception->errors() as $field => $details) {
            $errors['errors'][] = [
                'id'     => $field,
                'detail' => current($details),
            ];
        }

        return $this->jsonApiSpec($errors, $exception->status, $headers, $options);
    }
}
