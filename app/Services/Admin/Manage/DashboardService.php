<?php

namespace App\Services\Admin\Manage;

use App\Models\Project;
use App\Models\Activity;
use App\Models\Department;
use App\Models\Strategic;
use App\Models\Year;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    use Utils;

    public function getYearDashborad($id)
    {
        $project_total = Project::where('id_year', $id)->count();

        $projects_report = Project::where('id_year', $id)
            ->where('status_report', '1')
            ->count();

        $activity_total = Activity::where('id_year', $id)->count();

        $activity_report = Activity::where('id_year', $id)
            ->where('status_report', '1')
            ->count();

        $year = Year::where('year_id', $id)->first();

        $Strategic = Strategic::where('id_year', $id)->sum('spend_money');


        return [
            'project_total' => $project_total,
            'projects_report' => $projects_report,
            'activity_total' => $activity_total,
            'activity_report' => $activity_report,
            'spend_money' => $Strategic,
            'avalible' =>  $year->budget - $Strategic,
        ];
    }

    public function getYearPieStrategic($id)
    {

        $Strategic = Strategic::select('strategic_number', 'strategic_name', 'spend_money')->where('id_year', $id)
            ->orderBy('strategic_number')->get();


        return $Strategic;
    }

    public function getYearLineStrategicReport($id)
    {
        $Project_total = Project::where('id_year', $id)
            ->where('status_report', 0)
            ->select('id_department', DB::raw('count(*) as project_count'))
            ->groupBy('id_department')
            ->get();

        $Project_report = Project::where('id_year', $id)
            ->where('status_report', 1)
            ->select('id_department', DB::raw('count(*) as project_count'))
            ->groupBy('id_department')
            ->get();


        $department = Department::select(['departments_name','departments_id'])
        ->where('status',1)
        ->get();

        return ['department' => $department,
        'project_report' => $Project_report,
         'project_total' => $Project_total];
    }
    public function getYearQuearDashborad($id, $quarter)
    {
        $project_total = Project::where('id_year', $id)->count();

        $projects_report = Project::where('id_year', $id)
            ->where('status_report', '1')
            ->count();

        $activity_total = Activity::where('id_year', $id)->count();

        $activity_report = Activity::where('id_year', $id)
            ->where('status_report', '1')
            ->count();

        $year = Year::where('year_id', $id)->first();

        $Strategic = Strategic::where('id_year', $id)->sum('spend_money');


        return [
            'project_total' => $project_total,
            'projects_report' => $projects_report,
            'activity_total' => $activity_total,
            'activity_report' => $activity_report,
            'spend_money' => $Strategic,
            'avalible' =>  $year->budget - $Strategic,
        ];
    }
}
