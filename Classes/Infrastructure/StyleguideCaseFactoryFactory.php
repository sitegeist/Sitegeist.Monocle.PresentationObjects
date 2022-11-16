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
        $caseFactoryClassName = $prototype->evaluate('/__meta/styleguide/__meta/caseFactory');
        if ($caseFactoryClassName === null) {
            throw new \DomainException(
                "Prototype "
                . $prototype->getName()
                . " has no StyleguideCaseFactoryInterface\n"
                . " @styleguide.@caseFactory is null "
                , 1665501456
            );
        }
        return $this->forPrototypeNameAndClassName($prototype->getName()->jsonSerialize(), $caseFactoryClassName);
    }

    public function forPrototypeNameAndClassName(string $prototypeName, string $caseFactoryClassName): StyleguideCaseFactoryInterface
    {
        $caseFactory = $this->objectManager->get($caseFactoryClassName);
        if ($caseFactory instanceof StyleguideCaseFactoryInterface === false) {
            throw new \DomainException(
                "StyleguideCaseFactoryInterface of prototype "
                . $prototypeName
                . "\n"
                . " @styleguide.@caseFactory = "
                . json_encode($caseFactoryClassName)
                . "\n"
                . " must implement "
                . StyleguideCaseFactoryInterface::class
                ,
                1665438019
            );
        }
        return $caseFactory;
    }
}
