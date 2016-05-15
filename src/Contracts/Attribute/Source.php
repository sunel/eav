<?php

namespace Eav\Contracts\Attribute;

interface Source
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions();

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value);
}
