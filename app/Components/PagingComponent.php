<?php

namespace App\Components;

class PagingComponent
{
    private $total;
    private $offset;
    private $limit;

    public function __construct($total, $offset, $limit)
    {
        $this->total = $total;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function render()
    {
        return [
            'total'        => $this->total,
            'page' => floor($this->offset / $this->limit) + 1,
            'from'         => $this->offset > 0 ? $this->offset : 1,
            'to'           => $this->offset + $this->limit,
            'limit'     => (int)$this->limit,
            'last_page'    => ceil($this->total / $this->limit),
        ];
    }
}
