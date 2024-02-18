<?php

namespace App\Tests\Trait;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

trait FixturesTrait
{
    private ?AbstractDatabaseTool $databaseTool = null;

    public function loadFixtures(array $fixtures): AbstractExecutor
    {
        if ($this->databaseTool === null) {
            $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        }

        return $this->databaseTool->loadFixtures($fixtures);
    }
}
