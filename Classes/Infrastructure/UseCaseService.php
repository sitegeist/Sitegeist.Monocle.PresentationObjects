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

use PackageFactory\AtomicFusion\PresentationObjects\Presentation\Slot\SlotInterface;
use Sitegeist\Monocle\Domain\PrototypeDetails\UseCases\UseCase;
use Sitegeist\Monocle\Domain\PrototypeDetails\UseCases\UseCaseCollection;
use Sitegeist\Monocle\Domain\PrototypeDetails\UseCases\UseCaseName;
use Sitegeist\Monocle\Domain\PrototypeDetails\UseCases\UseCaseTitle;
use Sitegeist\Monocle\PresentationObjects\Domain\StyleguideCaseFactoryInterface;

class UseCaseService
{
    public function getPresentationObjectFromFactory(StyleguideCaseFactoryInterface $presentationFactory, ?string $useCaseId): SlotInterface
    {
        if (!$useCaseId) {
            return $presentationFactory->getDefaultCase();
        }
        foreach ($this->getUseCasesFromPresentationFactory($presentationFactory) as $key => $presentationObject) {
            if ($useCaseId === $key) {
                return $presentationObject;
            }
        }
        throw new \DomainException("Key $useCaseId does not exist in " . $presentationFactory::class, 1665438299);
    }

    public function useCaseCollectionFromFactory(StyleguideCaseFactoryInterface $presentationFactory): UseCaseCollection
    {
        $useCaseCollection = [];
        foreach ($this->getUseCasesFromPresentationFactory($presentationFactory) as $key => $component) {
            $useCaseCollection[] = new UseCase(
                UseCaseName::fromString($key),
                UseCaseTitle::fromString($key),
                [] // todo currently unused so no effort https://github.com/sitegeist/Sitegeist.Monocle/issues/189
            );
        }
        return new UseCaseCollection(...$useCaseCollection);
    }

    private function getUseCasesFromPresentationFactory($presentationFactory)
    {
        $existingKeys = [];
        foreach ($presentationFactory->getUseCases() as $key => $presentationObject) {
            $keyAsStringAndNonZeroIndexed = is_numeric($key) ? (string)($key + 1) : $key;
            if (in_array($keyAsStringAndNonZeroIndexed, $existingKeys, true)) {
                throw new \DomainException("Key $keyAsStringAndNonZeroIndexed already exists.");
            }
            $existingKeys[] = $keyAsStringAndNonZeroIndexed;
            yield $keyAsStringAndNonZeroIndexed => $presentationObject;
        }
    }
}
