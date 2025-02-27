<?php

namespace Mautic\CoreBundle\Tests\Unit\Factory;

use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\PointBundle\Model\TriggerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModelFactoryTest extends TestCase
{
    /**
     * @var MockObject|ContainerInterface
     */
    private \PHPUnit\Framework\MockObject\MockObject $container;

    /**
     * @var ModelFactory<object>
     */
    private \Mautic\CoreBundle\Factory\ModelFactory $factory;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new ModelFactory($this->container);
    }

    public function testModelKeyIsLowerCaseToMatchServiceKeys(): void
    {
        $pointTriggerModel = $this->createMock(TriggerModel::class);
        $modelName         = 'point.triggerEvent';
        $containerKey      = 'mautic.point.model.triggerEvent';

        $this->container->expects($this->once())
            ->method('has')
            ->with($containerKey)
            ->willReturn(true);

        $this->container->expects($this->once())
            ->method('get')
            ->with($containerKey)
            ->willReturn($pointTriggerModel);

        $givenPointTriggerModel = $this->factory->getModel($modelName);

        $this->assertInstanceOf(TriggerModel::class, $givenPointTriggerModel);
    }
}
