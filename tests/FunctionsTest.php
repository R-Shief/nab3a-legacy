<?php

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test json_stream_callback
     */
    public function testJsonStreamCallback()
    {
        $callback = function ($buffer) {
            PHPUnit_Framework_Assert::assertTrue(true, 'Callback was called');
        };
        $streamCallback = line_delimited_stream($callback);

        $this->assertThat($streamCallback, new PHPUnit_Framework_Constraint_IsInstanceOf(Closure::class), 'json_stream_callback returns callable');

        $faker = Faker\Factory::create();
        $streamCallback(PHP_EOL);

        $faker->addProvider(new \Faker\Provider\en_US\Text($faker));
        $streamCallback($faker->realText(100));
    }
}
