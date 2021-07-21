<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha\Exceptions;

/**
 * Thrown if a value is not of the expected type.
 */
class UnexpectedValueException extends \UnexpectedValueException implements ExceptionInterface
{
}
