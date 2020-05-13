<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlightError', [$this, 'highlightError']),
        ];
    }

    public function highlightError(string $string, int $start, int $end)
    {
        $highlightText = substr($string, $start, $end);

        return str_replace($highlightText, "<span class=\"bg-danger\">$highlightText</span>", $string);
    }
}
