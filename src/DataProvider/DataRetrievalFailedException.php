<?php

declare(strict_types=1);

namespace App\DataProvider;

use RuntimeException;
use Throwable;

final class DataRetrievalFailedException extends RuntimeException
{
    public function __construct(
        string $dataProviderName,
        $message = 'Data retrieval failed.',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct("{$dataProviderName}: {$message}", $code, $previous);
    }
}
