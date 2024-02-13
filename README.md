# Larawards

An way of implementing achievement on Laravel.


## How to install Larawards

Execute this command on the Laravel root project folder:

```bash
composer require edulazaro/larawards
```

Configure the database details on the `.env` file and execute the migrations using:

```php
php artisan migrate
```

## Creating new awards

To create a new award (achievement, trophy, badge...) use this command, replacing `FooAchievement` for the ward class name:

```php
php artisan make:award FooAchievement
```

This will create a new award on the `app/Awards/FooAchievement.php`.

An award can be of different types, like an `achievement` or a `badge`. To set the type, use thje `$type` attribute:

```php
/** @var string The award type. */
public $type = 'achievement';
```

An award can have one or more tiers. Each tier si something which can be awarded, like an achievement. Tiers allow to define similar achievements on a single file:

```php
/** @var protected The award tiers. */
protected array $tiers = [
    'comment_written' => [
        'score' => 1,
        'title' => 'First Comment Written',
    ],
    '3_comments_written' => [
        'score' => 3,
        'title' => '3 Comments Written',
    ],
    '5_comments_written' => [
        'score' => 5,
        'title' => '5 Comments Written',
    ],
    '10_comments_written' => [
        'score' => 10,
        'title' => '10 Comments Written',
    ],
    '20_comments_written' => [
        'score' => 20,
        'title' => '20 Comments Written',
    ],
];
```

Each tier has a `title`, which is the achievement/badge name and also a `score`,` which is the score needed to reward the users with the achievement/badge.


Finally, the `score` method is the place to code the logic which retrieves the current award score for the `awardablep` user:

```php
/**
* Get the awardable score a user
 *
* @return int
*/
public function score(): int
{
    return $this->rewardable->comments()->count();
}
```

## Registering awards

If you ant an model to accept rewards, you will need to use the `HasWards` trait into it, like on this example:


```php
namespace App\Models;

use EduLazaro\Larawards\Concerns\HasRewards;

class User
{
    use HasRewards;

}
```

The achievements should be registered with the User model or another model including the `HasRewards` trait using the `AwardProvider`:

```php
User::awardable(AchievementsBadge::class);
```

Or grouped:

```php
User::awardableGroup('achievements', [
    CommentsAchievement::class,
    LessonsWatchedAchievement::class
]);
```

Awards can be registed to any model which uses the `HasRewards` trait.

## Checking rewards

When you need to check an award, you can do this:

```php
FooAchievement::scope($user)->check();
```

As you can see, the award needs to be scoped first to the user This will add the reward if it matches the requirement of any tier.
awardables.

You can also perform queries to check many awards at a time. This will check all awards assigned to a user:

```php
User::awardables()->check();
```

You can also select a specific group:

```php
User::awardables()->group('top_awards')->check();
```

Or a specific type:

```php
User::awardables()->where('type', 'achievement')->check();
```

Once an award is awarded, it will be added into the `rewards` table.

## Reward events

If you want, you can specify an event to be fired using the `$event` attribute:

```php
namespace App\Awards;

use EduLazaro\Larawards\Concerns\IsAward;
use EduLazaro\Larawards\Contracts\AwardInterface;
use App\Events\AchievementUnlocked;

class CommentsAchievement implements AwardInterface
{
    use IsAward;

    // ...

    protected string $event = AchievementUnlocked::class;
    // ...
}
```

This event will be fired each time a user gets a reward.

## Checking awards

To get the rewards for a user you can do:

```php
$rewards = $user->rewards;
```

To check if a specific award and tier has been rewarded:

```php
$isRewarded = $user->rewards()->where('name', 'comment_written')->exists();
```

You can query the rewards for many users:

```php
use EduLazaro\Larawards\Models\Reward;

// ...
Reward::where('award_id', 'comments_achievement')->get();
```
## Awards morph map

This works in the same way as the standard [Laravel morph maps](https://laravel.com/docs/10.x/eloquent-relationships#custom-polymorphic-types).

When a reward is inserte dinto the database, the award id will be the class where the award is defined. However this looks ugly. To fix it, use the `Award::enforceMap` method on any service provider:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use EduLazaro\Larawards\Collections\Awards;

use App\Awards\CommentsAchievement;
use App\Awards\LikesAchievement;

class AwardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Awards::enforceMap([
            'comments_achievement' => CommentsAchievement::class,
            'likes_achievement' => LikesAchievement::class,
        ]);
    }
}
```

This will assign the specified alias to the desided Award classes. For example, the `App\Awards\CommentsAchievement` will be represented as `comments_achievement`  which is more readable.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).