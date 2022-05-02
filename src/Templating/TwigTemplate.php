<?php

declare(strict_types=1);

namespace App\Templating;

abstract class TwigTemplate
{
    public const USER_REGISTER = 'mails/user/register.twig';
    public const REQUEST_RESET_PASSWORD = 'mails/user/request-reset-password.twig';
}