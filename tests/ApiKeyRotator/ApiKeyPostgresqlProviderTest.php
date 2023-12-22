<?php

namespace bpteam\QuickParserWizard\Tests\ApiKeyRotator;

use bpteam\QuickParserWizard\ApiKeyRotator\ApiKeyPostgresqlProvider;
use bpteam\QuickParserWizard\ApiKeyRotator\Key;
use PHPUnit\Framework\TestCase;

class ApiKeyPostgresqlProviderTest extends TestCase
{
    public function testKeyLimitCheck()
    {
        $provider = new ApiKeyPostgresqlProvider(
            new \PDO(
                'pgsql:host=postgres;port=5432;dbname=postgres',
                'postgres',
                'postgres',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            )
        );

        $provider->init();

        $provider->add('key1', new Key('123'), 1, new \DateInterval('PT10S'));
        $provider->add('key2', new Key('456'), 2, new \DateInterval('PT10S'));
        $provider->add('key3', new Key('789'), 3, new \DateInterval('PT10S'));

        $this->assertEquals('123', $provider->find(1)->getCredentials());
        $this->assertEquals('456', $provider->find(1)->getCredentials());
        $this->assertEquals('456', $provider->find(1)->getCredentials());
        $this->assertEquals('789', $provider->find(3)->getCredentials());
        $this->assertEquals(null, $provider->find(1)?->getCredentials());

        $provider->deleteStorage();
    }
}
