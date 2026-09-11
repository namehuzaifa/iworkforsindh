<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobPostingReportExport implements FromCollection, WithHeadings
{
    public function __construct(protected $query)
    {
    }

    public function headings(): array
    {
        return ['Date', 'Job Title', 'Company Account', 'Posted By', 'Source', 'Note', 'IP'];
    }

    public function collection()
    {
        return $this->query
            ->with(['job:id,title', 'company.user:id,name', 'teamMember:id,name', 'source:id,name'])
            ->latest()
            ->get()
            ->map(fn ($log) => [
                $log->created_at?->format('d M Y H:i'),
                $log->job?->title,
                $log->company?->user?->name,
                $log->teamMember?->name,
                $log->source?->name,
                $log->source_note,
                $log->ip_address,
            ]);
    }
}
