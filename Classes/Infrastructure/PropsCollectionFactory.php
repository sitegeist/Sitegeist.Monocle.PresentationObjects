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
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\ComponentName;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\Props;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\BoolPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\EnumPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\FloatPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\IntPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\IsComponent;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\StringLikePropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\StringPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\UriPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\PackageKey;
use Sitegeist\Monocle\Domain\Fusion\Prototype;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\EditorFactory;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\Prop;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropName;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollection;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionFactory as DefaultPropsCollectionFactory;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionFactoryInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropValue;

#[Flow\Scope('singleton')]
final class PropsCollectionFactory implements PropsCollectionFactoryInterface
{
    #[Flow\Inject]
    protected EditorFactory $editorFactory;

    #[Flow\Inject]
    protected DefaultPropsCollectionFactory $defaultPropsCollectionFactory;

    #[Flow\Inject]
    protected StyleguideCaseFactoryFactory $styleguideCaseFactoryFactory;

    #[Flow\Inject]
    protected PropSerializationService $propSerializationService;

    public function fromPrototypeForPrototypeDetails(
        Prototype $prototype
    ): PropsCollectionInterface {
        $componentName = ComponentName::fromInput(
            $prototype->getName()->jsonSerialize(),
            new PackageKey('Sitegeist.Monocle')
        );

        if (IsComponent::isSatisfiedByClassName($componentName->getFullyQualifiedClassName()) === false) {
            // no presentation object component, fall back to default
            return $this->defaultPropsCollectionFactory->fromPrototypeForPrototypeDetails($prototype);
        }

        $caseFactory = $this->styleguideCaseFactoryFactory->forPrototype($prototype);
        $defaultComponent = $caseFactory->getDefaultCase();

        $monocleProps = [];
        foreach (Props::fromClassName($componentName->getFullyQualifiedClassName()) as $propName => $propType) {
            switch (get_class($propType)) {
                case BoolPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(
                            $this->propSerializationService->serialize($propName, $propType, $defaultComponent)
                        ),
                        $this->editorFactory->checkbox()
                    );
                    break;
                case EnumPropType::class:
                    /** @var \BackedEnum[] $cases */
                    $cases = $propType->className::cases();
                    $selectBoxOptions = [];
                    foreach ($cases as $case) {
                        $selectBoxOptions[] = [
                            'value' => $case->value,
                            'label' => $case->value
                        ];
                    }
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(
                            $this->propSerializationService->serialize($propName, $propType, $defaultComponent)
                        ),
                        $this->editorFactory->selectBox([
                            'options' => $selectBoxOptions
                        ])
                    );
                    break;
                case FloatPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(
                            $this->propSerializationService->serialize($propName, $propType, $defaultComponent)
                        ),
                        $this->editorFactory->number('float')
                    );
                    break;
                case IntPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(
                            $this->propSerializationService->serialize($propName, $propType, $defaultComponent)
                        ),
                        $this->editorFactory->number('integer')
                    );
                    break;
                case StringLikePropType::class:
                case StringPropType::class:
                case UriPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(
                            $this->propSerializationService->serialize($propName, $propType, $defaultComponent)
                        ),
                        $this->editorFactory->text()
                    );
                    break;
                default:
            }
        }
        return new PropsCollection(...$monocleProps);
    }
}
