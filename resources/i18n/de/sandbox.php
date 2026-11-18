<?php

declare(strict_types=1);

// Sandbox module texts: the layout and the test page. Catalog of the language "de".
// Not adding namespace declaration (Psalm dead code error).
// Not using docblock here to prevent "The file-level docblock must follow the opening PHP tag in the file header"
return [
    'layout.base_url' => 'Basis-URL:',
    'layout.hello' => 'Hallo, Welt!',
    'layout.language' => 'Sprache',
    'layout.title' => 'Sandbox',
    'test.date' => 'Datum',
    'test.int_property' => 'Ganzzahl-Eigenschaft',
    'test.items' => '{count, plural, one {# Element} other {# Elemente}}',
    'test.number' => 'Zahl',
    'test.plural' => 'Plural',
    'test.string_property' => 'Text-Eigenschaft (Benutzereingabe)',
];
