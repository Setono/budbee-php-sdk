<?php

declare(strict_types=1);

namespace Setono\Budbee\Exception;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ResponseExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_exception_if_status_code_is_below_200(): void
    {
        $this->expectException(ResponseException::class);
        ResponseException::assertStatusCode(new Response(199));
    }

    /**
     * @test
     */
    public function it_throws_exception_if_status_code_is_above_299(): void
    {
        $this->expectException(ResponseException::class);
        ResponseException::assertStatusCode(new Response(300));
    }
}
