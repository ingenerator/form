<?php
declare(strict_types=1);

namespace Ingenerator\Form\Validation;

use DateTimeImmutable;
use Exception;
use Ingenerator\PHPUtils\DateTime\DateTimeImmutableFactory;

final class DateValidation
{

    public static function between(mixed $value, string $min, string $max): bool
    {
        // Min and max are from code/config, and must be valid dates already (or should throw)
        $min = DateTimeImmutableFactory::fromStrictFormat($min, 'Y-m-d');
        $max = DateTimeImmutableFactory::fromStrictFormat($max, 'Y-m-d');

        return self::testIfInputIsValidDate(
            $value,
            fn(DateTimeImmutable $dateValue) => $dateValue >= $min && $dateValue <= $max,
        );
    }

    public static function onOrAfter(mixed $value, string $min): bool
    {
        // Min is from code/config, and must be valid date already (or should throw)
        $min = DateTimeImmutableFactory::fromStrictFormat($min, 'Y-m-d');
        return self::testIfInputIsValidDate(
            $value,
            fn(DateTimeImmutable $dateValue) => $dateValue >= $min,
        );
    }

    public static function onOrBefore(mixed $value, string $max): bool
    {
        // Min is from code/config, and must be valid date already (or should throw)
        $max = DateTimeImmutableFactory::fromStrictFormat($max, 'Y-m-d');
        return self::testIfInputIsValidDate(
            $value,
            fn(DateTimeImmutable $dateValue) => $dateValue <= $max,
        );
    }

    /**
     * @param callable(DateTimeImmutable):bool $test
     */
    private static function testIfInputIsValidDate(mixed $value, callable $test): bool
    {
        // Looks odd, but these are expected early returns for invalid input. We want this validator to return
        // *TRUE* if the value isn't a valid Y-m-d date string, because we don't want to tag e.g. a
        // `must be greater than` validation failure on a field where the input is nonsense, that is already handled
        // by other validators.

        if (!is_string($value)) {
            return true;
        }

        try {
            $dateValue = DateTimeImmutableFactory::fromStrictFormat($value, 'Y-m-d');
        } catch (Exception) {
            return true;
        }

        return $test($dateValue);
    }
}
