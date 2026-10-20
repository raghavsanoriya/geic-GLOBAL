<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmProfileEntry extends Model
{
    use HasFactory;

    public const FIELDS = [
        'education' => ['institution' => 'Institution / school', 'qualification' => 'Qualification', 'subject' => 'Subject / specialisation', 'country' => 'Country', 'start_year' => 'Start year', 'passing_year' => 'Passing year', 'result' => 'Percentage / CGPA', 'grading_scale' => 'Grading scale', 'backlogs' => 'Backlogs', 'remarks' => 'Remarks'],
        'employment' => ['employer' => 'Employer', 'designation' => 'Designation', 'country' => 'Country', 'started_on' => 'Started on', 'ended_on' => 'Ended on (blank if current)', 'work_type' => 'Work type', 'responsibilities' => 'Responsibilities / experience', 'remarks' => 'Remarks'],
        'test_score' => ['test' => 'Test (IELTS, PTE, TOEFL, etc.)', 'taken_on' => 'Test date', 'expires_on' => 'Expiry date', 'overall' => 'Overall score', 'listening' => 'Listening', 'reading' => 'Reading', 'writing' => 'Writing', 'speaking' => 'Speaking', 'remarks' => 'Remarks'],
        'preference' => ['country' => 'Country', 'university' => 'Preferred university', 'campus' => 'Preferred campus', 'course' => 'Course', 'study_level' => 'Study level', 'intake' => 'Intake', 'budget' => 'Budget and currency', 'remarks' => 'Remarks'],
    ];

    protected $fillable = ['creation_key', 'student_id', 'kind', 'details', 'is_active', 'created_by', 'version'];

    protected function casts(): array
    {
        return ['details' => 'array', 'is_active' => 'boolean', 'version' => 'integer'];
    }
}
