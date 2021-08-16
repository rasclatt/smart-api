<?php
namespace Nubersoft\Api\Interfaces;

interface IAuth
{
    public function validate(): bool;
}