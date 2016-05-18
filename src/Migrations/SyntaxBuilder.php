<?php

namespace Eav\Migrations;

class SyntaxBuilder
{
    /**
     * A template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  array $schema
     * @return string
     */
    public function create($schema)
    {
        $up = $this->createSchemaForUpMethod($schema);
        $down = $this->createSchemaForDownMethod($schema);

        return compact('up', 'down');
    }

    /**
     * Create the schema for the "up" method.
     *
     * @param  string $schema
     * @return string
     */
    private function createSchemaForUpMethod($schema)
    {
        $fields = $this->constructSchema($schema);

        return $this->insert($fields)->into($this->getCreateSchemaWrapper());
    }

    /**
     * Construct the syntax for a down field.
     *
     * @param  array $schema
     * @return string
     */
    private function createSchemaForDownMethod($schema)
    {
        // If the user created a table, then for the down
        // method, we should drop it.
        return "Schema::drop('{{table}}');";
    }

    /**
     * Store the given template, to be inserted somewhere.
     *
     * @param  string $template
     * @return $this
     */
    private function insert($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the stored template, and insert into the given wrapper.
     *
     * @param  string $wrapper
     * @param  string $placeholder
     * @return mixed
     */
    private function into($wrapper, $placeholder = 'schema_up')
    {
        return str_replace('{{' . $placeholder . '}}', $this->template, $wrapper);
    }

    /**
     * Get the wrapper template for a "create" action.
     *
     * @return string
     */
    private function getCreateSchemaWrapper()
    {
        return file_get_contents(__DIR__ . '/stubs/schema-create.stub');
    }

    /**
     * Get the wrapper template for an "add" action.
     *
     * @return string
     */
    private function getChangeSchemaWrapper()
    {
        return file_get_contents(__DIR__ . '/stubs/schema-change.stub');
    }

    /**
     * Construct the schema fields.
     *
     * @param  array $schema
     * @param  string $direction
     * @return array
     */
    private function constructSchema($schema, $direction = 'Add')
    {
        if (!$schema) {
            return '';
        }

        $fields = array_map(function ($field) use ($direction) {
            $method = "{$direction}Column";

            return $this->$method($field);
        }, $schema);

        return implode("\n" . str_repeat(' ', 12), $fields);
    }


    /**
     * Construct the syntax to add a column.
     *
     * @param  string $field
     * @return string
     */
    private function addColumn($field)
    {
        $syntax = sprintf("\$table->%s('%s')", $field['type'], $field['name']);

        // If there are arguments for the schema type, like decimal('amount', 5, 2)
        // then we have to remember to work those in.
        if ($field['arguments']) {
            $syntax = substr($syntax, 0, -1) . ', ';

            $syntax .= implode(', ', $field['arguments']) . ')';
        }

        foreach ($field['options'] as $method => $value) {
            $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
        }

        return $syntax .= ';';
    }

    /**
     * Construct the syntax to drop a column.
     *
     * @param  string $field
     * @return string
     */
    private function dropColumn($field)
    {
        return sprintf("\$table->dropColumn('%s');", $field['name']);
    }
}
