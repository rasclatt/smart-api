<?php
namespace SmartApi\Interfaces;

interface IAuth
{
    public function validate(): bool;
}