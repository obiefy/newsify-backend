<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FollowingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_user_can_follow_and_unfollow_category() {
        $user = User::factory()->create();
        $user->follow('category', 'Sports');
        $user->follow('category', 'Business');

        $this->assertEquals(['Sports', 'Business'], $user->categories);
        
        $user->unfollow('category', 'Business');
        $this->assertCount(1, $user->categories);
        $this->assertEquals('Sports', $user->categories[0]);
    }


    public function test_user_can_follow_and_unfollow_source() {
        $user = User::factory()->create();
        $user->follow('source', 'BBC');
        $user->follow('source', 'NYT');

        $this->assertEquals(['BBC', 'NYT'], $user->sources);
        
        $user->unfollow('source', 'NYT');
        $this->assertCount(1, $user->sources);
        $this->assertEquals('BBC', $user->sources[0]);
    }
}
