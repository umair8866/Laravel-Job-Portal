<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobType;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\savedVacancy;
use App\Models\VacancyApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class AccountController extends Controller
{
    //This method will show user registration page
    public function registration()
    {
        return view('front.account.registration');
    }

    //This function will save a user
    public function processRegistration(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required'

        ]);

        if($validator->passes())
        {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have registered successfully.');

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

    //This method will show user login page
    public function login() 
    {
        return view('front.account.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]); 

        if($validator->passes())
        {
            if (Auth::attempt(['email'=>$request->email , 'password'=>$request->password])){
                return redirect()->route('account.profile');

            }else{
                return redirect()->route('account.login')->with('error', 'Either email/password is incorrect');
            }

        }else{
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function profile()
    {
        $id = Auth::user()->id;

        $user = User::find($id);
        return view('front.account.profile',[
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;

        $validator = Validator::make($request->all(),[
            
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email, '.$id.',id'
        ]);

        if ($validator->passes()){

            $user = User::find($id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();

            session()->flash('success', 'Profile updated successfully.');

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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'image' => 'required|image'
        ]);

        if ($validator->passes()){

            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id.'-'.time().'.'.$ext;
            $image->move(public_path('/profile_pic/'), $imageName);

            //To delete old image
            File::delete(public_path('/profile_pic/'.Auth::user()->image));

            User::where('id', $id)->update(['image' => $imageName]);

            session()->flash('success', 'Profile picture updated successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        }else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);

        }
    }

    public function createJob()
    {
        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobtypes = JobType::orderBy('name', 'ASC')->where('status', 1)->get();


        return view('front.account.job.create', [
            'categories' => $categories,
            'jobtypes' => $jobtypes

        ]);
    }

    public function saveJob(Request $request)
    {
        $rules = [
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            // 'experience' => 'required',
            'company_name' => 'required|min:3|max:50'

        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){

            $job = new Vacancy();
            $job->title = $request->title;
            $job->category_id  = $request->category;
            $job->job_types_id = $request->jobType;
            $job->user_id = Auth::user()->id;

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
            $job->company_location = $request->location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash('success', 'Job added successfully.');

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

    public function myjobs()
    {
        $jobs = Vacancy::where('user_id', Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
        return view('front.account.job.myjobs', [
            'jobs' => $jobs
        ]);
    }

    public function editJob($id)
    {
        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobtypes = JobType::orderBy('name', 'ASC')->where('status', 1)->get();

        $job = Vacancy::where([
            'user_id' => Auth::user()->id,
            'id'      => $id
        ])->first();

        if ($job == null){
            abort(404); 
        }

        return view('front.account.job.edit',[
            'categories' => $categories,
            'jobtypes' => $jobtypes,
            'job'      => $job,
        ]);
    }

    public function updateJob(Request $request)
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
            $job->user_id = Auth::user()->id;

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

    public function deleteJob(Request $request)
    {
        $job = Vacancy::where([
            'user_id' => Auth::user()->id,
            'id'      => $request->jobId, 
        ])->first();

        if ($job == null) {
            session()->flash('error', 'Either job deleted or not found');

            return response()->json([
                'status' => true
            ]);
        }

        Vacancy::where('id', $request->jobId)->delete();
            session()->flash('success', 'Job deleted successfully.');

            return response()->json([
                'status' => true
            ]);
                 
    }

    public function myJobApplications()
    {
        $vacancyApplications = VacancyApplication::where('user_id', Auth::user()->id)->with('vacancy', 'vacancy.applications')->paginate(10);
        return view('front.account.job.my-job-applications',[
            'vacancyApplications' => $vacancyApplications,
        ]);
    }
    
    public function removeVacancy(Request $request)
    {
         $vacancyApplication = VacancyApplication::where(['id' => $request->id, 'user_id'=> Auth::user()->id])->first();

         if ($vacancyApplication == null){

            session()->flash('error', 'Job Application not found.');
            return response()->json([
                'status' => false,
                
            ]);
        }
         VacancyApplication::find($request->id)->delete();
            session()->flash('success', 'Job Application removed successfully.');
            return response()->json([
                'status' => true,
            ]);
    }

    public function savedVacancy()
    {
        // $vacancyApplications = VacancyApplication::where('user_id', Auth::user()->id)->with('vacancy', 'vacancy.applications')->paginate(10);

        $savedJobs = savedVacancy::where(['user_id' => Auth::user()->id,])->with('vacancy', 'vacancy.applications')->paginate(10);
        
        return view('front.account.job.savedVacancy',[
            'savedJobs' => $savedJobs,
        ]);
    }

    public function removeSavedVacancy(Request $request)
    {
         $savedvacancy = savedVacancy::where(['id' => $request->id, 'user_id'=> Auth::user()->id])->first();

         if ($savedvacancy == null){

            session()->flash('error', 'Job Application not found.');
            return response()->json([
                'status' => false,
                
            ]);
        }
        savedvacancy::find($request->id)->delete();
            session()->flash('success', 'Job removed successfully.');
            return response()->json([
                'status' => true,
            ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'errors'  => $validator->errors(),
            ]);
        }

        if (Hash::check($request->old_password, Auth::user()->password) == false){
            session()->flash('error', 'Your old password is incorrect.');
            return response()->json([
                'status' => true,
            ]);
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        session()->flash('success', 'Your password updated successfully.');
        return response()->json([
            'status' => true,
        ]);
    }
}
