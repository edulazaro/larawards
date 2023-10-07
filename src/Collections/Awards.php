<?php

namespace EduLazaro\Larawards\Collections;

use ArrayIterator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use EduLazaro\Larawards\Concerns\HasRewards;
use Illuminate\Support\Traits\EnumeratesValues;

/**
 * Use the store and query the awards
 */
class Awards extends Collection
{
   /** @var array  Stores the class map */
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
     * @param HasRewards $rewardable
     * @return static
     */
    public function scope($rewardable): static
    {
        $scopedItems = [];
        foreach ($this->items as $key => $award) {            
            $scopedItems[$key] = $award::scope($rewardable);
        }

        return new static($scopedItems);
    }

    /**
     * Add an award to a group
     *
     * @param string $name
     * @param array $awards
     * @return static
     */
    public function setGroup(string $name, $awards = []): static
    {
        if (empty($this->groups[$name])) $this->groups[$name] = [];

        foreach ($awards as $award) {
            $this->groups[$name][$award] = $award;
        }

        return $this;
    }
 
    /**
     * Run a filter over each of the items.
     *
     * @param string $groupName
     * 
     * @return static
     */   
    public function group($groupName)
    {
        $groups = $this->groupBy('group');

        $group = $groups->get($groupName);

        return $group ?: new static([]);
    }


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
     * Return the next unlockable tiers
     *
     * @return Collection
     */
    public function nextUnlockableTiers(): Collection
    {
        $unlockableTiers = collect();
        foreach($this->items as $award) {
            $nextTier = $award->nextTier();
            if (!$nextTier) continue;
            
            $unlockableTiers->push($nextTier);
        }

        return $unlockableTiers;
    }

    /**
     * @return void
     */
    public function check()
    {
        foreach ($this->items as $award) {
            $award->check();
        }
    }
}