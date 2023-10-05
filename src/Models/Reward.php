<?php

namespace EduLazaro\Larawards\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

use EduLazaro\Larawards\Support\Collections\Awards;

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

    public function getAwardAttribute()
    {
        $className = isset(Awards::$map[$this->attributes['award_id']])
            ? Awards::$map[$this->attributes['award_id']]
            : $this->attributes['award_id'];

        return $className::scope($this->awardable);
    }

    public function getTierAttribute()
    {
        return $this->award->tiers()->get($this->name);
    }

    public function getNextTierAttribute()
    {
        return $this->award->tiers()->where('score', '>', $this->tier['score'])->first();
    }

    public function getTitleAttribute()
    {
        return $this->tier['title'];
    }

    public function getNextTierScoreAttribute()
    {
        if (!$this->nextTier) return null;

        $currentScore = $this->award->score($this->awardable);

        return $this->nextTier['score'] - $currentScore;
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function awardable(): MorphTo
    {
        return $this->morphTo();
    }
}
