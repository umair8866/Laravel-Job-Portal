<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Vacancy::orderBy('Created_at', 'DESC')->with('user', 'applications')->paginate(10);
        return view('admin.Jobs.list',[
            'jobs' => $jobs,
        ]);
    }

    public function edit($id)
    {
        $job = Vacancy::findOrFail($id);
        $categories = Category::orderBy('name', 'ASC')->get();
        $jobtypes = JobType::orderBy('name', 'ASC')->get();

        return view('admin.Jobs.edit',[
            'job' => $job,
            'categories' => $categories,
            'jobtypes' => $jobtypes,

        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:50'
            // 'experience' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){


            $job = Vacancy::find($request->job_id);
            $job->title = $request->title;
            $job->category_id  = $request->category;
            $job->job_types_id = $request->jobType;
            // $job->user_id = Auth::user()->id;

            $job->vacancy_count = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibilities = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->website;

            $job->status = $request->status;
            $job->isFeatured = (!empty($request->isFeatured)) ? $request->isFeatured : 0;

            $job->save();

            session()->flash('success', 'Job updated successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $job = Vacancy::find($id);

        if ($job == null){
            session()->flash('error' , 'Job not found');
            return response()->json([
                'status' => false,
            ]);
        }
        $job->delete();
            session()->flash('success' , 'Job deleted successfully.');
            return response()->json([
                'status' => true,
            ]);
    }
}
