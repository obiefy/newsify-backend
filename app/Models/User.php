<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ITEM_CATEGORY = 'category';
    const ITEM_SOURCE = 'source';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'categories' => 'array',
        'sources' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function follow(string $type, string $value): void {
        if($type === static::ITEM_CATEGORY) {
            $categories = $this->categories ?? [];
            $categories[] = $value;
            $this->categories = $categories;
        }
        if($type === static::ITEM_SOURCE) {
            $sources = $this->sources ?? [];
            $sources[] = $value;
            $this->sources = $sources;
        }
        
        $this->save();
    }

    public function unfollow(string $type, string $value): void {
        if($type === static::ITEM_CATEGORY) {
            $categories = $this->categories ?? [];
            $categories = collect($categories)->filter(fn ($category) => $category !== $value)->toArray();
            $this->categories = $categories;
        }
        if($type === static::ITEM_SOURCE) {
            $sources = $this->sources ?? [];
            $sources = collect($sources)->filter(fn ($source) => $source !== $value)->toArray();
            $this->sources = $sources;
        }

        $this->save();
    }
}
