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

namespace Sitegeist\Monocle\PresentationObjects\Domain;

use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\SlotInterface;

interface StyleguideCaseFactoryInterface
{
    public function getDefaultCase(): SlotInterface;

    /** @return \Traversable<string,SlotInterface> */
    public function getUseCases(): \Traversable;
}
