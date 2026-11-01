<?php

use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Finder\Finder;

/**
 * Hosting tidak menjalankan daemon queue:work; antrean hanya diproses lewat cron.
 * Antrean yang tidak ada di --queue cron akan menumpuk tanpa pernah dikerjakan.
 */
function cronWorkerQueues(): array
{
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($e) => str_contains($e->command ?? '', 'queue:work'));

    expect($event)->not->toBeNull('queue:work tidak terdaftar di scheduler');

    preg_match('/--queue=(\S+)/', $event->command, $m);

    return explode(',', $m[1] ?? 'default');
}

/** @return array<string, string> nama antrean => file pertama yang memakainya */
function declaredQueues(): array
{
    $found = [];
    foreach ((new Finder)->files()->in(app_path())->name('*.php') as $file) {
        preg_match_all('/onQueue\(\s*[\'"]([\w.-]+)[\'"]|\$queue\s*=\s*[\'"]([\w.-]+)[\'"]/', $file->getContents(), $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $found[$match[1] ?: $match[2]] ??= $file->getRelativePathname();
        }
    }

    return $found;
}

it('processes every queue named in app code from the cron queue worker', function () {
    $workerQueues = cronWorkerQueues();

    foreach (declaredQueues() as $queue => $file) {
        $this->assertContains($queue, $workerQueues, "Antrean '{$queue}' ({$file}) tidak diproses oleh cron queue:work di routes/console.php");
    }
});

it('always keeps the default queue in the cron worker', function () {
    expect(cronWorkerQueues())->toContain('default');
});
