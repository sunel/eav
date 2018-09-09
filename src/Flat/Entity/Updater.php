<?php

namespace Eav\Flat\Entity;

use Eav\Entity;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;

class Updater
{
    /**
     * Entity model instacne.
     *
     * @var \Eav\Entity
     */
    protected $entity;

    /**
     * Console command instance.
     *
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $console;

    /**
     * Create updater instance.
     *
     * @param \Eav\Entity  $entity
     * @param \Symfony\Component\Console\Command\Command $console
     *
     * @return void
     */
    public function __construct(Entity $entity, Command $console)
    {
        $this->entity = $entity;
        $this->console = $console;
    }

    /**
     * Inserts values into the flat table.
     *
     * @return void
     */
    public function insert()
    {
        $entity = app($this->entity->entity_class);

        $flatEntity = app($this->entity->entity_class);
    
        $flatEntity->baseEntity();
        
        $flatEntity->setUseFlat(true);

        $flatEntity->truncate();

        $flatEntity->setUseFlat(false);

        $attributes = $this->entity->attributes()
                    ->whereNotIn('backend_type', ['static', ''])
                    ->get()->patch()->toBase();

        $this->console->info("\t Updating `{$this->entity->entity_table}` flat table.");

        $bar = $this->console->getOutput()->createProgressBar(\DB::table($this->entity->entity_table)->count());

        $bar->setFormat("\t %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");

        $count = (int) $this->console->option('count');

        \DB::table($this->entity->entity_table)
            ->select($entity->getKeyName())
            ->orderBy($entity->getKeyName(), 'asc')
            ->chunk($count, function ($chunk) use ($entity, $flatEntity, $attributes, &$bar) {
                $ids = $chunk->pluck($entity->getKeyName())->toArray();
                
                $products = Collection::make([]);
                
                $attributes->chunk(50)->each(function ($chunk, $key) use ($entity, $ids, &$products) {
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

                $flatEntity->setUseFlat(true);
                $flatEntity->insert($products->toArray());
                $flatEntity->setUseFlat(false);

                $bar->advance(count($ids));
            });

        $bar->finish();

        $this->console->info("\n");
        $this->console->info("\t Updated `{$this->entity->entity_table}` flat table.");
    }
}
