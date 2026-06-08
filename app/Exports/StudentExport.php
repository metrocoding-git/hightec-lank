<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $branch_id;
    protected $intake_id;
    protected $course_id;

    public function __construct($branch_id = null, $intake_id = null, $course_id = null)
    {
        $this->branch_id = $branch_id;
        $this->intake_id = $intake_id;
        $this->course_id = $course_id;
    }

    public function query()
    {
        $query = Student::with([
            'branch',
            'gender',
            'studentCategory',
            'district',
            'createUser',
            'updateUser'
        ])->active();

        // Apply filters if provided
        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }

        if ($this->intake_id) {
            $query->whereHas('studentCourses', function ($q) {
                $q->where('intake_id', $this->intake_id);
            });
        }

        if ($this->course_id) {
            $query->whereHas('studentCourses', function ($q) {
                $q->where('course_id', $this->course_id);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Admission Number',
            'Full Name',
            'Phone 1',
            'Phone 2',
            'NIC',
            'Address',
            'Branch',
            'Admission Date',
            'Category',
            'Gender',
            'Date of Birth',
            'Age',
            'Email',
            'District',
            'Full Name Verified',
            'Phone 1 Verified',
            'Phone 2 Verified',
            'NIC Verified',
            'Address Verified',
            'Notes',
            'Previous Institute',
            'Created By',
            'Updated By',
            'Created Date',
            'Updated Date',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->admission_number ?? '-',
            $student->student_name ?? '-',
            $student->phone_number ?? '-',
            $student->phone_number_2 ?? '-',
            $student->national_id_number ?? '-',
            $student->permanent_address ?? '-',
            $student->branch->name ?? '-',
            $student->admission_date ?? '-',
            $student->studentCategory->category_name ?? '-',
            $student->gender->name ?? '-',
            $student->date_of_birth ?? '-',
            $student->stu_age ?? '-',
            $student->email_address ?? '-',
            $student->district->name ?? '-',
            $student->isVerified('Full Name Verified') ? 'YES' : 'NO',
            $student->isVerified('Mobile Number Verified') ? 'YES' : 'NO',
            $student->isVerified('Mobile Number 2 Verified') ? 'YES' : 'NO',
            $student->isVerified('NIC Verified') ? 'YES' : 'NO',
            $student->isVerified('Address Verified') ? 'YES' : 'NO',
            $student->additional_notes ?? '-',
            $student->previous_institute_details ?? '-',
            $student->createUser->name ?? '-',
            $student->updateUser->name ?? '-',
            $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : '-',
            $student->updated_at ? $student->updated_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set header row style
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(10);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(15);
        $sheet->getColumnDimension('S')->setWidth(12);
        $sheet->getColumnDimension('T')->setWidth(12);
        $sheet->getColumnDimension('U')->setWidth(25);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(15);
        $sheet->getColumnDimension('X')->setWidth(15);
        $sheet->getColumnDimension('Y')->setWidth(18);
        $sheet->getColumnDimension('Z')->setWidth(18);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}