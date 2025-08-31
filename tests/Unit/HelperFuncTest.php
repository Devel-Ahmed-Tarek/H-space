<?php
namespace Tests\Unit;

use App\Http\Helpers\HelperFunc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HelperFuncTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_response_returns_correct_format(): void
    {
        $response = HelperFunc::sendResponse(200, 'Success message', ['key' => 'value']);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $data['status']);
        $this->assertEquals('Success message', $data['msg']);
        $this->assertEquals(['key' => 'value'], $data['data']);
    }

    public function test_send_response_without_data(): void
    {
        $response = HelperFunc::sendResponse(400, 'Error message');

        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $data['status']);
        $this->assertEquals('Error message', $data['msg']);
        $this->assertEquals([], $data['data']);
    }

    public function test_upload_file(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $path = HelperFunc::uploadFile('documents', $file);

        $this->assertStringContainsString('uploads/documents', $path);
        $this->assertStringContainsString('.pdf', $path);
    }

    public function test_delete_file(): void
    {
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');

        $this->assertTrue(file_exists($tempFile));

        HelperFunc::deleteFile($tempFile);

        $this->assertFalse(file_exists($tempFile));
    }

    public function test_get_image_url_with_relative_path(): void
    {
        $url = HelperFunc::getImageUrl('uploads/images/photo.jpg');

        $this->assertStringContainsString('uploads/images/photo.jpg', $url);
        $this->assertStringStartsWith('http', $url);
    }

    public function test_get_image_url_with_absolute_url(): void
    {
        $absoluteUrl = 'https://example.com/image.jpg';
        $url         = HelperFunc::getImageUrl($absoluteUrl);

        $this->assertEquals($absoluteUrl, $url);
    }

    public function test_get_image_url_with_empty_value(): void
    {
        $url = HelperFunc::getImageUrl('');

        $this->assertNull($url);
    }

    public function test_get_youtube_thumbnail(): void
    {
        $videoId   = 'dQw4w9WgXcQ';
        $thumbnail = HelperFunc::getYouTubeThumbnail($videoId, 'hq');

        $expectedUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        $this->assertEquals($expectedUrl, $thumbnail);
    }

    public function test_get_youtube_thumbnail_with_empty_id(): void
    {
        $thumbnail = HelperFunc::getYouTubeThumbnail('');

        $this->assertNull($thumbnail);
    }

    public function test_format_duration_seconds(): void
    {
        $formatted = HelperFunc::formatDuration(125);
        $this->assertEquals('02:05', $formatted);
    }

    public function test_format_duration_hours(): void
    {
        $formatted = HelperFunc::formatDuration(3661);
        $this->assertEquals('01:01:01', $formatted);
    }

    public function test_format_duration_zero(): void
    {
        $formatted = HelperFunc::formatDuration(0);
        $this->assertEquals('00:00', $formatted);
    }

    public function test_parse_duration_to_seconds_mm_ss(): void
    {
        $seconds = HelperFunc::parseDurationToSeconds('02:30');
        $this->assertEquals(150, $seconds);
    }

    public function test_parse_duration_to_seconds_hh_mm_ss(): void
    {
        $seconds = HelperFunc::parseDurationToSeconds('01:30:45');
        $this->assertEquals(5445, $seconds);
    }

    public function test_prepare_google_drive_link(): void
    {
        $shareLink  = 'https://drive.google.com/file/d/1234567890/view';
        $directLink = HelperFunc::prepareGoogleDriveLink($shareLink);

        $expectedLink = 'https://drive.google.com/uc?id=1234567890&export=download';
        $this->assertEquals($expectedLink, $directLink);
    }

    public function test_prepare_google_drive_link_already_direct(): void
    {
        $directLink = 'https://drive.google.com/uc?id=1234567890&export=download';
        $result     = HelperFunc::prepareGoogleDriveLink($directLink);

        $this->assertEquals($directLink, $result);
    }

    public function test_prepare_google_drive_link_invalid(): void
    {
        $invalidLink = 'https://example.com/file.pdf';
        $result      = HelperFunc::prepareGoogleDriveLink($invalidLink);

        $this->assertEquals($invalidLink, $result);
    }

    public function test_get_pagination_params(): void
    {
        $request = request()->merge([
            'per_page' => 25,
            'page'     => 3,
        ]);

        $params = HelperFunc::getPaginationParams($request, 15);

        $this->assertEquals(25, $params['per_page']);
        $this->assertEquals(3, $params['page']);
    }

    public function test_get_pagination_params_with_defaults(): void
    {
        $request = request();

        $params = HelperFunc::getPaginationParams($request, 20);

        $this->assertEquals(20, $params['per_page']);
        $this->assertEquals(1, $params['page']);
    }

    public function test_get_pagination_params_with_limits(): void
    {
        $request = request()->merge([
            'per_page' => 150, // Should be limited to 100
            'page'     => 0,   // Should be limited to 1
        ]);

        $params = HelperFunc::getPaginationParams($request, 15);

        $this->assertEquals(100, $params['per_page']);
        $this->assertEquals(1, $params['page']);
    }

    public function test_limit_function(): void
    {
        $limit = HelperFunc::limit(25);
        $this->assertEquals(25, $limit);

        $defaultLimit = HelperFunc::limit();
        $this->assertEquals(10, $defaultLimit);
    }
}
