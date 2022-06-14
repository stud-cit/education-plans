<?php

namespace Tests\Unit\Services;

use App\ExternalServices\Asu\ASU;
use Tests\TestCase;

class AsuTest extends TestCase
{
    public function testHasAPIKey()
    {
        $asu = new ASU();
        $key = $asu->getAsuKey();

        $keyNotEmpty = !(strlen($key) !== 32);

        $this->assertIsString($key);
        $this->assertTrue($keyNotEmpty);
    }
}
