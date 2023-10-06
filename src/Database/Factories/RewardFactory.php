<?php

namespace EduLazaro\Larawards\Database\Factories;

use App\Models\User;
use EduLazaro\Larawards\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

use EduLazaro\Larawards\Collections\Awards;

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
            'rewardable_id' => false,
            'rewardable_type' => 'user',
            'award_id' => $this->faker->text(),
            'award_type' => $this->faker->text(),
            'name' => $this->faker->text(),
        ];
    }

    public function withUser($id): Factory
    {
        $user = User::findOrFail($id);

        return $this->state([
            'rewardable_id' => $user->id,
            'rewardable_type' => 'user',
        ]);
    }

    public function withAward($award): Factory
    {
        $existingTierNames = Reward::where('rewardable_id', $award->rewardable->id)
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
