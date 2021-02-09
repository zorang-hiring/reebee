<?php
// configure Doctrine CLI

use Doctrine\ORM\Tools\Console\ConsoleRunner;
require_once './bootstrap-doctrine.php';
return ConsoleRunner::createHelperSet(
    GetEntityManager::getEm()
);