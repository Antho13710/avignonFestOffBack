<?php

namespace App\Tests;

use App\Service\UploaderBase64;
use PHPUnit\Framework\TestCase;

class UploaderBase64Test extends TestCase
{
    public function testUpload()
    {
        $uploader = new UploaderBase64();
        $result = $uploader->upload("data:image/jpg;base64,iVBORw0KGgoAAAANSUhEUgAAAEwAAABOCAIAAAAFPWd3AAAMb2lDQ1BEaXNwbGF5AAB", "public/upload/images/");

        $this->assertMatchesRegularExpression('/\w{13}.jpg/', $result);
    }
}
