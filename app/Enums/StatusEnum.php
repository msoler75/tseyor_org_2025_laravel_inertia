<?php
namespace App\Enums;

enum StatusEnum
    {
        case DRAFT;
        case PUBLISHED;

        public function readableText(): string
        {
            return "TT:" . $this;
            return match ($this) {
                StatusEnum::DRAFT => 'Is draft',
                StatusEnum::PUBLISHED => 'Is published',
            };
        }
    }
