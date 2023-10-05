<?php

namespace EduLazaro\Larawards\Observers;

use EduLazaro\Larawards\Models\Reward;

class RewardObserver
{
    /**
     * Handle the Reward "created" event.
     * 
     * @param Reward $reward
     * @return void
     */
    public function created(Reward $reward): void
    {
        $event = $reward->award->getEvent();
        if (empty($event)) return;

        $event::dispatch($reward);
    }
}
