<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use stdClass;

class RoadV2Export implements FromCollection, WithMapping, WithHeadings, WithColumnWidths
{
    /**
     * @var stdClass
     */
    private $responses = null;

    /**
     * @var int
     */
    private $i = 0;

    /**
     * @param $responses
     */
    public function __construct($responses)
    {
        $this->responses = $responses;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $result_excel = $this->responses;
        return collect($result_excel);
    }

    /**
     * Heading
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'STT',
            'Khu vực',
            'Loại xe',
            'Thời gian xuất bến',
            'Từ',
            'Tới',
            'Đơn giá',
        ];
    }

    /**
     * columnWidths
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
        ];
    }

    /**
     * Map
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        $total = count($this->responses) + 1;
        return [
            $total - (++$this->i),
            $row->road_name,
            $row->category['name'] ?? '',
            $row->start,
            $row->location_start,
            $row->location_end,
            $row->cost

        ];
    }
}
