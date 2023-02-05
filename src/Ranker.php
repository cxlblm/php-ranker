<?php

namespace Cxlblm\PhpRanker;

class Ranker
{
    /**
     * @var float
     */
    public $epsinon = 1e-7;

    /**
     * @var int 0 null as null, 1 null as ranking(int)
     */
    public $nullMode = 0;

    /**
     * @param array<array-key, int|float|string|null> $data
     * @param 'asc'|'desc' $mode
     * @return array<array-key, int<1,max>|null>
     */
    public function rank($data, $mode = 'desc')
    {
        $this->sort($data, $mode);
        $lastRank = $rank = 1;
        $last = null;
        $first = true;
        $r = [];
        foreach ($data as $key => $datum) {
            if ($this->nullMode == 0 && null === $datum) {
                $r[$key] = null;
            } else {
                if (!$first && !self::equal($datum, $last)) {
                    $rank = $lastRank;
                }
                $r[$key] = $rank;
                $lastRank++;
            }
            $first = false;
            $last = $datum;
        }

        return $r;
    }

    /**
     * @param array<array-key, int|float|string|null> $data
     * @param 'asc'|'desc' $mode
     * @return array<array-key, int<1,max>|null>
     */
    public function denseRank($data, $mode = 'desc')
    {
        $this->sort($data, $mode);
        $rank = 1;
        $last = null;
        $first = true;
        $r = [];
        foreach ($data as $key => $datum) {
            if ($this->nullMode == 0 && null === $datum) {
                $r[$key] = null;
            } else {
                if (!$first && !self::equal($datum, $last)) {
                    ++$rank;
                }

                $r[$key] = $rank;
            }
            $last = $datum;
            $first = false;
        }

        return $r;
    }

    /**
     * @param array<array-key, int|float|string|null> $data
     * @param 'asc'|'desc' $mode
     * @return array<array-key, int<1,max>|null>
     */
    public function sequence($data, $mode = 'desc')
    {
        $this->sort($data, $mode);
        $rank = 1;
        $r = [];
        foreach ($data as $key => $datum) {
            if ($this->nullMode == 0 && null === $datum) {
                $r[$key] = null;
            } else {
                $r[$key] = $rank;
                ++$rank;
            }
        }

        return $r;
    }

    /**
     * @param array<array-key, array<array-key, int|float|string|null>> $data
     * @param array<int, string>|string $fields
     * @param string $key
     * @param 'asc'|'desc' $mode
     * @return array<array-key, int<1,max>|null>
     */
    public function rankMulti($data, $fields, $key, $mode = 'desc')
    {
        $this->multiSort($data, $fields, $mode);
        $lastRank = $rank = 1;
        $last = null;
        $first = true;
        $r = [];
        foreach ($data as $datum) {
            if (null !== $datum) {
                if (!$first && !$this->arrayEqual($datum, $last, $fields)) {
                    $rank = $lastRank;
                }
                $r[$datum[$key]] = $rank;
                $lastRank++;
            }

            $last = $datum;
            $first = false;
        }

        return $r;
    }

    /**
     * @param array<array-key, array<array-key, int|float|string|null>> $data
     * @param array<int, string>|string $fields
     * @param string $uniKey
     * @param string $rankingKey
     * @param 'asc'|'desc' $mode
     * @return array<array-key, array<array-key, int|float|string|null>>
     */
    public function rankMultiInject($data, $fields, $uniKey, $rankingKey = 'ranking', $mode = 'desc')
    {
        if (empty($data)) {
            return $data;
        }
        $clone = $data;
        $this->multiSort($clone, $fields, $mode);
        $lastRank = $rank = 1;
        $last = null;
        $first = true;
        $cloneRankingByUni = [];
        foreach ($clone as $datum) {
            if (!$first && !$this->arrayEqual($datum, $last, $fields)) {
                $rank = $lastRank;
            }
            $cloneRankingByUni[$datum[$uniKey]] = $rank;
            $lastRank++;
            $last = $datum;
            $first = false;
        }

        foreach ($data as $key => $datum) {
            $data[$key][$rankingKey] = $cloneRankingByUni[$datum[$uniKey]];
        }

        return $data;
    }

    /**
     * @param array<array-key, array<array-key, int|float|string|null>> $data
     * @param array<int, string>|string $fields
     * @param string $uniKey
     * @param string $rankingKey
     * @param 'asc'|'desc' $mode
     * @return void
     */
    public function rankMultiRefInject(&$data, $fields, $uniKey, $rankingKey = 'ranking', $mode = 'desc')
    {
        if (empty($data)) {
            return;
        }
        $clone = $data;
        $this->multiSort($clone, $fields, $mode);
        $lastRank = $rank = 1;
        $last = null;
        $first = true;
        $cloneRankingByUni = [];
        foreach ($clone as $datum) {
            if (!$first && !$this->arrayEqual($datum, $last, $fields)) {
                $rank = $lastRank;
            }
            $cloneRankingByUni[$datum[$uniKey]] = $rank;
            $lastRank++;
            $last = $datum;
            $first = false;
        }

        foreach ($data as $key => $datum) {
            $data[$key][$rankingKey] = $cloneRankingByUni[$datum[$uniKey]];
        }
    }

    /**
     * @param array<array-key, array<array-key, int|float|string|null>> $data
     * @param array<int, string>|string $fields
     * @param 'asc'|'desc' $mode
     * @return void
     */
    private function multiSort(&$data, $fields, $mode = 'desc')
    {
        $args = [];
        foreach ((array)$fields as $field) {
            $args[] = array_column($data, $field);
            $args[] = $mode == 'desc' ? SORT_DESC : SORT_ASC;
        }
        $args[] = &$data;

        call_user_func_array('array_multisort', $args);
    }


    /**
     * @param array{int|string|float} $data
     * @param 'asc'|'desc' $mode
     * @return void
     */
    private function sort(&$data, $mode = 'desc')
    {
        if ($mode == 'desc') {
            arsort($data);
        } else {
            asort($data);
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private function equal($a, $b)
    {
        if (null === $a || null === $b) {
            return $a === $b;
        }
        if (is_numeric($a) || is_numeric($b)) {
            return ($a > $b ? $a - $b : $b - $a) < $this->epsinon;
        }

        return $a === $b;
    }

    /**
     * @param array<array-key, string|int|float> $a
     * @param array<array-key, string|int|float> $b
     * @param array<int, string>|string $keys
     * @return bool
     */
    private function arrayEqual($a, $b, $keys)
    {
        foreach ((array)$keys as $key) {
            if (!$this->equal($a[$key], $b[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return Ranker
     */
    public static function make()
    {
        return new self();
    }
}
