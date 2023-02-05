<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Cxlblm\PhpRanker\Ranker;

class RankerTest extends TestCase
{
    /** @var Ranker */
    public $ranker;

    /**
     * @return Ranker
     */
    public function genRanker()
    {
        return Ranker::make();
    }

    /**
     * @return void
     */
    public function testIntRankDesc()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => 15,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->rank($arr);
        $this->assertSame(
            [
                'd' => 1,
                'a' => 2,
                'b' => 2,
                'c' => 2,
                'e' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntRankDescHasNull()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => null,
            'd' => 35,
            'e' => 5,
            'f' => -2,
        ];
        $r = $this->genRanker()->rank($arr);
        $this->assertSame(
            [
                'd' => 1,
                'a' => 2,
                'b' => 2,
                'e' => 4,
                'f' => 5,
                'c' => null,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntRankAsc()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => 15,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->rank($arr, 'asc');
        $this->assertSame(
            [
                'e' => 1,
                'a' => 2,
                'b' => 2,
                'c' => 2,
                'd' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntRankAscHasNull()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => -11,
            'd' => 35,
            'e' => 5,
            'f' => null
        ];
        $r = $this->genRanker()->rank($arr, 'asc');
        $this->assertSame(
            [
                'f' => null,
                'c' => 1,
                'e' => 2,
                'a' => 3,
                'b' => 3,
                'd' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntRankAscHasNullAndNullValid()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => -11,
            'd' => 35,
            'e' => 5,
            'f' => null
        ];
        $ranker = $this->genRanker();
        $ranker->nullMode = 1;
        $r = $ranker->rank($arr, 'asc');
        $this->assertSame(
            [
                'f' => 1,
                'c' => 2,
                'e' => 3,
                'a' => 4,
                'b' => 4,
                'd' => 6,

            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntDenseRankDesc()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => 15,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->denseRank($arr);
        $this->assertSame(
            [
                'd' => 1,
                'a' => 2,
                'b' => 2,
                'c' => 2,
                'e' => 3,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntDenseRankDesc1()
    {
        $arr = [
            'a' => 15,
            'b' => 13,
            'c' => -1,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->denseRank($arr);
        $this->assertSame(
            [
                'd' => 1,
                'a' => 2,
                'b' => 3,
                'e' => 4,
                'c' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntDenseRankAsc()
    {
        $arr = [
            'a' => 15,
            'b' => 15,
            'c' => 15,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->denseRank($arr, 'asc');
        $this->assertSame(
            [
                'e' => 1,
                'a' => 2,
                'b' => 2,
                'c' => 2,
                'd' => 3,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntDenseRankAsc1()
    {
        $arr = [
            'a' => 0,
            'b' => 15,
            'c' => 13,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->denseRank($arr, 'asc');
        $this->assertSame(
            [
                'a' => 1,
                'e' => 2,
                'c' => 3,
                'b' => 4,
                'd' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntSequenceDesc()
    {
        $arr = [
            'a' => 0,
            'b' => 15,
            'c' => 13,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->sequence($arr);
        $this->assertSame(
            [
                'd' => 1,
                'b' => 2,
                'c' => 3,
                'e' => 4,
                'a' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntSequenceDesc1()
    {
        $arr = [
            'a' => 0,
            'b' => 15,
            'c' => 15,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->sequence($arr);
        $this->assertSame(
            [
                'd' => 1,
                'b' => 2,
                'c' => 3,
                'e' => 4,
                'a' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testIntSequenceAsc()
    {
        $arr = [
            'a' => 0,
            'b' => 15,
            'c' => 13,
            'd' => 35,
            'e' => 5,
        ];
        $r = $this->genRanker()->sequence($arr, 'asc');
        $this->assertSame(
            [
                'a' => 1,
                'e' => 2,
                'c' => 3,
                'b' => 4,
                'd' => 5,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testRankMultiDesc()
    {
        $arr = [
            ['id' => 1, 'name' => 'a', 'score' => 10.0],
            ['id' => 2, 'name' => 'b', 'score' => 322.2],
            ['id' => 3, 'name' => 'c', 'score' => 233.2],
            ['id' => 4, 'name' => 'd', 'score' => -111.2],
        ];
        $r = $this->genRanker()->rankMulti($arr, 'score', 'id');
        $this->assertSame(
            [
                2 => 1,
                3 => 2,
                1 => 3,
                4 => 4,
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testRankMultiInjectDesc()
    {
        $arr = [
            ['id' => 1, 'name' => 'a', 'score' => 10.0],
            ['id' => 2, 'name' => 'b', 'score' => 322.2],
            ['id' => 3, 'name' => 'c', 'score' => 233.2],
            ['id' => 4, 'name' => 'd', 'score' => -111.2],
        ];
        $r = $this->genRanker()->rankMultiInject($arr, 'score', 'id');
        $this->assertSame(
            [
                ['id' => 1, 'name' => 'a', 'score' => 10.0, 'ranking' => 3],
                ['id' => 2, 'name' => 'b', 'score' => 322.2, 'ranking' => 1],
                ['id' => 3, 'name' => 'c', 'score' => 233.2, 'ranking' => 2],
                ['id' => 4, 'name' => 'd', 'score' => -111.2, 'ranking' => 4],
            ],
            $r
        );
    }

    /**
     * @return void
     */
    public function testRankMultiRefInjectDesc()
    {
        $arr = [
            ['id' => 1, 'name' => 'a', 'score' => 10.0],
            ['id' => 2, 'name' => 'b', 'score' => 322.2],
            ['id' => 3, 'name' => 'c', 'score' => 233.2],
            ['id' => 4, 'name' => 'd', 'score' => -111.2],
        ];
        $this->genRanker()->rankMultiRefInject($arr, 'score', 'id');
        $this->assertSame(
            [
                ['id' => 1, 'name' => 'a', 'score' => 10.0, 'ranking' => 3],
                ['id' => 2, 'name' => 'b', 'score' => 322.2, 'ranking' => 1],
                ['id' => 3, 'name' => 'c', 'score' => 233.2, 'ranking' => 2],
                ['id' => 4, 'name' => 'd', 'score' => -111.2, 'ranking' => 4],
            ],
            $arr
        );
    }
}