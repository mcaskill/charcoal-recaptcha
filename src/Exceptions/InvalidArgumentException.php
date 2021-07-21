<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha\Exceptions;

/**
 * Thrown if an argument is not of the expected type.
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
