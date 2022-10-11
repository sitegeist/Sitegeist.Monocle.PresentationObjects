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

use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\ComponentName;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\IsComponent;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\PropTypeFactory;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\PackageKey;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\SlotInterface;
use Sitegeist\Monocle\Domain\Fusion\Prototype;
use Sitegeist\Monocle\Domain\PrototypeDetails\AnatomyFactory;
use Sitegeist\Monocle\Domain\PrototypeDetails\FusionPrototypeAst;
use Sitegeist\Monocle\Domain\PrototypeDetails\ParsedCode;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\Prop;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollection;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionFactoryInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropValue;
use Sitegeist\Monocle\Domain\PrototypeDetails\PropSets\PropSetCollection;
use Sitegeist\Monocle\Domain\PrototypeDetails\PrototypeDetails;
use Sitegeist\Monocle\Domain\PrototypeDetails\PrototypeDetailsFactoryInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\PrototypeDetailsInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\PrototypeDetailsFactory as DefaultPrototypeDetailsFactory;
use Neos\Flow\Annotations as Flow;
use Sitegeist\Monocle\Domain\PrototypeDetails\RenderedCode;
use Sitegeist\Monocle\Fusion\ReverseFusionParser;
use Symfony\Component\Yaml\Yaml;

class PrototypeDetailsFactory implements PrototypeDetailsFactoryInterface
{
    #[Flow\Inject]
    protected DefaultPrototypeDetailsFactory $defaultPropsCollectionFactory;

    #[Flow\Inject]
    protected AnatomyFactory $anatomyFactory;

    #[Flow\Inject]
    protected PropsCollectionFactoryInterface $propsCollectionFactory;

    #[Flow\Inject]
    protected StyleguideCaseFactoryFactory $styleguideCaseFactoryFactory;

    #[Flow\Inject]
    protected PropSerializationService $propSerializationService;

    #[Flow\Inject]
    protected UseCaseService $useCaseService;

    public function forPrototype(Prototype $prototype): PrototypeDetailsInterface
    {
        $componentName = ComponentName::fromInput(
            $prototype->getName()->jsonSerialize(),
            new PackageKey('Sitegeist.Monocle')
        );

        if (IsComponent::isSatisfiedByClassName($componentName->getFullyQualifiedClassName()) === false) {
            // no presentation object component, fall back to default
            return $this->defaultPropsCollectionFactory->forPrototype($prototype);
        }

        $presentationFactory = $this->styleguideCaseFactoryFactory->forPrototype($prototype);

        return new PrototypeDetails(
            $prototype->getName(),
            RenderedCode::fromString(
                ReverseFusionParser::restorePrototypeCode(
                    (string) $prototype->getName(),
                    $prototype->getAst()
                )
            ),
            ParsedCode::fromString(
                Yaml::dump($prototype->getAst(), 99)
            ),
            FusionPrototypeAst::fromArray($prototype->getAst()),
            $this->anatomyFactory
                ->fromPrototypeForPrototypeDetails($prototype),
            $this->propsCollectionFactory
                ->fromPrototypeForPrototypeDetails($prototype),
            /** @deprecated */ new PropSetCollection(),
            $this->useCaseService->useCaseCollectionFromFactory($presentationFactory)
        );
    }
}
