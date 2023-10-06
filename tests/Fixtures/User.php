<?php

namespace Laravel\Cashier\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use EduLazaro\Larawards\Concerns\HasRewards;

class User extends Authenticatable
{
    use HasFactory, HasRewards;

    protected $guarded = [];
}