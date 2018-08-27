<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Meeting;
use App\User;

class MeetingController extends Controller
{
    public function __constructor(){
        // this->middleware(name);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::all();
        foreach($meetings as $meeting){
          $meeting->view_meeting = [
            'href' => 'api/v1/meeting/'. $meeting->id,
            'method' => 'GET'
          ];
        };
        
        $response = [
          'msg' => 'list of all meetings',
          'meetings' => $meetings
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validator = \Validator::make($request->all(), [
        'title' => 'required',
        'description' => 'required|max:255',
        // 'time' => 'required|date_format:YmdHie',
        'user_id' => 'required'
      ]);

      if($validator->fails()){
        return response()->json($validator->errors(), 400);
      }  
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = new Meeting([
          'title' => $title,
          'description' => $description,
          'time' => Carbon::now()
        ]);
          
        if($meeting->save()) {
          // dd('Successfully Saved');
          $meeting->users()->attach($user_id);
          $meeting->view_meeting = [
            'href' => 'api/v1/meeting/' . $meeting->id,
            'method' => 'GET'
          ];

          $response = [
            'msg' => 'Meeting Created',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);

        }
    else {
      $response = [
        'msg' => 'An error occur while creating a meeting',
    ];

    return response()->json($response, 200);

    }
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id', $id)->firstOrFail();
        // dd($meeting);
        $meeting->view_meeting = [
          'href' => 'api/v1/meeting',
          'method' => 'GET'
        ];

        $response = [
          'msg' => 'Meeting Information',
          'meeting' => $meeting
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = [
          'title' => $title,
          'description' => $description,
          'time' => Carbon::now(),
          'user_id' => $user_id,
          'view_meeting' => [
            'href' => 'api/v1/meeting/1',
            'method' => 'GET'
          ]
        ];
        $meeting = Meeting::with('users')->findOrFail($id);

        if (!$meeting->users()->where('id', $user_id)->first()){
          return response()->json('msg' => 'User')
        }
        return 'it works';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [
            'msg' => 'Deleted Successful',
            'create' => [
                'href' => 'ap1/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
        ];
        return response()->json($response, 200);
    }
}
