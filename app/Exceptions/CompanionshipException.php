<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a companionship business rule is violated.
 * Caught by Laravel's handler and flashed back as a user-friendly error.
 */
class CompanionshipException extends Exception
{
    public static function cannotJoinOwn(): self
    {
        return new self('You cannot join your own meetup.');
    }

    public static function notOpen(): self
    {
        return new self('This meetup is no longer open.');
    }

    public static function alreadyRequested(): self
    {
        return new self('You have already sent a request to join this meetup.');
    }
}
