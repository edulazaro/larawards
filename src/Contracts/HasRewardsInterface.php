<?php

namespace EduLazaro\Larawards\Contracts;

use Illuminate\Support\Collection;

use EduLazaro\Larawards\Contracts\AwardInterface;

interface HasRewardsInterface
{
    /**
     * Register a new award for the awardable model
     * 
     * @param string $awardable An awardable class.
     * @return AwardInterface
     */
    public static function awardable(string $award): AwardInterface;

    /**
     * Register a new award group for the awardable model
     * 
     * @param string $name The group name.
     * @param string[] $awardables The awards class names
     * @return Collection
     */
    public static function awardableGroup(string $name, array $awardablesArray): Collection;

    /**
     * Query the registered awards
     * 
     * @return Awards
     */
    public function awardables(): Awards;

    /**
     * Return the user rewards
     *
     * @return MorphMany
     */
    public function rewards(): MorphMany;

    /**
     * Reward the user with the specified award and tier
     * 
     * @param string $name An award name.
     * @param string $tier A tier name.
     * @return Reward
     */
    public function reward(string $award, string $tier): Reward;
}
