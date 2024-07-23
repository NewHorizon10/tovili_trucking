<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\AboutUs;
use App\Models\Client;
use App\Models\Team;
use App\Models\TeamDescription;
use App\Models\Achievment;
use App\Models\Language;
use App\Models\AboutUsDescription;
use App\Models\AchievmentDetail;

class FrontPagesController extends Controller
{

    public function aboutUs(Request $request)
    {

        $about = AboutUs::first();

        if ($request->isMethod('POST')) {
            $image = "";
            $goal_image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];


            $validator                    =   Validator::make(
                array(
                    'heading'                     => $dafaultLanguageArray['heading'],
                    'description'                 => $dafaultLanguageArray['description'],
                    'goal_description'            => $dafaultLanguageArray['goal_description'],
                    'image'                       => $request->file('image'),
                ),
                array(
                    'heading'                              => 'required',
                    'description'                          => 'required',
                    'goal_description'                     => 'required',
                    'image'                                => 'nullable|mimes:png,jpg,jpeg',
                ),
                array(
                    'heading'                           => trans("messages.This field is required"),
                    'description'                       => trans("messages.This field is required"),
                    'goal_description'                  => trans("messages.This field is required"),
                    'image.mimes'                       => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ),
            );

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {

                if (empty($about)) {
                    $about = new AboutUs();
                    $about->heading =  $dafaultLanguageArray['heading'];

                    if ($request->hasFile('image')) {
                        $file = rand() . '.' . $request->image->getClientOriginalExtension();
                        $request->file('image')->move(Config('constants.ABOUT_US_IMAGE_ROOT_PATH'), $file);
                        $about->image =  $file;
                    }


                    if ($request->hasFile('goal_image')) {
                        $file = rand() . '.' . $request->goal_image->getClientOriginalExtension();
                        $request->file('goal_image')->move(Config('constants.ABOUT_US_GOAL_IMAGE_ROOT_PATH'), $file);
                        $about->goal_image =  $file;
                    }
                    $about->description = $dafaultLanguageArray['description'];
                    $about->goal_description = $dafaultLanguageArray['goal_description'];
                    $about->save();
                } else {

                    $about->heading =  $dafaultLanguageArray['heading'];
                    if ($request->hasFile('image')) {
                        $file = rand() . '.' . $request->image->getClientOriginalExtension();
                        $request->file('image')->move(Config('constants.ABOUT_US_IMAGE_ROOT_PATH'), $file);
                        $about->image =  $file;
                    }

                    if ($request->hasFile('goal_image')) {
                        $file = rand() . '.' . $request->goal_image->getClientOriginalExtension();
                        $request->file('goal_image')->move(Config('constants.ABOUT_US_GOAL_IMAGE_ROOT_PATH'), $file);
                        $about->goal_image =  $file;
                    }
                    $about->description = $dafaultLanguageArray['description'];
                    $about->goal_description = $dafaultLanguageArray['goal_description'];
                    $about->save();
                }



                $last_id =    $about->id;


                $description_data = array_filter($request['data'], function ($item) {
                    return $item['heading'] != null;
                });
                if ($description_data && count($description_data) > 0) {
                    foreach ($description_data as $language_id => $value) {
                        AboutUsDescription::updateOrCreate([
                            'language_id' => $language_id,
                            'parent_id'   => $last_id
                        ], [
                            'heading'       => $value['heading'],
                            'description'  => $value['description'],
                            'goal_description'  => $value['goal_description'],
                        ]);
                    }
                }

                Session()->flash('success', ucfirst(trans("messages.admin_About_has_been_save_successfully")));
                return Redirect()->back();
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        $multiLanguage    =    array();

        $AboutUsDescription = AboutUsDescription::get();

        if (!empty($AboutUsDescription)) {
            foreach ($AboutUsDescription as $description) {
                $multiLanguage[$description->language_id]['heading'] = $description->heading;
                $multiLanguage[$description->language_id]['description'] = $description->description;
                $multiLanguage[$description->language_id]['goal_description'] = $description->goal_description;
            }
        }

        return view('admin.about-us.index', array('languages' => $languages, 'language_code' => $language_code, 'about' => $about, 'multiLanguage' => $multiLanguage));
    }

    public function clients(Request $request)
    {

        $DB                    =    Client::query();
        $searchVariable        =    array();
        $inputGet            =    $request->all();
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->whereBetween('created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $DB->where('created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->where('created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where('is_deleted', 0);
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();

        return  View("admin.client.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }


    public function clientAdd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'image' => 'required|mimes:jpg,jpeg,png',
                ],
                [
                    'image.mimes' => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ]
            );

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $client = new Client();

                if ($request->hasFile('image')) {
                    $file = rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->file('image')->move(Config('constants.CLIENT_IMAGE_ROOT_PATH'), $file);
                    $client->image = $file;
                }
                $client->save();

                Session()->flash('success', ucfirst(trans("messages.admin_Client_has_been_save_successfully")));
                return Redirect()->route('clients.index');
            }
        }


        return view('admin.client.create');
    }

