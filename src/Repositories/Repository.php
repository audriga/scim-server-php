<?php

namespace Opf\Repositories;

use Psr\Container\ContainerInterface;

abstract class Repository
{
    protected $container;
    protected $dataAccess;
    protected $adapter;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    abstract public function getAll(): array;
    abstract public function getOneById(string $id): ?object;
    abstract public function create($object): ?object;
    abstract public function update(string $id, $object): ?object;
    abstract public function delete(string $id): bool;
}
