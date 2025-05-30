<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Transformation;

use Cloudinary\Transformation\Expression\ExpressionUtils;
use Cloudinary\Transformation\Qualifier\BaseQualifier;

/**
 * Class BaseOffsetValueQualifier
 */
abstract class BaseOffsetQualifier extends BaseQualifier
{
    protected const RANGE_VALUE_RE = /** @lang PhpRegExp */
        '/^(?P<value>(\d+\.)?\d+)(?P<modifier>[%pP])?$/';

    /**
     * @var bool Indicates whether to allow value 'auto'.
     */
    protected static bool $allowAuto = false;

    /**
     * Normalizes a range value.
     *
     * @param string|float|mixed $value The value to normalize.
     *
     * @return string|null The normalized value.
     */
    private static function normRangeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Ensure that trailing decimal(.0) part is not cropped when float is provided
        // e.g. float 1.0 should be returned as "1.0" and not "1" as it happens by default
        if (is_float($value) && $value - (int)$value == 0) {
            $value = sprintf('%.1f', $value);
        }

        preg_match(self::RANGE_VALUE_RE, $value, $matches);

        if (empty($matches)) {
            return ExpressionUtils::normalize($value);
        }

        $modifier = '';
        if (! empty($matches['modifier'])) {
            $modifier = 'p';
        }

        return $matches['value'] . $modifier;
    }

    /**
     * Normalized range value that allows 'auto' value
     *
     * @param string|float|mixed $value The value to normalize
     *
     * @return string|mixed The normalized value.
     *
     * @uses         self::normRangeValue()
     *
     * @noinspection TypeUnsafeComparisonInspection
     */
    private static function normAutoRangeValue(mixed $value): mixed
    {
        if ($value == 'auto') {
            return $value;
        }

        return self::normRangeValue($value);
    }

    /**
     * Serializes to string.
     *
     * @return string
     */
    public function __toString()
    {
        if (static::$allowAuto === false) {
            $value = self::normRangeValue($this->value);
        } else {
            $value = self::normAutoRangeValue($this->value);
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return (string)$value == '' ? '' : self::getKey() . static::KEY_VALUE_DELIMITER . $value;
    }
}