    public  function clientDelete(Request $request, $endesid = null)
    {

        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $depDetails   =   Client::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->back();
        }
        if ($dep_id) {
            Client::where('id', $dep_id)->update(array('is_deleted' => 1));

            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Client_has_been_removed_successfully")));
        }
        return back();
    }

    public function team(Request $request)
    {

        $DB                    =    Team::query();
        $searchVariable        =    array();
        $inputGet            =    $request->all();
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->whereBetween('created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $DB->where('created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->where('created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "designation") {
                        $DB->where("designation", 'like', '%' . $fieldValue . '%');
                    }
                }

                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where('is_deleted', 0)->select("teams.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();


        return view('admin.team.index', compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function teamAdd(Request $request)
    {

        if ($request->isMethod('POST')) {

            $image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];

            $validator                    =   Validator::make(
                array(
                    'name'                     => $dafaultLanguageArray['name'],
                    'designation'                  => $dafaultLanguageArray['designation'],
                    'image'                     => $request->file('image'),
                ),
                array(
                    'name'                     => 'required',
                    'designation'                  => 'required',
                    'image'                     => 'required|mimes:png,jpg,jpeg',
                ),
                array(
                    'name'                     => trans("messages.This field is required"),
                    'designation'                  => trans("messages.This field is required"),
                    'image'                    => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ),
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $team = new Team();
                $team->name =  $dafaultLanguageArray['name'];
                $team->designation =  $dafaultLanguageArray['designation'];
                if ($request->hasFile('image')) {
                    $file = rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->file('image')->move(Config('constants.TEAM_IMAGE_ROOT_PATH'), $file);
                    $team->image =  $file;
                }
                $team->save();

                if (!$team) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back();
                }
                $last_id            =    $team->id;

                foreach ($thisData['data'] as $language_id => $value) {

                    $TeamDescription                =  new TeamDescription();
                    $TeamDescription->language_id    =    $language_id;
                    $TeamDescription->parent_id        =    $last_id;
                    $TeamDescription->name            =    $value['name'];
                    $TeamDescription->designation    =    $value['designation'];
                    $TeamDescription->save();
                }

                Session()->flash('success', ucfirst(trans("messages.admin_Team_has_been_save_successfully")));
                return Redirect()->route('team.index');
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        return view('admin.team.create', compact('languages', 'language_code'));
    }


    public function teamEdit(Request $request, $endesid = null)
    {
        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $team   =   Team::find($dep_id);
        if (empty($team)) {
            return Redirect()->back();
        }


        if ($request->isMethod('POST')) {

            $image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];


            $validator                    =   Validator::make(
                array(
                    'name'                      => $dafaultLanguageArray['name'],
                    'designation'               => $dafaultLanguageArray['designation'],
                    'image'                     => $request->file('image'),
                ),
                array(
                    'name'                     => 'required',
                    'designation'              => 'required',
                    'image'                    => 'nullable|mimes:png,jpg,jpeg',
                ),
                array(
                    'name'                     => trans("messages.This field is required"),
                    'designation'              => trans("messages.This field is required"),
                    'image.mimes'                => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ),
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $team->name =  $dafaultLanguageArray['name'];
                $team->designation =  $dafaultLanguageArray['designation'];
                if ($request->hasFile('image')) {
                    $file = rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->file('image')->move(Config('constants.TEAM_IMAGE_ROOT_PATH'), $file);
                    $team->image =  $file;
                }
                $team->save();

                $last_id =    $team->id;
                $description_data = array_filter($request['data'], function ($item) {
                    return $item['name'] != null;
                });
                if ($description_data && count($description_data) > 0) {
                    foreach ($description_data as $language_id => $value) {
                        TeamDescription::updateOrCreate([
                            'language_id' => $language_id,
                            'parent_id'   => $last_id
                        ], [
                            'name'       => $value['name'],
                            'designation'  => $value['designation'],
                        ]);
                    }
                }

                Session()->flash('success', ucfirst(trans("messages.admin_Team_has_been_updated_successfully")));
                return Redirect()->route('team.index');
            }
        }

        $multiLanguage    =    array();

        $TeamDescription = TeamDescription::where('parent_id', $team->id)->get();

        if (!empty($TeamDescription)) {
            foreach ($TeamDescription as $description) {
                $multiLanguage[$description->language_id]['name'] = $description->name;
                $multiLanguage[$description->language_id]['designation'] = $description->designation;
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        return view('admin.team.edit', array('languages' => $languages, 'language_code' => $language_code, 'team' => $team, 'multiLanguage' => $multiLanguage));
    }

    public  function teamDelete(Request $request, $endesid = null)
    {
        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $depDetails   =   Team::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->back();
        }
        if ($dep_id) {
            Team::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Team_has_been_removed_successfully")));
        }
        return back();
    }


    public function achievment(Request $request)
    {

        $DB                    =    Achievment::query();
        $searchVariable        =    array();
        $inputGet            =    $request->all();
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->whereBetween('created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = date('Y-m-d', strtotime($searchData['date_from']));
                $DB->where('created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = date('Y-m-d', strtotime($searchData['date_to']));
                $DB->where('created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "description") {
                        $DB->where("description", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $DB->where('is_deleted', 0)->select("achievments.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();


        return view('admin.achievment.index', compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function achievmentAdd(Request $request)
    {

        if ($request->isMethod('POST')) {

            $image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];

            $validator                    =   Validator::make(
                array(
                    'name'                     => $dafaultLanguageArray['name'],
                    'description'                  => $dafaultLanguageArray['description'],
                    'image'                     => $request->file('image'),
                ),
                array(
                    'name'                     => 'required',
                    'description'                  => 'required',
                    'image'                     => 'required|mimes:png,jpg,jpeg',
                ),
                array(
                    'name'                     => trans("messages.This field is required"),
                    'description'              => trans("messages.This field is required"),
                    'image.mimes'               => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ),
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $achievment = new Achievment();
                $achievment->name =  $dafaultLanguageArray['name'];
                $achievment->description =  $dafaultLanguageArray['description'];
                if ($request->hasFile('image')) {
                    $file = rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->file('image')->move(Config('constants.ACHIEVMENT_IMAGE_ROOT_PATH'), $file);
                    $achievment->image =  $file;
                }
                $achievment->save();

                if (!$achievment) {
                    Session()->flash('error', ucfirst(trans("messages.something_went_wrong")));
                    return Redirect()->back();
                }
                $last_id            =    $achievment->id;

                foreach ($thisData['data'] as $language_id => $value) {

                    $AchievmentDetail                =  new AchievmentDetail();
                    $AchievmentDetail->language_id    =    $language_id;
                    $AchievmentDetail->parent_id        =    $last_id;
                    $AchievmentDetail->name            =    $value['name'];
                    $AchievmentDetail->description    =    $value['description'];
                    $AchievmentDetail->save();
                }

                Session()->flash('success', ucfirst(trans("messages.admin_Achievement_has_been_saved_successfully")));
                return Redirect()->route('achievment.index');
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        return view('admin.achievment.create', compact('languages', 'language_code'));
    }


    public function achievmentEdit(Request $request, $endesid = null)
    {
        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $achievment   =   Achievment::find($dep_id);
        if (empty($achievment)) {
            return Redirect()->back();
        }


        if ($request->isMethod('POST')) {

            $image = "";
            $thisData                       =    $request->all();
            $default_language               =    Config('constants.DEFAULT_LANGUAGE.FOLDER_CODE');
            $language_code                  =   Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');
            $dafaultLanguageArray           =    $thisData['data'][$language_code];




            $validator                    =   Validator::make(
                array(
                    'name'                      => $dafaultLanguageArray['name'],
                    'description'               => $dafaultLanguageArray['description'],
                    'image'                     => $request->file('image'),
                ),
                array(
                    'name'                     => 'required',
                    'description'              => 'required',
                    'image'                    => 'nullable|mimes:png,jpg,jpeg',
                ),
                array(
                    'name'                     => trans("messages.This field is required"),
                    'description'              => trans("messages.This field is required"),
                    'image.mimes'           => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                ),
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $achievment->name =  $dafaultLanguageArray['name'];
                $achievment->description =  $dafaultLanguageArray['description'];
                if ($request->hasFile('image')) {
                    $file = rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->file('image')->move(Config('constants.ACHIEVMENT_IMAGE_ROOT_PATH'), $file);
                    $achievment->image =  $file;
                }
                $achievment->save();

                $last_id =    $achievment->id;
                $description_data = array_filter($request['data'], function ($item) {
                    return $item['name'] != null;
                });
                if ($description_data && count($description_data) > 0) {
                    foreach ($description_data as $language_id => $value) {
                        AchievmentDetail::updateOrCreate([
                            'language_id' => $language_id,
                            'parent_id'   => $last_id
                        ], [
                            'name'       => $value['name'],
                            'description'  => $value['description'],
                        ]);
                    }
                }

                Session()->flash('success', ucfirst(trans("messages.admin_Achievement_has_been_updated_successfully")));
                return Redirect()->route('achievment.index');
            }
        }

        $multiLanguage    =    array();

        $AchievmentDetail = AchievmentDetail::where('parent_id', $achievment->id)->get();

        if (!empty($AchievmentDetail)) {
            foreach ($AchievmentDetail as $description) {
                $multiLanguage[$description->language_id]['name'] = $description->name;
                $multiLanguage[$description->language_id]['description'] = $description->description;
            }
        }

        $languages = Language::where('is_active', 1)->get();
        $language_code = Config('constants.DEFAULT_LANGUAGE.LANGUAGE_CODE');

        return view('admin.achievment.edit', compact('achievment', 'languages', 'language_code', 'multiLanguage'));
    }

    public  function achievmentDelete(Request $request, $endesid = null)
    {

        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $depDetails   =   Achievment::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->back();
        }
        if ($dep_id) {
            Achievment::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', ucfirst(trans("messages.admin_Achievement_has_been_removed_successfully")));
        }
        return back();
    }
}