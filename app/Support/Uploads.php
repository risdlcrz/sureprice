<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Upload limits from config/uploads.php, as validation rules + messages (server) and input attributes (browser).
 *
 *   $proof = Uploads::file('payment_proof', 'documents', required: true, maxKb: 5120, label: 'Proof of payment');
 *   $photos = Uploads::files('images', 'images', maxKb: 2048, label: 'Images');
 *   $request->validate([...other rules, ...$proof->rules, ...$photos->rules], [...$proof->messages, ...$photos->messages]);
 *
 *   <input type="file" name="images[]" multiple {{ Uploads::attributes('images', maxFiles: 5, maxKb: 2048) }}>
 *
 * Types are checked twice on the server: the file name's extension (`extensions`) and the detected content
 * (`mimes`), so a renamed .exe or .html is rejected. Limits can be stricter than the config, never looser.
 */
final class Uploads
{
    public function __construct(public readonly array $rules, public readonly array $messages)
    {
    }

    /** One file in $field. */
    public static function file(string $field, string $kind, bool $required = false, ?int $maxKb = null, ?string $label = null): self
    {
        $maxKb = self::maxKb($maxKb);
        $label ??= 'The file';

        return new self(
            [$field => [$required ? 'required' : 'nullable', 'file', ...self::typeRules($kind), 'max:' . $maxKb]],
            [
                "$field.required" => "Please choose a file for {$label}.",
                "$field.file" => "{$label} must be a file.",
                "$field.uploaded" => self::uploadFailed($label),
                "$field.extensions" => "{$label} must be " . self::describeTypes($kind) . '.',
                "$field.mimes" => "{$label} must be " . self::describeTypes($kind) . '.',
                "$field.max" => "{$label} must not be larger than " . self::mb($maxKb) . '.',
            ],
        );
    }

    /** Several files in $field (an array input, name="field[]"). */
    public static function files(string $field, string $kind, ?int $maxFiles = null, ?int $maxKb = null, bool $required = false, ?string $label = null): self
    {
        $maxKb = self::maxKb($maxKb);
        $maxFiles = self::maxFiles($maxFiles);
        $label ??= 'Files';

        return new self(
            [
                $field => [$required ? 'required' : 'nullable', 'array', 'max:' . $maxFiles],
                "$field.*" => ['file', ...self::typeRules($kind), 'max:' . $maxKb],
            ],
            [
                "$field.required" => "Please choose at least one file for {$label}.",
                "$field.array" => "{$label} must be a list of files.",
                "$field.max" => "You can upload at most {$maxFiles} " . ($maxFiles === 1 ? 'file' : 'files') . " for {$label}.",
                "$field.*.file" => "Each file in {$label} must be a file.",
                "$field.*.uploaded" => self::uploadFailed('A file in ' . $label),
                "$field.*.extensions" => "Each file in {$label} must be " . self::describeTypes($kind) . '.',
                "$field.*.mimes" => "Each file in {$label} must be " . self::describeTypes($kind) . '.',
                "$field.*.max" => "Each file in {$label} must not be larger than " . self::mb($maxKb) . '.',
            ],
        );
    }

    /** Attributes for <input type="file">: accept + the data-upload-* limits read by resources/js/upload-limits.js. */
    public static function attributes(string $kind, int $maxFiles = 1, ?int $maxKb = null): HtmlString
    {
        return new HtmlString(collect(self::attributeArray($kind, $maxFiles, $maxKb))->map(fn ($v, $k) => $k . '="' . e($v) . '"')->implode(' '));
    }

    /** The same attributes as an array (for components such as <x-field upload="documents">). */
    public static function attributeArray(string $kind, int $maxFiles = 1, ?int $maxKb = null): array
    {
        $maxKb = self::maxKb($maxKb);
        $maxFiles = self::maxFiles($maxFiles);
        $types = self::types($kind);

        return [
            'accept' => implode(',', array_map(fn ($ext) => '.' . $ext, $types)),
            'data-upload-types' => implode(',', $types),
            'data-upload-max-kb' => $maxKb,
            'data-upload-max-files' => $maxFiles,
            'data-upload-hint' => self::hint($kind, $maxFiles, $maxKb),
        ];
    }

    /** "JPG, PNG, WEBP or PDF · up to 10 MB each · up to 5 files" */
    public static function hint(string $kind, int $maxFiles = 1, ?int $maxKb = null): string
    {
        $maxKb = self::maxKb($maxKb);
        $maxFiles = self::maxFiles($maxFiles);

        return self::typeList($kind) . ' · up to ' . self::mb($maxKb) . ($maxFiles > 1 ? ' each · up to ' . $maxFiles . ' files' : '');
    }

    /** The smallest of: the requested limit, config/uploads.php, and what PHP will accept (upload_max_filesize / post_max_size). */
    public static function maxKb(?int $requested = null): int
    {
        return (int) min(array_filter([$requested, (int) config('uploads.max_kb'), self::serverMaxKb()]));
    }

    /** What PHP accepts per file, in KB (null if unlimited). */
    public static function serverMaxKb(): ?int
    {
        $limits = array_filter([self::iniKb('upload_max_filesize'), self::iniKb('post_max_size')]);

        return $limits ? (int) min($limits) : null;
    }

    public static function types(string $kind): array
    {
        return config("uploads.types.$kind") ?? throw new \InvalidArgumentException("Unknown upload kind [$kind].");
    }

    private static function maxFiles(?int $requested): int
    {
        $limit = (int) config('uploads.max_files');
        $server = (int) ini_get('max_file_uploads');

        return max(1, min(array_filter([$requested, $limit, $server ?: null])));
    }

    private static function typeRules(string $kind): array
    {
        $list = implode(',', self::types($kind));

        return ['extensions:' . $list, 'mimes:' . $list];
    }

    private static function typeList(string $kind): string
    {
        // jpeg is shown as JPG; txt only exists so CSV files saved as text pass the content check
        $names = collect(self::types($kind))->reject(fn ($ext) => in_array($ext, ['jpeg', 'txt'], true))->map(fn ($ext) => strtoupper($ext))->values();

        return $names->count() > 1 ? $names->slice(0, -1)->implode(', ') . ' or ' . $names->last() : (string) $names->first();
    }

    private static function describeTypes(string $kind): string
    {
        return 'a ' . self::typeList($kind) . ' file';
    }

    private static function uploadFailed(string $label): string
    {
        return "{$label} could not be uploaded. Make sure it is no larger than " . self::mb(self::maxKb()) . ' and try again.';
    }

    private static function mb(int $kb): string
    {
        $mb = $kb / 1024;

        return (floor($mb) == $mb ? (int) $mb : round($mb, 1)) . ' MB';
    }

    private static function iniKb(string $key): ?int
    {
        $value = trim((string) ini_get($key));
        if ($value === '' || $value === '0' || $value === '-1') {
            return null;
        }
        $number = (float) $value;

        return (int) match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 * 1024,
            'm' => $number * 1024,
            'k' => $number,
            default => $number / 1024,
        };
    }
}
