<?php

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use Vistik\Checks\Application\Response500Check;
use Vistik\Metrics\Metrics;

class Response500CheckTest extends TestCase
{

    /**
     * @test
     * @group checks
     *
     */
    public function health_check()
    {
        // Given
        $check = new Response500Check(1.00);
        $this->assertTrue($check->run());

        // When
        $mock = Mockery::mock(Response::class);
        $mock->shouldReceive('getStatusCode')->andReturn(500);
        Metrics::addData($mock);

        $mock = Mockery::mock(Response::class);
        $mock->shouldReceive('getStatusCode')->andReturn(200);
        Metrics::addData($mock);

        // Then
        $this->assertFalse($check->run());
        $this->assertEquals(50, Metrics::getRatio(500));
    }
}