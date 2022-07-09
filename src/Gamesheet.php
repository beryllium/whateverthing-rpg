<?php

namespace Beryllium\OnePageRpg;

class Gamesheet
{
    protected array $sheet;

    public function __construct(mixed $sheetData)
    {
        if (!is_array($sheetData)) {
            throw new \Exception('Gamesheet is invalid');
        }

        $this->sheet = $sheetData;
    }

    public function getName(): string
    {
        return $this->sheet['Name'] ?? 'Unknown';
    }

    public function getStats(): array
    {
        return $this->sheet['Stats'] ?? [];
    }

    public function getEvents(): array
    {
        return $this->sheet['Events'] ?? [];
    }

    public function getRules(): array
    {
        return $this->sheet['Rules'] ?? [];
    }
}