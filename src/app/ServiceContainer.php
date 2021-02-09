<?php
declare(strict_types=1);

namespace App;

class ServiceContainer
{
    protected $services = [];

    /**
     * @param string $serviceId
     *
     * @return mixed
     */
    public function get($serviceId)
    {
        if (empty($this->services[$serviceId])) {
            throw new \RuntimeException(sprintf('Invalid service "%s"', $serviceId));
        }
        return $this->services[$serviceId];
    }

    /**
     * @param string $serviceId
     * @param object $service
     * @return $this
     */
    public function addServices($serviceId, $service)
    {
        $this->services[$serviceId] = $service;
        return $this;
    }
}