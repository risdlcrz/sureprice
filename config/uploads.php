<?php

/*
 * App-wide upload limits, used by App\Support\Uploads for server-side validation and for the
 * data-upload-* attributes that resources/js/upload-limits.js checks in the browser.
 *
 * An upload can be stricter than these (smaller max_kb, fewer files, a narrower type group) but never looser.
 * The server must also accept the request: PHP upload_max_filesize / post_max_size and max_file_uploads
 * (XAMPP default here: 40M / 40M / 20) must stay >= max_kb and max_files x max_kb.
 */
return [
    // Largest file accepted, in kilobytes (10 MB)
    'max_kb' => (int) env('UPLOAD_MAX_KB', 10240),

    // Most files in one upload field
    'max_files' => (int) env('UPLOAD_MAX_FILES', 5),

    // Allowed extensions per kind of upload. Checked against both the file name and the detected content type.
    'types' => [
        'images' => ['jpg', 'jpeg', 'png', 'webp'],
        'documents' => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
        'attachments' => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'csv' => ['csv', 'txt'],
    ],
];
