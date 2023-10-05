<?php

namespace EduLazaro\Larawards\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use EduLazaro\Larawards\Models\Reward;
use EduLazaro\Larawards\Contracts\AwardInterface;
use EduLazaro\Larawards\Support\Collections\Awards;

/**
 * Applicable to models which can be awarded
 */
trait HasRewards
{
    /** @var Collection Stores the awards */
    private static $awardableGroups;

    /** @var Awards Stores the awards */
    public static $awardables;

    /**
     * Register a new award for the awardable model
     * 
     * @param string $award An awardable class.
     * @return AwardInterface
     */
    public static function awardable(string $award)
    {
        !empty(self::$awardables) || self::$awardables = Awards::instance();

        self::$awardables->has($award) || self::$awardables->put($award, app()->make($award));
  
        return self::$awardables->get($award);
    }

    /**
     * Register a new award group for the awardable model
     * 
     * @param string $name The group name.
     * @param string[] $awardables The awards class names
     * @return Collection
     */
    public static function awardableGroup(string $name, array $awardablesArray)
    {
        !empty(self::$awardables) || self::$awardables = Awards::instance();


        foreach ($awardablesArray as  $awardable) {
            self::awardable($awardable)->setGroup($name);
        }

        return self::$awardables->group($name);
    }

    /**
     * Query the registered awards
     * 
     * @return Awards
     */
    public function awardables(): Awards
    {
        return self::$awardables->scope($this);
    }

    /**
     * Return the user awards
     *
     * @return MorphMany
     */
    public function rewards(): MorphMany
    {
        return $this->morphMany(Reward::class, 'awardable');
    }

    /**
     * Check if the awardable entity has an award
     * 
     * @param string $name An award name.
     * @return Reward
     */
    public function reward($award, $tier): Reward
    {
        $awardName = array_search($award::class, Awards::$map);

        return $this->rewards()->firstOrCreate([
            'award_id' => $awardName ? $awardName : $award::class,
            'award_type' => $award->type,
            'name' => $tier,
        ]);
    }
}