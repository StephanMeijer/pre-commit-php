#!/usr/bin/env php
<?php
declare(strict_types=1);
define("MARKUP", "\033[");
define("MARKUP_RESET", "0");
define('MARKUP_UNDERLINE', "4");
define('MARKUP_FG_WHITE', "97");
define('MARKUP_FG_GREEN', "32");
define('MARKUP_FG_RED', "31");
define('MARKUP_FG_GRAY', "90");
define('MARKUP_BOLD', "1");

function color(string $text = '', array $colors = []): string {
    if (!$colors || !$text) {
        return $text;
    }
    $colorStr = MARKUP . join(';', $colors) . 'm';
    return $colorStr . $text . MARKUP . MARKUP_RESET . 'm';
}

function write_line(string $text = '', array $colors = [], bool $stderr = false): void {
    $line = color($text, $colors) . PHP_EOL;
    if ($stderr) {
        fwrite(STDERR, $line);
    } else {
        fwrite(STDOUT, $line);
    }
}
/**
 * @var string[] $successes
 */
$successes = [];
/**
 * @var array<string, string> $failures
 */
$failures = [];

if ($argc === 1) {
    write_line(
        color("[ERROR]    ", [MARKUP_FG_RED, MARKUP_BOLD]) . "No files given.",
        [], true
    );
    exit(1);
}

foreach ($argv as $i => $file) {
    if ($i === 0) {
        continue;
    } else if (!str_ends_with($file, ".php")) {
        continue;
    } else if (!file_exists($file)) {
        $failures[$file] = "File does not exist";
    } else if (!is_readable($file)) {
        $failures[$file] = "File is not readable";
    } else {
        $contents = file_get_contents($file);
        if (str_contains($contents, "declare(strict_types=1);")) {
            $successes[] = $file;
        } else {
            $failures[$file] = "File does not contain 'declare(strict_types=1);'";
        }
    }
}

if (!empty($successes)) {
    write_line();
    write_line("SUCCESSES", [ MARKUP_FG_WHITE, MARKUP_UNDERLINE ]);
    foreach ($successes as $file) {
        write_line(
            color("[SUCCESS]", [MARKUP_FG_GREEN, MARKUP_BOLD]) . "        $file"
        );
    }
}

if (!empty($failures)) {
    $maxFailureFilename = 0;

    foreach ($failures as $file => $reason) {
        if (strlen($file) > $maxFailureFilename) {
            $maxFailureFilename = strlen($file);
        }
    }

    write_line();
    write_line("FAILURES", [ MARKUP_FG_WHITE, MARKUP_UNDERLINE ], true);

    foreach ($failures as $file => $reason) {
        write_line(
            color("[FAILURE]", [MARKUP_FG_RED, MARKUP_BOLD]) .
                str_pad("        $file", $maxFailureFilename + 12, " ", STR_PAD_RIGHT) .
                color("⸺    " . $reason, [MARKUP_FG_GRAY]),
            [],
            true
        );
    }
}

if (!empty($failures)) {
    exit(1);
}

exit(0);
