<?php

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

declare(strict_types=1);

namespace Sitegeist\Monocle\PresentationObjects\Tests\Unit\Domain;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\Component;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\ComponentName;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\FusionNamespace;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\PackageKey;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\Props;
use PHPUnit\Framework\Assert;
use Sitegeist\Monocle\PresentationObjects\Domain\StyleguideCaseFactoryRenderer;

/**
 * Test cases for the StyleguideCaseFactoryRenderer
 */
class StyleguideCaseFactoryRendererTest extends UnitTestCase
{
    private ?Component $component = null;

    public function setUp(): void
    {
        parent::setUp();

        $componentName = new ComponentName(
            new PackageKey('Vendor.Site'),
            FusionNamespace::default(),
            'MyNewComponent',
        );
        $this->component = new Component(
            $componentName,
            Props::fromInputArray(
                $componentName,
                [
                    'bool:bool',
                    'nullableBool:?bool',
                    'float:float',
                    'nullableFloat:?float',
                    'int:int',
                    'nullableInt:?int',
                    'string:string',
                    'nullableString:?string',
                    'uri:Uri',
                    'nullableUri:?Uri',
                    'image:ImageSource',
                    'nullableImage:?ImageSource',
                    'subComponent:MyComponent',
                    'nullableSubComponent:?MyComponent',
                    'componentArray:array<MyComponent>',
                    'enum:MyStringEnum',
                    'nullableEnum:?MyStringEnum',
                    'slot:slot',
                    'nullableSlot:?slot',
                ]
            )
        );
    }

    public function testGetFactoryContent(): void
    {
        Assert::assertSame(
            '<?php

/*
 * This file is part of the Vendor.Site package.
 */

declare(strict_types=1);

namespace Vendor\Site\Presentation\Component\MyNewComponent;

use PackageFactory\AtomicFusion\PresentationObjects\Fusion\AbstractComponentPresentationObjectFactory;
use Sitegeist\Monocle\PresentationObjects\Domain\StyleguideCaseFactoryInterface;
use GuzzleHttp\Psr7\Uri;
use Sitegeist\Kaleidoscope\Domain\DummyImageSource;
use Vendor\Site\Presentation\Component\MyComponent\MyComponent;
use Vendor\Site\Presentation\Component\AnotherComponent\AnotherComponent;
use Vendor\Site\Presentation\Component\MyComponent\MyComponents;
use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\Value;

final class MyNewComponentFactory extends AbstractComponentPresentationObjectFactory implements StyleguideCaseFactoryInterface
{
    public function getDefaultCase(): MyNewComponent
    {
        return new MyNewComponent(
            true,
            true,
            47.11,
            47.11,
            4711,
            4711,
            \'Text\',
            \'Text\',
            new Uri(\'https://neos.io\'),
            new Uri(\'https://neos.io\'),
            new DummyImageSource(
                (string)$this->uriService->getDummyImageBaseUri(),
                null,
                null,
                1920,
                1080
            ),
            new DummyImageSource(
                (string)$this->uriService->getDummyImageBaseUri(),
                null,
                null,
                1920,
                1080
            ),
            new MyComponent(
                \'Text\',
                new AnotherComponent(
                    4711
                )
            ),
            new MyComponent(
                \'Text\',
                new AnotherComponent(
                    4711
                )
            ),
            new MyComponents(
                new MyComponent(
                    \'Text\',
                    new AnotherComponent(
                        4711
                    )
                ),
                new MyComponent(
                    \'Text\',
                    new AnotherComponent(
                        4711
                    )
                )
            ),
            MyStringEnum::VALUE_MY_VALUE,
            MyStringEnum::VALUE_MY_VALUE,
            Value::fromString(\'[Enter slot value]\'),
            Value::fromString(\'[Enter slot value]\')
        );
    }

    /**
     * @return \Traversable<int|string,MyNewComponent>
     */
    public function getUseCases(): \Traversable
    {
    }
}
',
            (new StyleguideCaseFactoryRenderer())->renderFactoryContent($this->component)
        );
    }
}
