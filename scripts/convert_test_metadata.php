<?php

$root = __DIR__ . '/../tests';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$updated = 0;

foreach ($files as $file) {
    if (!$file->isFile()) continue;
    if ($file->getExtension() !== 'php') continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    // Pattern: capture docblock (/** ... */) followed by optional whitespace/newlines and a function declaration
    $pattern = '/(\/\*\*(?:.|\s)*?\*\/)(\s*)(public|protected|private)\s+function\s+/i';

    $newContent = preg_replace_callback($pattern, function ($m) {
        $docblock = $m[1];
        $whitespace = $m[2];
        $funcVisibility = $m[3];

        // If docblock contains @test, we need to remove the @test line and add attribute
        if (stripos($docblock, '@test') !== false) {
            // Split docblock into lines
            $lines = preg_split("/\R/", $docblock);
            $kept = [];
            foreach ($lines as $line) {
                if (stripos($line, '@test') !== false) {
                    // skip this line
                    continue;
                }
                $kept[] = $line;
            }

            // Rebuild docblock if any meaningful content remains
            $docRemaining = implode(PHP_EOL, $kept);
            // Determine indent from docblock first line if possible
            if (preg_match('/^(\s*)\/\*/', $lines[0], $im)) {
                $indent = $im[1] ?? '';
            } else {
                $indent = '';
            }

            $attributeLine = $indent . "#[\\PHPUnit\\Framework\\Attributes\\Test]";

            // If docRemaining is just /** */ or empty, drop it entirely
            $docTrim = trim(preg_replace('/^\/\*\*|\*\/\s*$/', '', $docRemaining));
            if ($docTrim === '') {
                // return attribute + whitespace + visibility (the function line remains unchanged)
                return $attributeLine . $whitespace . $funcVisibility . ' function ';
            }

            // Otherwise keep the cleaned docblock and insert attribute before function
            return $docRemaining . PHP_EOL . $attributeLine . $whitespace . $funcVisibility . ' function ';
        }

        // no change
        return $m[0];
    }, $content);

    if ($newContent !== $content) {
        file_put_contents($path, $newContent);
        echo "Updated: $path\n";
        $updated++;
    }
}

echo "Conversion complete. Files updated: $updated\n";
