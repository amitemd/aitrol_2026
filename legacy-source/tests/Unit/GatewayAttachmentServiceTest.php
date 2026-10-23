<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\DB;
use Sis_medico\Services\GatewayAttachmentService;

require_once __DIR__ . '/../../app/Services/GatewayAttachmentService.php';

class GatewayAttachmentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        DB::clearResolvedInstance('db');
        parent::tearDown();
    }

    private function existingEvidence()
    {
        $row = (object) ['name' => 'pantalla.png', 'download_url' => 'https://branch.example/evidence/token'];
        $database = new class($row) {
            public $row;
            public function __construct($row) { $this->row = $row; }
            public function table($name) { return $this; }
            public function where($key, $value) { return $this; }
            public function orderBy($key) { return $this; }
            public function get() { return [$this->row]; }
        };
        DB::swap($database);
        return $row;
    }

    public function testRetryKeepsTheOriginalDownloadLink(): void
    {
        $row = $this->existingEvidence();
        $result = (new GatewayAttachmentService())->prepare('SUC-test', 'user', []);
        $this->assertSame([$row], $result);
    }

    public function testRetryDoesNotReplaceSavedEvidence(): void
    {
        $this->existingEvidence();
        $this->expectException(\RuntimeException::class);
        (new GatewayAttachmentService())->prepare('SUC-test', 'user', [new \stdClass()]);
    }

    public function testNamesCannotInjectPathsOrNewDescriptionLines(): void
    {
        $name = GatewayAttachmentService::safeName("../evidencia\\foto\r\n.pdf");
        foreach (["/", "\\", "\r", "\n"] as $character) {
            $this->assertFalse(strpos($name, $character));
        }
        $this->assertSame(150, mb_strlen(GatewayAttachmentService::safeName(str_repeat('á', 200))));
    }
}
