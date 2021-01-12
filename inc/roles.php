<?php

const MEMBER = 'MEMBER';
const MODERATOR = 'MODERATOR';
const ADMINISTRATOR = 'ADMINISTRATOR';

const DEFAULT_ROLE = MEMBER;

function hasRole(array $user, string... $roles): bool
{
    return in_array(getRoleByUser($user), $roles);
}

function getRoleByUser(array $user): string
{
    return !empty($user['role']) ? getRoleByValue($user['role']) : DEFAULT_ROLE;
}

function getRoleByValue($value): string
{
    switch ($value) {
        case MEMBER:
        case MODERATOR:
        case ADMINISTRATOR:
            return $value;
    }
    return DEFAULT_ROLE;
}
