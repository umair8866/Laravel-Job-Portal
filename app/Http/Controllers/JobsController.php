<?php

namespace App\Http\Controllers;

use App\Mail\VacancyNotificationEmail;
use App\Models\Category;
use App\Models\User;
use App\Models\JobType;
use App\Models\SavedVacancy;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobsController extends Controller
{
    //This method will show the jobs page
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $jobTypes = JobType::where('status', 1)->get();

        $vacancies = Vacancy::where('status',1);

        //search using keywords
        if(!empty($request->keywords)){
            $vacancies = $vacancies->where(function($query) use ($request){
                $query->orWhere('title','like','%'.$request->keywords.'%');
                $query->orWhere('keywords','like','%'.$request->keywords.'%');
            });
        }

        //search using location
        if(!empty($request->location)){
            $vacancies = $vacancies->where('location',$request->location);
        }

         //search using Category
         if(!empty($request->category)){
            $vacancies = $vacancies->where('category_id',$request->category);
        }

        $jobTypeArray =[];
         //search using JobType
         if(!empty($request->jobType)){
            //1,2,3
            $jobTypeArray = explode(',' , $request->jobType);
            $vacancies = $vacancies->whereIn('job_types_id',$jobTypeArray);
        }

          //search using Experience
          if(!empty($request->experience)){
            $vacancies = $vacancies->where('experience',$request->experience);
        }


        $vacancies = $vacancies->orderBy('created_at', 'DESC');

        if($request->sort == 0){
            $vacancies = $vacancies->orderBy('created_at', 'ASC');
        }else{
            $vacancies = $vacancies->orderBy('created_at', 'DESC');
        }

        $vacancies = $vacancies->paginate(9);


        return view('front.jobs',[
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'vacancies' => $vacancies,
            'jobTypeArray' => $jobTypeArray,


        ]);
    }

    //This method will show job detail page
    public function detail($id){

        $vacancies = Vacancy::where(['id'=> $id, 'status' =>1])->first();

        if ($vacancies == null){
            abort(404);
        }

        // fetch applicants

        $applications = VacancyApplication::where('vacancy_id', $id)->with('user')->get();

        return view('front.jobDetail',[
            'vacancies' => $vacancies,
            'applications' => $applications,

        ]);
    }

    public function applyJob(Request $request){
        $id = $request->id;

        $vacancies = Vacancy::where('id', $id)->first();

        //If job not found in db
        if($vacancies == null){

            session()->flash('error', 'Job does not exist');
            return response()->json([
                'status' => false,
                'message' => 'Job does not exist'
            ]);
        }

        //you cannot apply twise on a single job
        $vacancyApplicationCount = VacancyApplication::where([
            'user_id' => Auth::user()->id,
            'vacancy_id' => $id
        ])->count();

        if ($vacancyApplicationCount > 0){

            $message = 'You have already applied.';

            session()->flash('success', $message);

            return response()->json([
                'status' => false,
                'message' => $message
            ]);

        }


            //you cannot apply own your own job
            $employer_id = $vacancies->user_id;

            if($employer_id == Auth::user()->id){

                session()->flash('error', 'You cannot apply own your own job');
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot apply own your own job'
                ]);
            }

            $application = new VacancyApplication();
            $application->vacancy_id = $id;
            $application->user_id = Auth::user()->id;
            $application->employer_id = $employer_id;
            $application->applied_date = now();
            $application->save();

            //Send Email Notification to Employer
            $employer = User::where('id',$employer_id)->first();
            $user = User::where('id',Auth::user()->id)->first();

            $maildata = [
                'employer' => $employer->toarray(),
                'user'     => $user->toarray(),
                'vacancy'  => $vacancies->toarray(),
            ];

            Mail::to($employer->email)->send(new VacancyNotificationEmail($maildata));

            $message = 'You have succfully applied.';

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);

    }

    public function saveVacancy(Request $request)
    {
        $id = $request->id;

        $job = Vacancy::find($id);

        if ($job == null){

            // session()->flash('error', 'Job not found');

            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Job not found'
            ]);
        }

        //Check user already saved the job

        $saveVacancy = SavedVacancy::where(['user_id' => Auth::user()->id, 'vacancy_id' => $id])->count();

        if ($saveVacancy > 0){
            // session()->flash('error', 'You already saved the job');

            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'You already saved the job'
            ]);
        }

        $savedVacancy = new SavedVacancy();
        $savedVacancy->vacancy_id = $id;
        $savedVacancy->user_id = Auth::user()->id;
        $savedVacancy->save();

        // session()->flash('success', 'You have successfully saved the job');

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => 'You have successfully saved the job'
        ]);

    }


}
