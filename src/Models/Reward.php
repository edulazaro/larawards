<?php

namespace EduLazaro\Larawards\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use EduLazaro\Larawards\Database\Factories\RewardFactory;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

use EduLazaro\Larawards\Collections\Awards;

class Reward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'award_id',
        'award_type',
        'name',
    ];

    /** @var array Appended attributes */
    protected $appends = [
        'title',
        'next_tier_score'
    ];

    /**
     * @return RewardFactory
     */
    protected static function newFactory(): RewardFactory
    {
        return RewardFactory::new();
    }

    public function getAwardAttribute()
    {
        $className = isset(Awards::$map[$this->attributes['award_id']])
            ? Awards::$map[$this->attributes['award_id']]
            : $this->attributes['award_id'];

        return $className::scope($this->awardable);
    }

    /**
     * @return array
     */
    public function getTierAttribute(): array
    {
        return $this->award->tiers()->get($this->name);
    }

    /**
     * @return array
     */
    public function getNextTierAttribute(): array
    {
        return $this->award->tiers()->where('score', '>', $this->tier['score'])->first();
    }

    /**
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->tier['title'];
    }

    /**
     * @return int
     */
    public function getNextTierScoreAttribute(): int
    {
        if (!$this->nextTier) return null;

        $currentScore = $this->award->score();

        return $this->nextTier['score'] - $currentScore;
    }

    /**
     * Get the parent rewardable model
     */
    public function rewardable(): MorphTo
    {
        return $this->morphTo();
    }
}
