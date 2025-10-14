<?php

namespace app\services;

interface ICacher
{
    public function has(string $key): bool;
    public function get(string $key): string;
    public function set(string $key, string $value): void;
}