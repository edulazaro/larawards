<?php

namespace EduLazaro\Larawards\Contracts;

use Illuminate\Support\Collection;

use EduLazaro\Larawards\Concerns\HasRewards;

interface AwardInterface
{
    /**
     * Add a new award tier
     *
     * @param string $name
     * @param string $score
     * @param string $title
     * @return void
     */
    public function tier(string $name, string $score, string $title): void;

    /**
     * Method to configure the tiers.
     *
     * @return Collection
     */
    public function tiers(): Collection;

    /**
     * Get the awardable score a user
     *
     * @return int
     */
    public function score(): int;
}
