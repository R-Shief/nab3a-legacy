<?php

namespace Nab3aBundle\Tests\Twitter;

use Doctrine\Common\Annotations\AnnotationReader;
use Faker\Factory;
use Nab3aBundle\Twitter\StreamParameters;
use Symfony\Component\Validator\ValidatorBuilder;

class StreamParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorBuilder
     */
    private $builder;

    protected function setUp()
    {
        $reader = new AnnotationReader();
        $this->builder = new ValidatorBuilder();
        $this->builder->enableAnnotationMapping($reader);
    }

    public function testFollow()
    {
        $param = new StreamParameters();
        $validator = $this->builder->getValidator();

        $follow = range(1, 5000);
        $param->setFollow($follow);
        $this->assertEquals($follow, $param->getFollow());
        $result = $validator->validate($param);
        $this->assertEquals(0, $result->count());

        $param->setFollow(range(1, 5001));
        $result = $validator->validate($param);
        $this->assertEquals(1, $result->count());
    }

    public function testTrack()
    {
        $param = new StreamParameters();
        $validator = $this->builder->getValidator();
        $faker = Factory::create();

        $track = $faker->words(400);
        $param->setTrack($track);
        $this->assertEquals($track, $param->getTrack());

        $result = $validator->validate($param);
        $this->assertEquals(0, $result->count());

        $track = $faker->words(401);
        $param->setTrack($track);
        $result = $validator->validate($param);
        $this->assertEquals(1, $result->count());
    }

    public function testLocations()
    {
        $param = new StreamParameters();
        $validator = $this->builder->getValidator();
        $faker = Factory::create();

        $locations = array();
        for ($i = 1; $i <= 25; ++$i) {
            $locations[] = [
              $faker->longitude,
              $faker->latitude,
              $faker->longitude,
              $faker->latitude,
            ];
        }

        $param->setLocations($locations);
        $this->assertEquals($locations, $param->getLocations());

        $result = $validator->validate($param);
        $this->assertEquals(0, $result->count());

        $locations[] = [
          $faker->longitude,
          $faker->latitude,
          $faker->longitude,
          $faker->latitude,
        ];
        $param->setLocations($locations);
        $result = $validator->validate($param);
        $this->assertEquals(1, $result->count());
    }

    public function testLanguage()
    {
        $param = new StreamParameters();
        $validator = $this->builder->getValidator();

        $param->setLanguage('en');
        $this->assertEquals('en', $param->getLanguage());

        $result = $validator->validate($param);
        $this->assertEquals(0, $result->count());

        $param->setLanguage('qqq');
        $result = $validator->validate($param);
        $this->assertEquals(1, $result->count());
    }
}
