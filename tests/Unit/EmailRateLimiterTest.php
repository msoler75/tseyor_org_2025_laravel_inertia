<?php

namespace Tests\Unit;

use App\Services\EmailRateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EmailRateLimiterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::clear(); // Clear cache before each test
    }

    public function test_can_send_within_limit()
    {
        Config::set('mail.rate_limit.max.overall', 5);
        Config::set('mail.rate_limit.window', 10); // 10 seconds window

        $rateLimiter = new EmailRateLimiter();

        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue($rateLimiter->canSend('test_job'));
            $rateLimiter->increment('test_job');
        }

        // After reaching the limit, it should not allow sending
        $this->assertFalse($rateLimiter->canSend('test_job'));
    }

    public function test_limit_resets_after_time_window()
    {
        Config::set('mail.rate_limit.max.overall', 3);
        Config::set('mail.rate_limit.window', 5); // 5 seconds window

        $rateLimiter = new EmailRateLimiter();

        // Saturate the system
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($rateLimiter->canSend('test_job'));
            $rateLimiter->increment('test_job');
            sleep(1); // Simulate time passing
        }

        // Should not allow sending after reaching the limit
        $this->assertFalse($rateLimiter->canSend('test_job'));

        // Wait for the time window to expire
        sleep(3);

        // Should allow sending again after the window resets
        $this->assertTrue($rateLimiter->canSend('test_job'));

        $rateLimiter->increment('test_job');
                // Should not allow sending after reaching the limit
        $this->assertFalse($rateLimiter->canSend('test_job'));

    }

    public function test_can_handle_multiple_job_types()
    {
        Config::set('mail.rate_limit.max.overall', 1000);
        Config::set('mail.rate_limit.max.test_job', 2);
        Config::set('mail.rate_limit.max.another_job', 3);
        Config::set('mail.rate_limit.window', 10); // 10 seconds window

        $rateLimiter = new EmailRateLimiter();

        // Test for 'test_job'
        $this->assertTrue($rateLimiter->canSend('test_job'));
        $rateLimiter->increment('test_job');
        $this->assertTrue($rateLimiter->canSend('test_job'));
        $rateLimiter->increment('test_job');
        $this->assertFalse($rateLimiter->canSend('test_job'));

        // Test for 'another_job'
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($rateLimiter->canSend('another_job'));
            $rateLimiter->increment('another_job');
        }
        $this->assertFalse($rateLimiter->canSend('another_job'));
    }
}
