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

use Neos\Flow\Annotations as Flow;
use Sitegeist\Monocle\Domain\Fusion\Prototype;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionFactoryInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionInterface;

/**
 * @Flow\Scope("singleton")
 */
final class PropsCollectionFactory implements PropsCollectionFactoryInterface
{
    /**
     * @Flow\InjectConfiguration(path="fusionContextName")
     * @var string
     */
    protected $fusionContextName;

    public function fromPrototypeForPrototypeDetails(
        Prototype $prototype
    ): PropsCollectionInterface {
        \Neos\Flow\var_dump($prototype);
        exit();
    }
}
