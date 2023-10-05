<?php

namespace EduLazaro\Larawards\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use EduLazaro\Larawards\Support\Collections\Awards;
use EduLazaro\Larawards\Concerns\HasRewards;
use EduLazaro\Larawards\Contracts\AwardInterface;

/**
 * Applicable to things which can be awarded
 */
trait IsAward
{
    /**
     * Constructor.
     */
    public function __construct($hasRewards = null)
    {
        $this->hasRewards = $hasRewards ? $hasRewards : null;

        $this->name = empty($this->name)
            ? strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', self::class))))
            : $this->name ;     
        
        $this->title = empty($this->title) ? ucwords(str_replace('_', ' ', $this->name)) : $this->title;

        !empty($this->type) || $this->type = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', self::class))));

        $this->alias = array_search(self::class, Awards::$map);
        if (!$this->alias) $this->alias = $this->name;
    }
    
    public function tiers(): Collection
    {
        return !empty($this->tiers) ? collect($this->tiers)->sortBy('score') : collect([
            $this->name => [
                'score' => 1,
                'title' => $this->title,
            ],
        ]);
    }

    public static function scope($hasRewards): static
    {
        return new static($hasRewards);
    }



    /**
     * Add a new award tier to the tiers array.
     * 
     * @param string $groupName
     * @return void
     */
    public function setGroup(string $groupName): AwardInterface
    {
        $this->group = $groupName;
        return $this;
    }

    /**
     * Add a new award tier to the tiers array.

     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Add a new award tier to the tiers array.

     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Add a new award tier to the tiers array.
     * 
     * @param string $name
     * @param string $score
     * @param string $title
     * @return void
     */
    public function tier(string $score, string $name, string $title): void
    {
        $this->tiers->put($name, [
            'score' => $score,
            'name' => $name,
            'title' => $title,
        ]);
    }

    /**
     * Award this award to an awardable or user.
     * 
     * @param $awardable
     * @return void
     */
    public function check($awardable = null): void
    {
        if ($awardable == null && $this->hasRewards) {
            $awardable = $this->hasRewards;
        }

        if (empty($awardable )) return;

        $awardableScore = $this->score($awardable);

        foreach ($this->tiers as $name => $tier) {
            if ($awardableScore >= $tier['score']) {
                $awardable->reward($this, $name);
            }
        }
    }

    /**
     * Award this award to an awardable or user.
     * 
     * @param $awardable
     * @return void
     */
    public function nextTier(): array|null
    {
        $namesArray = $this->hasRewards->rewards()->where('award_id', $this->alias)->pluck('name')->toArray();

        foreach ($this->tiers() as $name => $tier) {

            if (in_array($name, $namesArray)) continue;
            return $tier;
        }

        return null;
    }
}
