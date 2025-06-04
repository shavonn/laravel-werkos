<?php

namespace Sb\LaravelWerkos\DTO;

use WorkOS\Resource\User;

class WorkOsUser extends User
{
    public mixed $id;

    public mixed $firstName;

    public mixed $lastName;

    public mixed $email;
}
