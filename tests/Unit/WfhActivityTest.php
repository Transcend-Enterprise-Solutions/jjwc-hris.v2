<?php

namespace Tests\Unit;

use App\Support\WfhActivity;
use PHPUnit\Framework\TestCase;

class WfhActivityTest extends TestCase
{
    public function test_it_identifies_common_browsers(): void
    {
        $this->assertSame('Google Chrome', WfhActivity::browserName('Mozilla/5.0 Chrome/125.0 Safari/537.36'));
        $this->assertSame('Microsoft Edge', WfhActivity::browserName('Mozilla/5.0 Chrome/125.0 Edg/125.0'));
        $this->assertSame('Safari', WfhActivity::browserName('Mozilla/5.0 Version/17.0 Safari/605.1.15'));
    }

    public function test_it_creates_readable_hris_page_labels(): void
    {
        $this->assertSame('Opened HRIS Home', WfhActivity::pageLabel('JJWC HRIS - Home', 'https://example.test/home'));
        $this->assertSame('Opened Daily Time Record', WfhActivity::pageLabel('Records', 'https://example.test/daily-time-record'));
        $this->assertSame('Opened My Profile', WfhActivity::pageLabel('My Profile | JJWC HRIS', 'https://example.test/profile'));
    }

    public function test_page_keys_ignore_query_string_changes(): void
    {
        $this->assertSame(
            WfhActivity::pageKey('https://example.test/home?page=1'),
            WfhActivity::pageKey('https://example.test/home?page=2')
        );
    }
}
