<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Transformation\Argument;

use Cloudinary\Transformation\ExpressionQualifierMultiValue;
use Cloudinary\Transformation\RotationDegreeInterface;

/**
 * Defines how to rotate an image.
 *
 * **Learn more**: <a
 * href="https://cloudinary.com/documentation/effects_and_artistic_enhancements#rotating_images" target="_blank">
 * Rotate an image</a>
 *
 * @api
 */
class Degree extends ExpressionQualifierMultiValue implements RotationDegreeInterface
{
    protected const VALUE_DELIMITER = '.';

    use AngleTrait;

    public const DEG_90  = '90';
    public const DEG_180 = '180';
    public const DEG_270 = '270';

    /**
     * Creates the instance.
     *
     * @param int|array $degree Given degrees or mode.
     *
     *
     * @internal
     */
    public static function createWithDegree(...$degree): static
    {
        return new static(...$degree);
    }
}
