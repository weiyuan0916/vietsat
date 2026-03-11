<?php

namespace Tests\Unit;

use App\Services\DeviceTrackingService;
use Illuminate\Http\Request;
use Tests\TestCase;

class DeviceTrackingServiceTest extends TestCase
{
    private DeviceTrackingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DeviceTrackingService();
    }

    /**
     * Test generate fingerprint returns consistent results.
     */
    public function test_generate_fingerprint_returns_consistent_results(): void
    {
        $request = Request::create('/test', 'POST');
        $request->merge([
            'ip' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 Test Browser',
        ]);

        // Set headers manually
        $request->headers->set('User-Agent', 'Mozilla/5.0 Test Browser');

        $fingerprint1 = $this->service->generateFingerprint($request);
        $fingerprint2 = $this->service->generateFingerprint($request);

        $this->assertEquals($fingerprint1, $fingerprint2);
        $this->assertNotEmpty($fingerprint1);
        $this->assertIsString($fingerprint1);
    }

    /**
     * Test generate fingerprint returns different results for different requests.
     */
    public function test_generate_fingerprint_differs_for_different_requests(): void
    {
        $request1 = Request::create('/test', 'POST');
        $request1->headers->set('User-Agent', 'Mozilla/5.0 Browser1');

        $request2 = Request::create('/test', 'POST');
        $request2->headers->set('User-Agent', 'Mozilla/5.0 Browser2');

        $fingerprint1 = $this->service->generateFingerprint($request1);
        $fingerprint2 = $this->service->generateFingerprint($request2);

        $this->assertNotEquals($fingerprint1, $fingerprint2);
    }

    /**
     * Test get device type from user agent.
     */
    public function test_get_device_type_from_user_agent(): void
    {
        // Test mobile
        $mobileRequest = Request::create('/test');
        $mobileRequest->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');

        // Reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getDeviceType');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        $this->assertEquals('Mobile', $result);

        // Test desktop
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $this->assertEquals('Desktop', $result);

        // Test tablet
        $result = $method->invoke($this->service, 'Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X)');
        $this->assertEquals('Tablet', $result);
    }

    /**
     * Test get browser from user agent.
     */
    public function test_get_browser_from_user_agent(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getBrowser');
        $method->setAccessible(true);

        // Test Chrome
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        $this->assertEquals('Chrome', $result);

        // Test Firefox
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0');
        $this->assertEquals('Firefox', $result);

        // Test Safari
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15');
        $this->assertEquals('Safari', $result);
    }

    /**
     * Test get OS from user agent.
     */
    public function test_get_os_from_user_agent(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getOS');
        $method->setAccessible(true);

        // Test Windows
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $this->assertEquals('Windows', $result);

        // Test macOS
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)');
        $this->assertEquals('macOS', $result);

        // Test iOS
        $result = $method->invoke($this->service, 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        $this->assertEquals('iOS', $result);

        // Test Android
        $result = $method->invoke($this->service, 'Mozilla/5.0 (Linux; Android 10; SM-G960U) AppleWebKit/537.36');
        $this->assertEquals('Android', $result);
    }
}

