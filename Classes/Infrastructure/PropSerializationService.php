<?php

/**
 * This file is part of the Sitegeist.Monocle.PresentationObjects package
 *
 * (c) 2022
 * Bernhard Schmitt <schmitt@sitegeist.de>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Sitegeist\Monocle\PresentationObjects\Infrastructure;

use GuzzleHttp\Psr7\Uri;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\BoolPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\EnumPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\FloatPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\IntPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\PropTypeInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\StringLikePropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\StringPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\UriPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\SlotInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\Value;

class PropSerializationService
{
    public function serialize(string $propName, PropTypeInterface $propType, SlotInterface $component): string|int|float|bool
    {
        switch (get_class($propType)) {
            case BoolPropType::class:
            case FloatPropType::class:
            case IntPropType::class:
            case StringPropType::class:
                return $component->{$propName};
            case EnumPropType::class:
                return $component->{$propName}->value;
            case StringLikePropType::class:
            case UriPropType::class:
                return (string)$component->{$propName};
        }
        throw new \DomainException("Could not serialize property $propName of component " . $component::class);
    }

    public function unserialize(string $propName, PropTypeInterface $propType, mixed $value): mixed
    {
        switch (get_class($propType)) {
            case BoolPropType::class:
            case FloatPropType::class:
            case IntPropType::class:
            case StringPropType::class:
                return $value;
            case EnumPropType::class:
                /** @var \BackedEnum $enum */
                $enum = $propType->className;
                return $enum::from($value);
            case StringLikePropType::class:
                return Value::fromString($value);
            case UriPropType::class:
                return new Uri($value);
            default:
        }
        throw new \DomainException("Could not unserialize property $propName");
    }
}
