<?php

declare(strict_types=1);

namespace App\Tests\Messenger;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final class BusConfigurationTest extends KernelTestCase
{
    public function testCommandBusThrowsWithoutAHandler(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('command.bus');

        $this->expectException(NoHandlerForMessageException::class);

        $commandBus->dispatch(new class {});
    }

    public function testEventBusAllowsNoHandlers(): void
    {
        self::bootKernel();
        $eventBus = self::getContainer()->get('event.bus');

        $envelope = $eventBus->dispatch(new class {});

        self::assertInstanceOf(\Symfony\Component\Messenger\Envelope::class, $envelope);
    }

    public function testBusesAreDistinctServices(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertInstanceOf(MessageBusInterface::class, $container->get('command.bus'));
        self::assertInstanceOf(MessageBusInterface::class, $container->get('event.bus'));
        self::assertNotSame($container->get('command.bus'), $container->get('event.bus'));
    }
}
