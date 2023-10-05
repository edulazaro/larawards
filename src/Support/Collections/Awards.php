<?php

namespace EduLazaro\Larawards\Support\Collections;

use ArrayIterator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use EduLazaro\Larawards\Concerns\Awardable;
use Illuminate\Support\Traits\EnumeratesValues;

/**
 * Use the store and query the awards
 */
class Awards extends Collection
{
    public static array $map;
    
    /**
     * @param array $awardClassMap
     * @return void
     */
    public static function enforceMap(array $awardClassMap = [])
    {
        foreach ($awardClassMap as $awardName => $awardClass) {
            self::$map[$awardName] = $awardClass;
        }
    }

    /**
     * Get a new ward colelction instance
     * @return Awards
     */
    public static function instance()
    {
        return app()->make(self::class);
    }

    /**
     * Awards not actually awarede to the user
     *
     * @param Awardable $awardable
     * @return static
     */
    public function scope($awardable): static
    {
        $scopedItems = [];
        foreach ($this->items as $key => $award) {
            $scopedItems[$key] = $award::scope($awardable);
        }

        return new static($scopedItems);
    }

    
    public function group($groupName)
    {
        $groups = $this->groupBy('group');

        $group = $groups->get($groupName);

        return $group ?: new static([]);
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return static
     */
    public function award($award)
    {
        if (!empty($this->items[$award])) {
            return new static($this->only($this->items, [$award]));
        }

        if (!empty(self::$map[$award])) {
            return new static($this->only($this->items, [self::$map[$award]]));
        }

        return new static($this->only($this->items, []));
    }


    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return static
     */
    public function nextUnlockableTiers()
    {
        $unlockableTiers = collect();
        foreach($this->items as $award) {
            $nextTier = $award->nextTier();
            if (!$nextTier) continue;
            
            $unlockableTiers->push($nextTier);
        }

        return $unlockableTiers;
    }


    public function check()
    {
        foreach ($this->items as $award) {
            $award->check($this->awardable);
        }
    }
}