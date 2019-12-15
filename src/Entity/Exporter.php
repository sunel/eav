<?php

namespace Eav\Entity;

use Eav\Entity;
use League\Csv\Writer;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;

class Exporter
{
    /**
     * Entity model instacne.
     *
     * @var \Eav\Entity
     */
    protected $entity;

    /**
     * Collecton of all attributes.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * Collecton of all attributes of type select.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $selectAttr;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Console command instance.
     *
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $console;

    /**
     * Holds the set name.
     *
     * @var array
     */
    protected $setNameCache = [];

    /**
     * Create updater instance.
     *
     * @param \Eav\Entity  $entity
     * @param \Symfony\Component\Console\Command\Command $console
     *
     * @return void
     */
    public function __construct(Entity $entity, Filesystem $files, Command $console)
    {
        $this->entity = $entity;
        $this->files = $files;
        $this->console = $console;

        $this->attributes = $entity->attributes()
                    ->with('optionValues')
                    ->whereNotIn('backend_type', ['static', ''])
                    ->get()->patch()->toBase();
        $this->selectAttr = $this->attributes->filter->usesSource();

        $this->setNameCache = Collection::make([]);
    }

    /**
     * Inserts values into the flat table.
     *
     * @param  string $path
     * @return void
     */
    public function export(string $path): void
    {
        $entity = app($this->entity->entity_class);

        $this->makeDirectory($path);
        
        $this->files->put($path, '');

        $writer = Writer::createFromPath($path, 'w+');

        $bar = $this->console->getOutput()->createProgressBar(\DB::table($this->entity->entity_table)->count());

        $bar->setFormat("%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $count = (int) $this->console->option('count');

        \DB::table($this->entity->entity_table)
            ->select($entity->getKeyName())
            ->orderBy($entity->getKeyName(), 'asc')
            ->chunk($count, function ($chunk, $page) use ($entity, $writer, &$bar) {
                $ids = $chunk->pluck($entity->getKeyName())->toArray();
                
                $products = Collection::make([]);
                
                $this->attributes->chunk(50)->each(function ($chunk, $key) use ($entity, $ids, &$products) {
                    $entity->select('*', ...$chunk->keys()->toArray())
                        ->whereIn("{$entity->getQuery()->from}.{$entity->getKeyName()}", $ids)
                        ->get()->each(function ($item, $key) use (&$products) {
                            if ($products->has($item->getKey())) {
                                $product = $products->get($item->getKey());
                                $products->put($item->getKey(), $product + $item->toArray());
                            } else {
                                $products->put($item->getKey(), $item->toArray());
                            }
                        });
                });

                if ($page === 1) {
                    $writer->insertOne(array_keys($products->first()));
                    $writer->addFormatter(function (array $row): array {
                        return $this->formate($row);
                    });
                }

                $writer->insertAll($products->toArray());

                $bar->advance(count($ids));
            });

        $bar->finish();
    }

    protected function formate(array $row): array
    {
        $row['entity_id'] = $this->entity->getCode();
        $row['attribute_set_id'] = $this->setNameCache->get($row['attribute_set_id'], function () use ($row) {
            return $this->setNameCache->put(
                $row['attribute_set_id'],
                $this->entity->attributeSet()->find($row['attribute_set_id'])->name()
            )->get($row['attribute_set_id']);
        });

        $this->selectAttr->each(function ($attribute, $code) use (&$row) {
            if (!is_null($row[$code])) {
                $row[$code] = Arr::get($attribute->options(), $row[$code]);
            }
        });
        
        return $row;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return void
     */
    protected function makeDirectory(string $path): void
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
