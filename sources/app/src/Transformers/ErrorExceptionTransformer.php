<?php declare(strict_types=1);

namespace App\Transformers;

/**
 * Class JsonAPIResponseTransformer
 * @package namespace App\Transformers;
 */
class ErrorExceptionTransformer
{
    /**
     * Transform the errors
     *
     * @param \Exception $e
     * @param bool $with_trace
     * @return array
     */
    public static function transform(\Exception $e, bool $with_trace = false): array
    {
        $exception = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];

        if ($with_trace) $exception['trace'] = $e->getTraceAsString();

        return $exception;
    }
}