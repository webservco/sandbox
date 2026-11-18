<?php

declare(strict_types=1);

// Sandbox module texts: the layout and the test page. Catalog of the language "en".
// Not adding namespace declaration (Psalm dead code error).
// Not using docblock here to prevent "The file-level docblock must follow the opening PHP tag in the file header"
return [
    'layout.base_url' => 'Base URL:',
    'layout.hello' => 'Hello, world!',
    'layout.language' => 'Language',
    'layout.title' => 'Sandbox',
    'test.date' => 'Date',
    'test.int_property' => 'Int property',
    'test.items' => '{count, plural, one {# item} other {# items}}',
    'test.number' => 'Number',
    'test.plural' => 'Plural',
    'test.string_property' => 'String property (user input)',
];
