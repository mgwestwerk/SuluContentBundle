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

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Infrastructure\Sulu\SmartContent\DataItem;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentRichEntityInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\WorkflowInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\SmartContent\DataItem\ContentDataItem;

class ContentDataItemTest extends TestCase
{
    /**
     * @param mixed[] $data
     */
    protected function getContentDataItem(
        DimensionContentInterface $dimensionContent,
        array $data
    ): ContentDataItem {
        return new ContentDataItem($dimensionContent, $data);
    }

    public function testGetId(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertSame('123-123', $dataItem->getId());
    }

    public function testGetTitle(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());

        $data = [
            'title' => 'test-title-1',
            'name' => 'test-name-1',
        ];

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), $data);

        $this->assertSame('test-title-1', $dataItem->getTitle());
    }

    public function testGetNameAsTitle(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());

        $data = [
            'title' => null,
            'name' => 'test-name-1',
        ];

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), $data);

        $this->assertSame('test-name-1', $dataItem->getTitle());
    }

    public function testGetImage(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertNull($dataItem->getImage());
    }

    public function testGetPublished(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');

        $published = new \DateTimeImmutable();

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->willImplement(WorkflowInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());
        $dimensionContent->getWorkflowPublished()->willReturn($published);

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertSame($published, $dataItem->getPublished());
    }

    public function testGetPublishedLocaleNull(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn(null);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertNull($dataItem->getPublished());
    }

    public function testGetPublishedNoWorkflow(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertNull($dataItem->getPublished());
    }

    public function testGetPublishedState(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');
        $dimension->getStage()->willReturn(DimensionInterface::STAGE_DRAFT);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->willImplement(WorkflowInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());
        $dimensionContent->getWorkflowPlace()->willReturn(WorkflowInterface::WORKFLOW_PLACE_PUBLISHED);

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertTrue($dataItem->getPublishedState());
    }

    public function testGetPublishedStateLocaleNull(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn(null);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertFalse($dataItem->getPublishedState());
    }

    public function testGetPublishedStateStageLive(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');
        $dimension->getStage()->willReturn(DimensionInterface::STAGE_LIVE);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertTrue($dataItem->getPublishedState());
    }

    public function testGetPublishedStateNoWorkflow(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');
        $dimension->getStage()->willReturn(DimensionInterface::STAGE_DRAFT);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertTrue($dataItem->getPublishedState());
    }

    public function testGetPublishedStateUnpublished(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');
        $dimension->getStage()->willReturn(DimensionInterface::STAGE_DRAFT);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->willImplement(WorkflowInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());
        $dimensionContent->getWorkflowPlace()->willReturn(WorkflowInterface::WORKFLOW_PLACE_UNPUBLISHED);

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertFalse($dataItem->getPublishedState());
    }

    public function testGetPublishedStateDraft(): void
    {
        $resource = $this->prophesize(ContentRichEntityInterface::class);
        $resource->getId()->willReturn('123-123');

        $dimension = $this->prophesize(DimensionInterface::class);
        $dimension->getLocale()->willReturn('en');
        $dimension->getStage()->willReturn(DimensionInterface::STAGE_DRAFT);

        $dimensionContent = $this->prophesize(DimensionContentInterface::class);
        $dimensionContent->willImplement(WorkflowInterface::class);
        $dimensionContent->getResource()->willReturn($resource->reveal());
        $dimensionContent->getDimension()->willReturn($dimension->reveal());
        $dimensionContent->getWorkflowPlace()->willReturn(WorkflowInterface::WORKFLOW_PLACE_DRAFT);

        $dataItem = $this->getContentDataItem($dimensionContent->reveal(), []);

        $this->assertFalse($dataItem->getPublishedState());
    }
}
