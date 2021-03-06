<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Application\DimensionCollectionFactory;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sulu\Bundle\ContentBundle\Content\Application\DimensionCollectionFactory\DimensionCollectionFactory;
use Sulu\Bundle\ContentBundle\Content\Domain\Factory\DimensionCollectionFactoryInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\Dimension;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionCollection;
use Sulu\Bundle\ContentBundle\Content\Domain\Repository\DimensionRepositoryInterface;

class DimensionCollectionFactoryTest extends TestCase
{
    protected function getDimensionFactoryInstance(
        DimensionRepositoryInterface $dimensionRepository
    ): DimensionCollectionFactoryInterface {
        return new DimensionCollectionFactory($dimensionRepository);
    }

    public function testCreateNotExist(): void
    {
        $localizedAttributes = [
            'locale' => 'de',
            'stage' => 'draft',
        ];

        $unlocalizedAttributes = [
            'locale' => null,
            'stage' => 'draft',
        ];

        $dimensionRepository = $this->prophesize(DimensionRepositoryInterface::class);
        $dimensionRepository->findByAttributes($localizedAttributes)->willReturn(
            new DimensionCollection($localizedAttributes, [])
        )->shouldBeCalled();

        $dimensionRepository->create(null, $localizedAttributes)->willReturn(
            $localizedDimension = new Dimension(null, $localizedAttributes)
        )->shouldBeCalled();

        $dimensionRepository->create(null, $unlocalizedAttributes)->willReturn(
            $unlocalizedDimension = new Dimension(null, $unlocalizedAttributes)
        )->shouldBeCalled();

        $dimensionRepository->add($localizedDimension)->shouldBeCalled();
        $dimensionRepository->add($unlocalizedDimension)->shouldBeCalled();

        $dimensionCollectionFactory = $this->getDimensionFactoryInstance($dimensionRepository->reveal());

        $dimensionCollectionFactory->create(['locale' => 'de', 'stage' => 'draft']);
    }

    public function testCreateExist(): void
    {
        $localizedAttributes = [
            'locale' => 'de',
            'stage' => 'draft',
        ];

        $unlocalizedAttributes = [
            'locale' => null,
            'stage' => 'draft',
        ];

        $localizedDimension = new Dimension(null, $localizedAttributes);
        $unlocalizedDimension = new Dimension(null, $unlocalizedAttributes);

        $dimensionRepository = $this->prophesize(DimensionRepositoryInterface::class);
        $dimensionRepository->findByAttributes($localizedAttributes)->willReturn(
            new DimensionCollection($localizedAttributes, [
                $unlocalizedDimension,
                $localizedDimension,
            ])
        )->shouldBeCalled();

        $dimensionRepository->create(Argument::any())->willReturn()->shouldNotBeCalled();
        $dimensionRepository->add(Argument::any())->shouldNotBeCalled();

        $dimensionCollectionFactory = $this->getDimensionFactoryInstance($dimensionRepository->reveal());

        $dimensionCollectionFactory->create($localizedAttributes);
    }

    public function testCreateUnlocalizedNotExist(): void
    {
        $unlocalizedAttributes = [
            'locale' => null,
            'stage' => 'draft',
        ];

        $dimensionRepository = $this->prophesize(DimensionRepositoryInterface::class);
        $dimensionRepository->findByAttributes($unlocalizedAttributes)->willReturn(
            new DimensionCollection($unlocalizedAttributes, [])
        )->shouldBeCalled();

        $dimensionRepository->create(null, $unlocalizedAttributes)->willReturn(
            $unlocalizedDimension = new Dimension(null, $unlocalizedAttributes)
        )->shouldBeCalled();

        $dimensionRepository->add($unlocalizedDimension)->shouldBeCalled();

        $dimensionCollectionFactory = $this->getDimensionFactoryInstance($dimensionRepository->reveal());

        $dimensionCollectionFactory->create($unlocalizedAttributes);
    }

    public function testCreateUnlocalizedExist(): void
    {
        $unlocalizedAttributes = [
            'locale' => null,
            'stage' => 'draft',
        ];

        $unlocalizedDimension = new Dimension(null, $unlocalizedAttributes);

        $dimensionRepository = $this->prophesize(DimensionRepositoryInterface::class);
        $dimensionRepository->findByAttributes($unlocalizedAttributes)->willReturn(
            new DimensionCollection($unlocalizedAttributes, [$unlocalizedDimension])
        )->shouldBeCalled();

        $dimensionRepository->create(Argument::any())->willReturn()->shouldNotBeCalled();
        $dimensionRepository->add(Argument::any())->shouldNotBeCalled();

        $dimensionCollectionFactory = $this->getDimensionFactoryInstance($dimensionRepository->reveal());

        $dimensionCollectionFactory->create($unlocalizedAttributes);
    }
}
