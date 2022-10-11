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

use Neos\Eel\ProtectedContextAwareInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\ComponentName;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\Props;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\IsComponent;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\PackageKey;
use Neos\Flow\Annotations as Flow;

class PresentationObjectHelper implements ProtectedContextAwareInterface
{
    #[Flow\Inject]
    protected PropSerializationService $propSerializationService;

    #[Flow\Inject]
    protected UseCaseService $useCaseService;

    #[Flow\Inject]
    protected StyleguideCaseFactoryFactory $styleguideCaseFactoryFactory;

    public function isPresentationObject(string $prototypeName)
    {
        $componentName = ComponentName::fromInput(
            $prototypeName,
            new PackageKey('Foo.Nudelsuppe') // unused
        );
        return IsComponent::isSatisfiedByClassName($componentName->getFullyQualifiedClassName());
    }

    public function createPresentationObject(string $prototypeName, string $presentationFactoryClassName, ?string $useCaseId, array $editedProps)
    {
        $presentationFactory = $this->styleguideCaseFactoryFactory->forPrototypeNameAndClassName($prototypeName, $presentationFactoryClassName);
        $presentationObject = $this->useCaseService->getPresentationObjectFromFactory($presentationFactory, $useCaseId);

        if ($presentationObject->getPrototypeName() !== $prototypeName) {
            throw new \DomainException(
                "Monocle is supposed to render $prototypeName but factory returned {$presentationObject->getPrototypeName()}",
                1665438356
            );
        }

        if (empty($editedProps)) {
            return $presentationObject;
        }

        $unserializedEditedPropsAndUseCaseProps = [];
        foreach (Props::fromClassName($presentationObject::class) as $propName => $propType) {

            if (isset($editedProps[$propName]) === false) {
                $unserializedEditedPropsAndUseCaseProps[$propName] = $presentationObject->{$propName};
                continue;
            }

            $unserializedEditedPropsAndUseCaseProps[$propName] = $this->propSerializationService->unserialize(
                $propName,
                $propType,
                $editedProps[$propName]
            );
        }

        return new ($presentationObject::class)(...$unserializedEditedPropsAndUseCaseProps);
    }

    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
