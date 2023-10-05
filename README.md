# Awards Demo

An possible way of implementing achievement on Laravel.

## Testing

The tests are configured to use an in-memory database. To execute the tests run:

```php
php artisan test
```

## Start up


Configure the database details on the `.env` file and execute the migrations using:

```php
php artisan migrate
```

## Creating new awards

To create a new award (achievement, trophy, badge...) use this command, replacing `FooAchievement` for the ward class name:

```php
php artisan make award FooAchievement
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

Each tier has a `title`, which is the achievement/badge name and also a `score`,` whcih is the score needed to reward the users with the achievement/badge.


Finally, the `score` method is the place to code the logic which retrieves the current award score for the `awardablep` user:

```php
/**
* Get the awardable score a user
 *
* @param $awardable;
* @return int
*/
public function score($awardable = null): int
{
    if ($awardable == null && $this->hasRewards) {
        $awardable = $this->hasRewards;
    }

    return $awardable->comments()->count();
}
```

## Registering awards

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

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# larawards
