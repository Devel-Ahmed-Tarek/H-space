<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class HelperFunc
{

    public static function uploadFile($path, $file)
    {
        try {
            // Validate file
            if (! $file || ! $file->isValid()) {
                throw new \Exception('Invalid file');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $name      = time() . rand(100, 999) . '.' . $extension;

            // Create directory if it doesn't exist
            $uploadPath = 'uploads/' . $path;
            if (! file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Use move method directly for better control
            $result = $file->move($uploadPath, $name);

            if (! $result) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Return the full path that matches the actual file location
            $fullPath = $uploadPath . '/' . $name;

            // Verify file exists after move
            if (! file_exists($fullPath)) {
                throw new \Exception('File not found after upload');
            }

            return $fullPath;
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('File upload failed: ' . $e->getMessage(), [
                'path'          => $path,
                'original_name' => $file ? $file->getClientOriginalName() : 'unknown',
                'error'         => $e->getMessage(),
            ]);

            throw new \Exception('فشل في رفع الملف: ' . $e->getMessage());
        }
    }

    public static function deleteFile($file)
    {
        if (file_exists(filename: $file) && is_file($file)) {
            unlink($file); // Delete the file
        }
    }

    public static function limit($limit = 10)
    {
        return $limit;
    }

    public static function sendResponse($code = 200, $msg = null, $data = [])
    {
        $response = [
            'status' => $code,
            'msg'    => $msg,
            'data'   => $data,
        ];
        return response()->json($response, $code);
    }

    public static function pagination($itme, $Resource)
    {
        if (count($itme) > 0) {

            $data = [
                'rows'            => $Resource,
                'paginationLinks' => [
                    'currentPages' => $itme->currentPage(),
                    'perPage'      => $itme->lastpage(),
                    'total'        => $itme->total(),
                    'links'        => [
                        'first' => $itme->url(1),
                        'last'  => $itme->url($itme->lastpage()),
                    ],
                ],
            ];

            return HelperFunc::sendResponse(200, 'تم بنجاح', $data);
        }
        return HelperFunc::sendResponse(200, 'لايوجد بيانات ', []);
    }

    public static function paginationNew($item, $data)
    {
        return [
            'data'      => $item,
            'paination' => [
                'currentPages' => $data->currentPage(),
                'perPage'      => $data->lastpage(),
                'total'        => $data->total(),
                'links'        => [
                    'first' => $data->url(1),
                    'last'  => $data->url($data->lastpage()),
                ],
            ],
        ];
    }

    public static function getLinkAfterUploads(string $url): string
    {
        $keyword = 'uploads';

        // البحث عن موضع الكلمة "uploads" في النص
        $position = strpos($url, $keyword);

        if ($position === false) {
            // إذا لم يتم العثور على الكلمة "uploads"، يمكننا إرجاع النص الأصلي أو رسالة فارغة
            return $url;
        }

        // استخراج النص بعد "uploads/"
        $path = substr($url, $position + strlen($keyword) + 1);

        // إضافة "uploads/" في بداية المسار
        return $keyword . '/' . ltrim($path, '/');
    }

    public static function getLocalizedImage(?array $images): ?string
    {
        if (is_null($images)) {
            return null;
        }

        // Get the current application locale
        $locale = app()->getLocale();

        // Return the image for the current locale or a default locale (fallback)
        return $images[$locale] ?? $images['en'] ?? null;
    }

    public static function getImageUrl($image)
    {
        if (empty($image)) {
            return null;
        }

        // Already absolute (http/https) or data URI
        if (preg_match('#^(?:https?:)?//#', $image) || str_starts_with($image, 'data:')) {
            return $image;
        }

        // Normalize leading slash
        $path = ltrim($image, '/');

        // Map public/ to storage/ for files saved on the public disk
        if (str_starts_with($path, 'public/')) {
            $path = 'storage/' . substr($path, strlen('public/'));
        }

        // If the path contains uploads somewhere, reduce it to start at uploads/
        if (! str_starts_with($path, 'uploads/') && ! str_starts_with($path, 'upload/') && ! str_starts_with($path, 'storage/')) {
            $afterUploads = self::getLinkAfterUploads($path);
            if (! empty($afterUploads) && $afterUploads !== $path) {
                $path = $afterUploads;
            }
        }

        return asset($path);
    }

    /**
     * Extract YouTube thumbnail URL from video ID
     * @param string $videoId YouTube video ID
     * @param string $quality thumbnail quality (default, hq, mq, sd, maxres)
     * @return string|null
     */
    public static function getYouTubeThumbnail($videoId, $quality = 'hq')
    {
        if (empty($videoId)) {
            return null;
        }

        $qualities = [
            'default' => 'default.jpg',
            'hq'      => 'hqdefault.jpg',
            'mq'      => 'mqdefault.jpg',
            'sd'      => 'sddefault.jpg',
            'maxres'  => 'maxresdefault.jpg',
        ];

        $quality = $qualities[$quality] ?? $qualities['hq'];

        return "https://img.youtube.com/vi/{$videoId}/{$quality}";
    }

    /**
     * Enhanced pagination function with per_page support
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function paginateResponse($paginator, $message = 'Data retrieved successfully')
    {
        if ($paginator->count() > 0) {
            $data = [
                'data'       => $paginator->items(),
                'pagination' => [
                    'current_page'   => $paginator->currentPage(),
                    'per_page'       => $paginator->perPage(),
                    'total'          => $paginator->total(),
                    'last_page'      => $paginator->lastPage(),
                    'from'           => $paginator->firstItem(),
                    'to'             => $paginator->lastItem(),
                    'has_more_pages' => $paginator->hasMorePages(),
                    'links'          => [
                        'first' => $paginator->url(1),
                        'last'  => $paginator->url($paginator->lastPage()),
                        'prev'  => $paginator->previousPageUrl(),
                        'next'  => $paginator->nextPageUrl(),
                    ],
                ],
            ];

            return self::sendResponse(200, $message, $data);
        }

        return self::sendResponse(200, 'No data found', [
            'data'       => [],
            'pagination' => [
                'current_page'   => 1,
                'per_page'       => 10,
                'total'          => 0,
                'last_page'      => 1,
                'from'           => null,
                'to'             => null,
                'has_more_pages' => false,
                'links'          => [
                    'first' => null,
                    'last'  => null,
                    'prev'  => null,
                    'next'  => null,
                ],
            ],
        ]);
    }

    /**
     * Get pagination parameters from request
     * @param \Illuminate\Http\Request $request
     * @param int $defaultPerPage
     * @return array
     */
    public static function getPaginationParams($request, $defaultPerPage = 10)
    {
        $perPage = $request->get('per_page', $defaultPerPage);
        $page    = $request->get('page', 1);

                                                     // Validate per_page limits
        $perPage = max(1, min(100, (int) $perPage)); // Between 1 and 100
        $page    = max(1, (int) $page);

        return [
            'per_page' => $perPage,
            'page'     => $page,
        ];
    }

    public static function formatDuration($seconds)
    {
        if (! $seconds) {
            return '00:00';
        }

        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs    = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Parse duration string to seconds
     */
    public static function parseDurationToSeconds($duration)
    {
        if (preg_match('/^(\d+):(\d+):(\d+)$/', $duration, $matches)) {
            // HH:MM:SS format
            return ($matches[1] * 3600) + ($matches[2] * 60) + $matches[3];
        } elseif (preg_match('/^(\d+):(\d+)$/', $duration, $matches)) {
            // MM:SS format
            return ($matches[1] * 60) + $matches[2];
        }

        return 0;
    }

    /**
     * Upload large files using move method (same as uploadFile but for videos)
     * @param string $path The upload directory path
     * @param \Illuminate\Http\UploadedFile $file The uploaded file
     * @return string The uploaded file path
     */
    public static function uploadFileInChunks($path, $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name      = time() . rand(100, 999) . '.' . $extension;
        return (string) $file->move('uploads/' . $path, $name);
    }

    /**
     * Prepare Google Drive video link (convert to direct link if needed)
     *
     * @param string $url
     * @return string|null
     */
    public static function prepareGoogleDriveLink(string $url): ?string
    {
        // If URL is empty or null, return null
        if (empty($url)) {
            return null;
        }

        // Direct link format already
        if (preg_match('/^https:\/\/drive\.google\.com\/uc\?id=([a-zA-Z0-9_-]+)&export=download$/', $url)) {
            return $url;
        }

        // Standard share link format
        if (preg_match('/^https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
            return "https://drive.google.com/uc?id={$fileId}&export=download";
        }

        // Not valid Google Drive link, return original URL
        return $url;
    }
}
