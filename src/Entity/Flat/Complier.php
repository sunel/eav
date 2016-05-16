<?php

namespace Eav\Entity\Flat;

use Eav\Entity;

class Complier
{
    protected $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function compile()
    {   
        $this->createTable();
        $this->insertValues();
    }

    protected function createTable()
    {
        $this->buildSchema();
    }

    protected function buildSchema()
    {
        foreach ($this->collectAttributes() as $key => $value) {
            dd($key, $value);
        }
    }

    protected function collectAttributes()
    {
        return $this->entity->eavAttributes();
    }


    //get entity
    //collect attributes
    //create columns
    //create table
    //insert values
}
