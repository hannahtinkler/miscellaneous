<?php

namespace Tests\Unit;

use Prophecy\Prophet;

class TestCase extends \Codeception\Test\Unit
{
    public function _before()
    {
        $this->prophet = new Prophet;
    }

    public function mock($class)
    {
        return $this->prophet->prophesize($class);
    }

    public function assertMethodsCalled()
    {
        return $this->prophet->checkPredictions();
    }
}
