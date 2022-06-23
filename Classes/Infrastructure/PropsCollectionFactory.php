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
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\SlotPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\StringPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\PropType\UriPropType;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Enum\PseudoEnumInterface;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\PackageKey;
use Sitegeist\Monocle\Domain\Fusion\Prototype;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\EditorFactory;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\Prop;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropName;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollection;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionFactoryInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropsCollectionInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\Props\PropValue;

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

    /**
     * @Flow\Inject
     * @var EditorFactory
     */
    protected $editorFactory;

    public function fromPrototypeForPrototypeDetails(
        Prototype $prototype
    ): PropsCollectionInterface {
        $componentName = ComponentName::fromInput(
            $prototype->getName()->jsonSerialize(),
            new PackageKey('Sitegeist.Monocle')
        );

        $monocleProps = [];
        foreach (Props::fromClassName($componentName->getFullyQualifiedClassName()) as $propName => $propType) {
            switch (get_class($propType)) {
                case BoolPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(false),
                        $this->editorFactory->checkbox()
                    );
                    break;
                case EnumPropType::class:
                    /** @var PseudoEnumInterface[] $cases */
                    $cases = ($propType->getClassName())::cases();
                    $defaultCase = reset($cases);
                    $selectBoxOptions = [];
                    foreach ($cases as $case) {
                        $selectBoxOptions[] = [
                            'value' => $case->getValue(),
                            'label' => $case->getValue()
                        ];
                    }
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny($defaultCase->getValue()),
                        $this->editorFactory->selectBox([
                            'options' => $selectBoxOptions
                        ])
                    );
                    break;
                case FloatPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(84.72),
                        $this->editorFactory->number('float')
                    );
                    break;
                case IntPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny(8472),
                        $this->editorFactory->number('integer')
                    );
                    break;
                case StringPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny('Text'),
                        $this->editorFactory->text()
                    );
                    break;
                case UriPropType::class:
                    $monocleProps[] = new Prop(
                        PropName::fromString($propName),
                        PropValue::fromAny('https://neos.io'),
                        $this->editorFactory->text()
                    );
                    break;
                default:
            }
        }

        return new PropsCollection(...$monocleProps);
    }
}
