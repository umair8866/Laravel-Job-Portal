@extends('front.layouts.app')

@section('main')
<section class="section-4 bg-2">
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('jobs') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">
                @include('front.message')
                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">

                                <div class="jobs_conetent">
                                    <a href="#">
                                        <h4>{{ $vacancies->title }}</h4>
                                    </a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-map-marker"></i> {{ $vacancies->location }}</p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-clock-o"></i> {{ $vacancies->vacancy_count }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="jobs_right">
                                <div class="apply_now">
                                    <a class="heart_mark" href="javascript:void(0);" onClick="saveVacancy({{ $vacancies->id }})">
                                        @if(Auth::check() && Auth::user()->savedVacancies()->where('vacancy_id', $vacancies->id)->exists())
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                            <h4>Job description</h4>
                            {{  $vacancies->description }}
                        </div>
                            @if (!empty($vacancies->responsibilities))
                                <div class="single_wrap">
                                     <h4>Responsibility</h4>
                                     {{ $vacancies->responsibilities }}
                                </div>
                            @endif

                            @if (!empty($vacancies->qualifications))
                            <div class="single_wrap">
                                <h4>Qualifications</h4>
                                {{ $vacancies->qualifications }}
                            </div>

                            @endif
                            @if (!empty($vacancies->benefits))
                            <div class="single_wrap">
                               <h4>Benefits</h4>
                                {{ $vacancies->benefits }}
                            </div>
                            @endif
                        <div class="border-bottom"></div>
                        <div class="pt-3 text-end">
                            <a href="#" onClick="saveVacancy({{ $vacancies->id }})" class="btn btn-secondary">Save</a>
                            <a href="#" onClick="applyJob({{ $vacancies->id }})" class="btn btn-primary">Apply</a>
                        </div>
                    </div>
                </div>

                @if(Auth::user())
                    @if(Auth::user()->id == $vacancies->user_id)

                <div class="card shadow border-0 mt-4">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">

                                <div class="jobs_conetent">
                                    <h4>Applicants</h4>
                                </div>
                            </div>
                            <div class="jobs_right">
                            </div>
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">

                        <table class="table table-striped">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Applied Date</th>
                            </tr>
                            @if ($applications->isNotEmpty())
                                @foreach ($applications as $application)
                                    <tr>
                                        <td>{{ $application->user->name }}</td>
                                        <td>{{ $application->user->email }}</td>
                                        <td>{{ $application->user->mobile }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($application->applied_date)->format('d M, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">Applicants not found</td>
                                    </tr>
                            @endif

                        </table>
                    </div>
                </div>
                @endif
                @endif


            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Job Summery</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Published on: <span>{{ \Carbon\Carbon::parse($vacancies->created_at)->format('d M, Y') }}</span></li>
                                <li>Vacancy: <span>{{ $vacancies->vacancy_count }}</span></li>

                                @if (!empty($vacancies->salary))
                                    <li>Salary: <span>{{ $vacancies->salary }}</span></li>
                                 @endif
                                <li>Location: <span>{{ $vacancies->location }}</span></li>
                                <li>Job Nature: <span>{{ $vacancies->jobType->name }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card shadow border-0 my-4">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Name: <span>{{ $vacancies->company_name }}</span></li>

                                @if (!empty($vacancies->company_location))
                                    <li>Locaion: <span>{{ $vacancies->company_location }}</span></li>
                                 @endif

                                 @if (!empty($vacancies->company_website))
                                     <li>Webite: <span><a href="{{ $vacancies->company_website }}">{{ $vacancies->company_website }}</a></span></li>
                                  @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJs')
<script type="text/javascript">


function applyJob(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to apply for this job?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, apply!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("applyjob") }}',
                type: 'post',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                        Swal.fire(
                            'Applied!',
                            response.message,
                            'success'
                        ).then(() => {
                            window.location.href = "{{ url()->current() }}";
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function () {
                    Swal.fire(
                        'Error!',
                        'An unexpected error occurred.',
                        'error'
                    );
                }
            });
        }
    });
}

// function applyJob(id){
//     if (confirm("Are you sure you want to apply?")){

//         $.ajax({
//             url : '{{ route("applyjob") }}',
//             type : 'post',
//             data : {id:id},
//             dataType : 'json',
//             success : function(response){
//                 window.location.href="{{ url()->current() }}";
//             }
//         });
//     }
// }

function saveVacancy(id){

        $.ajax({
            url : '{{ route("savejob") }}',
            type : 'post',
            data : {id:id},
            dataType : 'json',
            success : function(response){
                if (response.status) {
                    Swal.fire(
                        'Applied!',
                        response.message,
                        'success'
                    ).then(() => {
                        window.location.href = "{{ url()->current() }}";
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.message,
                        'error'
                    );
                }
            }
        });
    }


</script>
@endsection
