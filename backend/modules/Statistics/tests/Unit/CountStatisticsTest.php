<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;
use Modules\Statistics\Services\CountStatisticsService;
use Modules\Statistics\Services\StatisticsHandlerInterface;
use Modules\User\Users\Models\User;

class CountStatisticsTest extends TestCase
{
    private CountStatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CountStatisticsService;
    }

    public function test_can_get_statistics_for_one_model_with_handler(): void
    {
        $builderMock = $this->generateBuilder(100);

        $handler = Mockery::mock(StatisticsHandlerInterface::class);

        $id = fake()->text(10);

        $handler
            ->shouldReceive('handle')
            ->andReturn($builderMock);

        $handler
            ->shouldReceive('identifier')
            ->andReturn($id);

        $result = $this->service
            ->for(Product::class)
            ->apply([$handler]);

        $this->assertEquals([
            'product:'.$id => 100,
        ], $result->toArray());
    }

    public function test_can_get_statistics_for_one_model_and_multiple_handlers(): void
    {
        $builderMock = $this->generateBuilder(50);

        $handlers = $this->generateHandlers(3, $builderMock);

        $result = $this->service
            ->for(Product::class)
            ->apply($handlers);

        $expected = [];

        foreach ($handlers as $handler) {
            $key = 'product:'.$handler->identifier();

            $expected[$key] = 50;
        }

        $this->assertEquals($expected, $result->toArray());
    }

    public function test_can_get_statistics_for_multiple_models_and_one_handler(): void
    {
        $builderMock = $this->generateBuilder(80);

        $handler = Mockery::mock(StatisticsHandlerInterface::class);

        $id = fake()->text(10);

        $handler
            ->shouldReceive('handle')
            ->andReturn($builderMock);

        $handler
            ->shouldReceive('identifier')
            ->andReturn($id);

        $result = $this->service
            ->for([Product::class, User::class])
            ->apply([$handler]);

        $this->assertEquals([
            'product:'.$id => 80,
            'user:'.$id => 80,
        ], $result->toArray());
    }

    public function test_can_get_statistics_for_multiple_models_and_multiple_handlers(): void
    {
        $builderMock = $this->generateBuilder(15);

        $handlers = $this->generateHandlers(3, $builderMock);

        $result = $this->service
            ->for([Product::class, User::class])
            ->apply($handlers);

        $expected = [];

        foreach ($handlers as $handler) {
            $key1 = 'product:'.$handler->identifier();

            $key2 = 'user:'.$handler->identifier();

            $expected[$key1] = 15;

            $expected[$key2] = 15;
        }

        $this->assertEquals($expected, $result->toArray());
    }

    public function test_can_get_statistics_for_multiple_models_where_handlers_related_to_their_models(): void
    {
        $builderMock1 = $this->generateBuilder(70);
        $productHandler1 = $this->generateHandlers(1, $builderMock1)[0];

        $builderMock2 = $this->generateBuilder(100);
        $productHandler2 = $this->generateHandlers(1, $builderMock2)[0];

        $builderMock3 = $this->generateBuilder(80);
        $userHandler1 = $this->generateHandlers(1, $builderMock3)[0];

        $builderMock4 = $this->generateBuilder(10);
        $userHandler2 = $this->generateHandlers(1, $builderMock4)[0];

        $result = $this->service
            ->for([Product::class, User::class])
            ->apply([
                Product::class => [
                    $productHandler1,
                    $productHandler2,
                ],
                User::class => [
                    $userHandler1,
                    $userHandler2,
                ],
            ]);

        $expected = [
            'product:'.$productHandler1->identifier() => 70,
            'product:'.$productHandler2->identifier() => 100,
            'user:'.$userHandler1->identifier() => 80,
            'user:'.$userHandler2->identifier() => 10,
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function test_can_get_statistics_for_multiple_models_where_handlers_both_related_and_for_all(): void
    {
        $builderMock1 = $this->generateBuilder(70);
        $productHandler1 = $this->generateHandlers(1, $builderMock1)[0];

        $builderMock3 = $this->generateBuilder(80);
        $userHandler1 = $this->generateHandlers(1, $builderMock3)[0];

        $handlerForAll = $this->generateHandlers(1, $this->generateBuilder(50))[0];

        $result = $this->service
            ->for([Product::class, User::class])
            ->apply([
                $handlerForAll,
                Product::class => [
                    $productHandler1,
                ],
                User::class => [
                    $userHandler1,
                ],
            ]);

        $expected = [
            'product:'.$productHandler1->identifier() => 70,
            'user:'.$userHandler1->identifier() => 80,
            'product:'.$handlerForAll->identifier() => 50,
            'user:'.$handlerForAll->identifier() => 50,
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    private function generateBuilder(int $count)
    {
        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('count')
            ->andReturn($count);

        return $builder;
    }

    private function generateHandlers(int $num, $builder): array
    {
        $result = [];

        for ($i = 0; $i < $num; $i++) {
            $handler = Mockery::mock(StatisticsHandlerInterface::class);

            $handler
                ->shouldReceive('handle')
                ->andReturn($builder);

            $handler
                ->shouldReceive('identifier')
                ->andReturn(fake()->text(10));

            $result[] = $handler;
        }

        return $result;
    }
}
