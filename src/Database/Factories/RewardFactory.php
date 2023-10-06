<?php

namespace EduLazaro\Larawards\Database\Factories;

use App\Models\User;
use EduLazaro\Larawards\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

use EduLazaro\Larawards\Support\Collections\Awards;

class RewardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reward::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'awardable_id' => false,
            'awardable_type' => 'user',
            'award_id' => $this->faker->text(),
            'award_type' => $this->faker->text(),
            'name' => $this->faker->text(),
        ];
    }

    public function withUser($id): Factory
    {
        $user = User::findOrFail($id);

        return $this->state([
            'awardable_id' => $user->id,
            'awardable_type' => 'user',
        ]);
    }

    public function withAward($award): Factory
    {
        $existingTierNames = Reward::where('awardable_id', $award->hasRewards->id)
                        ->where('award_id', array_search($award::class, Awards::$map))
                        ->pluck('name')->toArray();

        $availableNames = array_diff($award->tiers()->keys()->toArray(), $existingTierNames);

        return $this->state([
            'award_id' => array_search($award::class, Awards::$map),
            'award_type' => 'achievement',
            'name' => reset($availableNames),
        ]);
    }
}
