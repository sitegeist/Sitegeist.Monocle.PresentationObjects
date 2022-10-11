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

use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Annotations as Flow;
use Sitegeist\Monocle\Domain\Fusion\Prototype;
use Sitegeist\Monocle\PresentationObjects\Domain\StyleguideCaseFactoryInterface;

class StyleguideCaseFactoryFactory
{
    #[Flow\Inject]
    protected ObjectManagerInterface $objectManager;

    public function forPrototype(Prototype $prototype): StyleguideCaseFactoryInterface
    {
        $presentationFactoryClassName = $prototype->evaluate('/__meta/styleguide/__meta/presentationFactory');
        if ($presentationFactoryClassName === null) {
            throw new \DomainException(
                "Prototype "
                . $prototype->getName()
                . " has no StyleguideCaseFactoryInterface\n"
                . " @styleguide.@presentationFactory is null "
                , 1665501456
            );
        }
        return $this->forPrototypeNameAndClassName($prototype->getName()->jsonSerialize(), $presentationFactoryClassName);
    }

    public function forPrototypeNameAndClassName(string $prototypeName, string $presentationFactoryClassName): StyleguideCaseFactoryInterface
    {
        $presentationFactory = $this->objectManager->get($presentationFactoryClassName);
        if ($presentationFactory instanceof StyleguideCaseFactoryInterface === false) {
            throw new \DomainException(
                "StyleguideCaseFactoryInterface of prototype "
                . $prototypeName
                . "\n"
                . " @styleguide.@presentationFactory = "
                . json_encode($presentationFactoryClassName)
                . "\n"
                . " must implement "
                . StyleguideCaseFactoryInterface::class
                ,
                1665438019
            );
        }
        return $presentationFactory;
    }
}
