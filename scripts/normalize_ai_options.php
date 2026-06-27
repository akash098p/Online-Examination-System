<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$normalize = function (string $value): string {
    $value = trim($value);
    $value = preg_replace('/^\s*(?:[A-Za-z]|\d+)[\)\].:\-]\s*/', '', $value) ?? $value;
    $value = preg_replace('/^\s*[A-Za-z]\s+\)\s*/', '', $value) ?? $value;

    return trim($value);
};

$optionUpdates = 0;

foreach (App\Models\Option::query()->get() as $option) {
    $clean = $normalize((string) $option->option_text);

    if ($clean !== $option->option_text) {
        $option->option_text = $clean;
        $option->save();
        $optionUpdates++;
    }
}

$draftUpdates = 0;

foreach (App\Models\AiGeneratedQuestion::query()->get() as $draft) {
    $options = collect($draft->options ?? [])
        ->map(fn ($item) => $normalize((string) $item))
        ->values()
        ->all();

    $correct = $normalize((string) $draft->correct_answer);

    if ($options !== ($draft->options ?? []) || $correct !== $draft->correct_answer) {
        $draft->options = $options;
        $draft->correct_answer = $correct;
        $draft->save();
        $draftUpdates++;
    }
}

echo "Updated options: {$optionUpdates}\n";
echo "Updated AI drafts: {$draftUpdates}\n";
