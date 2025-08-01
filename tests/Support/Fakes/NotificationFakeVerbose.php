<?php

namespace Tests\Support\Fakes;

use Illuminate\Support\Testing\Fakes\NotificationFake;
use Exception;

class NotificationFakeVerbose extends NotificationFake
{
    public function assertSentTo($notifiable, $notification, $callback = null, $context = null)
    {
        try {
            parent::assertSentTo($notifiable, $notification, $callback);
        } catch (\Throwable $e) {
            $msg = "[NotificationFakeVerbose] assertSentTo failed";
            if ($context) {
                $msg .= " | Context: $context";
            }
            $msg .= "\n" . $e->getMessage();
            throw new Exception($msg, $e->getCode(), $e);
        }
    }

    public function assertNotSentTo($notifiable, $notification, $callback = null, $context = null)
    {
        try {
            parent::assertNotSentTo($notifiable, $notification, $callback);
        } catch (\Throwable $e) {
            $msg = "[NotificationFakeVerbose] assertNotSentTo failed";
            if ($context) {
                $msg .= " | Context: $context";
            }
            $msg .= "\n" . $e->getMessage();
            throw new Exception($msg, $e->getCode(), $e);
        }
    }

    public function assertSentToTimes($notifiable, $notification, $times = 1, $context = null)
    {
        try {
            parent::assertSentToTimes($notifiable, $notification, $times);
        } catch (\Throwable $e) {
            $msg = "[NotificationFakeVerbose] assertSentToTimes failed";
            if ($context) {
                $msg .= " | Context: $context";
            }
            $msg .= "\n" . $e->getMessage();
            throw new Exception($msg, $e->getCode(), $e);
        }
    }

    public function assertNothingSent($context = null)
    {
        try {
            parent::assertNothingSent();
        } catch (\Throwable $e) {
            $msg = "[NotificationFakeVerbose] assertNothingSent failed";
            if ($context) {
                $msg .= " | Context: $context";
            }
            $msg .= "\n" . $e->getMessage();
            throw new Exception($msg, $e->getCode(), $e);
        }
    }
}
