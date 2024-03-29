<?php

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

declare(strict_types=1);

namespace Sitegeist\Monocle\PresentationObjects\Domain;

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\Component\Component;
use PackageFactory\AtomicFusion\PresentationObjects\Domain\FactoryRendererInterface;

/**
 * The dummy presentation object factory renderer
 */
#[Flow\Proxy(false)]
final class StyleguideCaseFactoryRenderer implements FactoryRendererInterface
{
    public function renderFactoryContent(Component $component): string
    {
        return '<?php

' . $component->name->renderClassComment() . '

declare(strict_types=1);

namespace ' . $component->name->getPhpNamespace() . ';

use PackageFactory\AtomicFusion\PresentationObjects\Fusion\AbstractComponentPresentationObjectFactory;

final class ' . $component->name->getSimpleFactoryName() . ' extends AbstractComponentPresentationObjectFactory
{
}
';
    }
}
