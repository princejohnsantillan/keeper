<?php

declare(strict_types=1);

namespace App;

final class Avatar
{
    public static function generateUrl(string $name): string
    {
        $colors = [
            'f87171', 'ef4444', 'dc2626',
            'fb923c', 'f97316', 'ea580c',
            'fbbf24', 'f59e0b', 'd97706',
            'facc15', 'eab308', 'ca8a04',
            'a3e635', '84cc16', '65a30d',
            '4ade80', '22c55e', '16a34a',
            '34d399', '10b981', '059669',
            '2dd4bf', '14b8a6', '0d9488',
            '22d3ee', '06b6d4', '0891b2',
            '38bdf8', '0ea5e9', '0284c7',
            '60a5fa', '3b82f6', '2563eb',
            '818cf8', '6366f1', '4f46e5',
            'a78bfa', '8b5cf6', '7c3aed',
            'c084fc', 'a855f7', '9333ea',
            'e879f9', 'd946ef', 'c026d3',
            'f472b6', 'ec4899', 'db2777',
            'fb7185', 'f43f5e', 'e11d48',
        ];

        $color = $colors[crc32($name) % count($colors)];

        return "https://ui-avatars.com/api/?size=512&format=png&color=fafafa&background={$color}&name=".urlencode($name);
    }
}
