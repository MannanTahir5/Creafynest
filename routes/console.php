<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Fallback when `php artisan storage:link` fails on hosts where PHP's symlink() is disabled
 * and Laravel's Filesystem::link() used an unqualified exec() (resolved to the wrong namespace).
 * Uses global \symlink() / \exec(), then prints an SSH one-liner if PHP cannot create the link.
 */
Artisan::command('storage:link-safe', function () {
    if (windows_os()) {
        $this->error('This command targets Unix-style hosting. Use storage:link on Windows.');

        return 1;
    }

    $target = storage_path('app/public');
    $link = public_path('storage');

    if (! is_dir($target)) {
        $this->warn("Creating missing directory: {$target}");
        mkdir($target, 0755, true);
    }

    if (file_exists($link) && ! is_link($link)) {
        $this->error("{$link} already exists and is not a symlink. Move or delete it, then retry.");

        return 1;
    }

    if (is_link($link)) {
        @unlink($link);
    }

    if (function_exists('symlink') && @\symlink($target, $link)) {
        $this->info("Linked: {$link} -> {$target}");

        return 0;
    }

    $shell = 'ln -s '.escapeshellarg($target).' '.escapeshellarg($link);
    if (function_exists('exec')) {
        $code = -1;
        @\exec($shell, $ignored, $code);
        if ($code === 0 && is_link($link)) {
            $this->info("Linked via exec: {$link} -> {$target}");

            return 0;
        }
    }

    $this->error('PHP could not create the storage link (symlink/exec unavailable or failed).');
    $this->line('Run this over SSH from your project directory (adjust paths if your layout differs):');
    $this->line('cd public && rm -f storage && ln -s ../storage/app/public storage');

    return 1;
})->purpose('Create public/storage -> storage/app/public without the Laravel Filesystem namespace bug');
