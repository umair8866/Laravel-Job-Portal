<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index()
    {
        $applications = VacancyApplication::orderBy('created_at', 'DESC')->with('vacancy', 'user', 'employer')->paginate(10);

        return view('admin.jobs.job-applications.list',[
            'applications' => $applications,
        ]);
    }

    public function destroy(Request $request){
        $id = $request->id;
        $jobapplication = VacancyApplication::find($id);

        if ( $jobapplication == null){
            session()->flash('error', 'Job Application not found');
            return response()->json([
                'status' => false,
            ]);
        }

        $jobapplication->delete();
        session()->flash('success', 'Job Application deleted successfully.');
        return response()->json([
            'status' => true,
        ]);
    }

}
