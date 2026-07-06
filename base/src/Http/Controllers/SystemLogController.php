<?php

namespace Polirium\Core\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemLogController extends BaseController
{
    private const MAX_TAIL_BYTES = 2097152;

    private const MAX_STACK_LINES = 300;

    public function index(Request $request)
    {
        page_title()->setTitle(trans('core/base::general.system_logs'));

        $selectedFile = $this->safeLogFileName($request->string('file')->toString());
        $logFiles = $this->availableLogFiles();

        if (! $selectedFile || ! isset($logFiles[$selectedFile])) {
            $selectedFile = array_key_first($logFiles);
        }

        $level = strtoupper($request->string('level')->toString());
        $allowedLevels = ['ALL', 'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
        if (! in_array($level, $allowedLevels, true)) {
            $level = 'ERROR';
        }

        $limit = min(max((int) $request->integer('limit', 50), 10), 200);
        $entries = $selectedFile
            ? $this->readLogEntries(storage_path('logs/' . $selectedFile), $limit, $level)
            : [];

        return view('core/base::system-logs.index', compact('entries', 'level', 'limit', 'logFiles', 'selectedFile'));
    }

    public function destroy(Request $request)
    {
        $selectedFile = $this->safeLogFileName($request->string('file')->toString());

        abort_if(! $selectedFile, 404);

        $path = storage_path('logs/' . $selectedFile);

        abort_if(! File::isFile($path), 404);

        File::delete($path);

        return redirect()
            ->route('core.system-logs.index')
            ->with('success', __('core/base::general.log_deleted_successfully'));
    }

    private function availableLogFiles(): array
    {
        if (! File::isDirectory(storage_path('logs'))) {
            return [];
        }

        return collect(File::files(storage_path('logs')))
            ->filter(fn ($file) => $file->getExtension() === 'log')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->mapWithKeys(fn ($file) => [
                $file->getFilename() => [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified_at' => $file->getMTime(),
                ],
            ])
            ->all();
    }

    private function readLogEntries(string $path, int $limit, string $level): array
    {
        if (! File::isFile($path) || ! File::isReadable($path)) {
            return [];
        }

        $entries = [];
        $current = null;

        foreach ($this->tailLines($path) as $line) {
            if (preg_match('/^\[(?<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(?<env>[^.]+)\.(?<level>[A-Z]+):\s+(?<message>.*)$/', $line, $matches)) {
                if ($current && $this->matchesLevel($current['level'], $level)) {
                    $entries[] = $this->sanitizeEntry($current);
                }

                $current = [
                    'date' => $matches['date'],
                    'environment' => $matches['env'],
                    'level' => $matches['level'],
                    'message' => $matches['message'],
                    'stack' => [],
                ];

                continue;
            }

            if ($current) {
                $current['stack'][] = $line;
            }
        }

        if ($current && $this->matchesLevel($current['level'], $level)) {
            $entries[] = $this->sanitizeEntry($current);
        }

        return array_slice(array_reverse($entries), 0, $limit);
    }

    private function tailLines(string $path): array
    {
        $handle = fopen($path, 'rb');

        if (! $handle) {
            return [];
        }

        try {
            $size = filesize($path) ?: 0;
            $offset = max(0, $size - self::MAX_TAIL_BYTES);

            if ($offset > 0) {
                fseek($handle, $offset);
                fgets($handle);
            }

            $content = stream_get_contents($handle) ?: '';
        } finally {
            fclose($handle);
        }

        return array_values(array_filter(
            array_map(fn ($line) => rtrim($line, "\r\n"), preg_split('/\R/', $content) ?: []),
            fn ($line) => $line !== ''
        ));
    }

    private function matchesLevel(string $entryLevel, string $selectedLevel): bool
    {
        return $selectedLevel === 'ALL' || $entryLevel === $selectedLevel;
    }

    private function sanitizeEntry(array $entry): array
    {
        $entry['message'] = $this->sanitizeText($entry['message']);
        $stackLineCount = count($entry['stack']);
        $entry['stack'] = array_map(
            fn ($line) => $this->sanitizeText($line),
            array_slice($entry['stack'], 0, self::MAX_STACK_LINES)
        );

        if ($stackLineCount > self::MAX_STACK_LINES) {
            $entry['stack'][] = '[stack trace truncated]';
        }

        return $entry;
    }

    private function sanitizeText(string $text): string
    {
        $patterns = [
            '/("?(?:password|passwd|secret|token|api_key|app_key|authorization)"?\s*[:=]\s*)("[^"]*"|\'[^\']*\'|[^\s,}]+)/i',
            '/(Bearer\s+)[A-Za-z0-9._\-]+/i',
        ];

        return preg_replace($patterns, '$1[redacted]', $text) ?? $text;
    }

    private function safeLogFileName(string $file): ?string
    {
        if ($file === '') {
            return null;
        }

        $basename = basename($file);

        return str_ends_with($basename, '.log') ? $basename : null;
    }
}
