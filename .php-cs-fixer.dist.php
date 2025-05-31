<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'header_comment' => [
            'header' => "© 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later\nSee LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.",
        ],
    ])
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder)
;
