<?php

namespace App\Config;

class Types
{
    private array $number = [
        'min' => true,
        'max' => true,
        'default' => true
    ];

    private array $text = [
        'min' => true,
        'max' => true,
        'default' => true
    ];

    private array $email = [

    ];

    private array $phone = [

    ];

    private array $selectBox = [
        'values' => true
    ];

    private array $sharedFields = [
        'required' => true
    ];

    private array $file = [

    ];

    public function getNumber(): array
    {
        return $this->number;
    }

    public function getText(): array
    {
        return $this->text;
    }

    public function getEmail(): array
    {
        return $this->email;
    }

    public function getPhone(): array
    {
        return $this->phone;
    }

    public function getSelectBox(): array
    {
        return $this->selectBox;
    }

    public function getFile(): array
    {
        return $this->file;
    }
}