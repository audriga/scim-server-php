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

    /**
     * @param string $filter Optional parameter which contains a filter expression
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
     *
     * @param int $startIndex Optional parameter for specifying the start index, used for pagination
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.4
     *
     * @param int $count Optional parameter for specifying the number of results, used for pagination
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.4
     *
     * @param array $attributes Optional parameter for including only specific attributes in the response
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.5
     *  (if $exludedAttributes is not empty, this should be empty)
     *
     * @param array $excludedAttributes Optional parameter for excluding specific attributes from the response
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.5
     *  (if $attributes is not empty, this should be empty)
     *
     * @return array An array of SCIM resources
     */
    abstract public function getAll(
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): array;

    /**
     * @param string $id Required parameter which contains of a given entity that should be retrieved
     *
     * @param string $filter Optional parameter which contains a filter expression
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.2
     *
     * @param int $startIndex Optional parameter for specifying the start index, used for pagination
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.4
     *
     * @param int $count Optional parameter for specifying the number of results, used for pagination
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.4
     *
     * @param array $attributes Optional parameter for including only specific attributes in the response
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.5
     *  (if $exludedAttributes is not empty, this should be empty)
     *
     * @param array $excludedAttributes Optional parameter for excluding specific attributes from the response
     *  as per https://www.rfc-editor.org/rfc/rfc7644.html#section-3.4.2.5
     *  (if $attributes is not empty, this should be empty)
     *
     * @return object|null A SCIM resource or null if no resource found
     */
    abstract public function getOneById(
        string $id,
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): ?object;

    abstract public function create($object): ?object;

    abstract public function update(string $id, $object): ?object;

    abstract public function delete(string $id): bool;
}
