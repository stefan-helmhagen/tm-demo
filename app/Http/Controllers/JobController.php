<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::table('jobs')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $job = new Job([
            'company_id'            => $request->company_id,
            'created_by_user_id'    => '0',
            'updated_by_user_id'    => '0',
            'title'                 => $request->title,
            'description'           => $request->description,
            'location'              => $request->location
        ]);
        $job->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        return $job;
    }

    /**
     * Display the company related to the specified job.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function showCompany(Job $job)
    {
        return $job->company;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        is_null($request->company_id) ?         : $job->company_id = $request->company_id;
        is_null($request->created_by_user_id) ? : $job->created_by_user_id = $request->created_by_user_id;
        is_null($request->updated_by_user_id) ? : $job->updated_by_user_id = $request->updated_by_user_id;
        is_null($request->title) ?              : $job->title = $request->title;
        is_null($request->description) ?        : $job->description = $request->description;
        is_null($request->location) ?           : $job->location = $request->location;
        $job->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        $job->delete();
    }
}
