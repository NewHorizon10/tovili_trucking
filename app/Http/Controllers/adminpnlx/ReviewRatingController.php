<?php

namespace App\Http\Controllers\adminpnlx;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\RatingReview;
use App\Models\RatingReviewPhoto;
use Illuminate\Http\Request;
use Config, Redirect, Session, View, DB;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ReportExport;

class ReviewRatingController extends Controller
{

	public $model		        = 'ReviewRating';
	public $sectionName	        = 'Review Rating';
	public $sectionNameSingular	= 'Review Rating';

	public function __construct(Request $request)
	{
		parent::__construct();
		View::share('model', $this->model);
		View::share('modelName', $this->model);
		View::share('sectionName', $this->sectionName);
		View::share('sectionNameSingular', $this->sectionNameSingular);
		$this->request = $request;
	}


	public function index(Request $request)
	{
		$DB					=	RatingReview::
		leftJoin('users as customer', 'customer.id', '=', 'rating_reviews.customer_id')
		->leftJoin('user_company_informations as truck_company', 'truck_company.user_id', '=', 'rating_reviews.truck_company_id')
		->select('rating_reviews.*','customer.name as customer_name','truck_company.company_name as truck_company_name')
		;
		$searchVariable		=	array();
		$inputGet			=	$request->all();
		if ($request->all()) {
			$searchData			=	$request->all();
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
				$dateS = $searchData['date_from'];
				$dateE = $searchData['date_to'];
				$DB->whereBetween('rating_reviews.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
			} elseif (!empty($searchData['date_from'])) {
				$dateS = $searchData['date_from'];
				$DB->where('rating_reviews.created_at', '>=', [$dateS . " 00:00:00"]);
			} elseif (!empty($searchData['date_to'])) {
				$dateE = $searchData['date_to'];
				$DB->where('rating_reviews.created_at', '<=', [$dateE . " 00:00:00"]);
			}
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldValue != "") {
					if ($fieldName == "user_name") {
						$DB->whereHas("getUser", function ($query) use ($fieldValue) {
							$query->where('users.name', 'like', '%' . $fieldValue . '%');
						});
					}
					if ($fieldName == "company_name") {
						$DB->where('truck_company.company_name', 'like', '%' . $fieldValue . '%');
					}
				}
				$searchVariable	=	array_merge($searchVariable, array($fieldName => $fieldValue));
			}
		}


		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'rating_reviews.created_at';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$records_per_page	=	($request->input('per_page')) ? $request->input('per_page') : Config::get("Reading.records_per_page");
		
		$DB->orderBy($sortBy, $order);
        $allData = clone $DB;
        Session::put(['export_data_review_rating'=>$allData->get()]);
        $results = $DB->paginate($records_per_page);


		$complete_string		=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$results->appends($inputGet)->render();
		return  View::make("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
	}

	public function reviewExport(Request $request)
	{

		$list[0] = array(
			trans('messages.review_by'),
            trans('messages.reviewed_to'),
            trans('messages.overall_review'),
            trans('messages.reviews'),
            trans('messages.admin_common_Added_On'),
		);


		$customers_export = Session::get('export_data_review_rating');
		

		foreach ($customers_export as $key => $excel_export) {


			$list[] = array(
                $excel_export->getUser ?->name,
				$excel_export->getTruckCompany ?->name,
				$excel_export->overall_rating . ' Out of 5' ,
				$excel_export->review,
				date(config("Reading.date_format"), strtotime($excel_export->created_at)),

			);
		}
	
		$collection = new Collection($list);
		return Excel::download(new ReportExport($collection), 'Reviews.xlsx');
	}




	public function show($modelId = 0)
	{
		$modelDetails	=	RatingReview::where('id', $modelId)->with('getUser', 'getTruckCompany', 'getPhotos', 'shipmentDetails')->first();
		if ($modelDetails->is_deleted == '1') {
			return Redirect::route($this->model . ".index");
		}

		return  View::make("admin.$this->model.view", compact('modelDetails'));
	}





	public function delete($enratingid)
	{
	
        $ratingid = '';
        if (!empty($enratingid)) {
            $ratingid = base64_decode($enratingid);
        }
		
        $CouponDetails   =   RatingReview::find($ratingid);
        if (empty($CouponDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($ratingid) {
            RatingReview::where('id', $ratingid)->delete();
            RatingReviewPhoto::where('rating_review_id', $ratingid)->delete();
            Session()->flash('flash_notice', trans("Rating has been removed successfully"));
        }
        return back();
	}

	public function edit(Request $request,  $enuserid = null)
	{

			if ($request->isMethod('POST')) {
			$thisData = $request->all();
			$user_id = '';
			if (!empty($enuserid)) {
				$user_id = base64_decode($enuserid);
				
			} else {
				return redirect()->route($this->model . ".index");
			}
			$review   =   RatingReview::with('getUser', 'getTruckCompany', 'getPhotos')->findOrFail($user_id);
			
			
			$validated = $request->validate([
				'review'         => 'required',
			]);
			
			$review->review             =   ($request->review ?? '');	
			$review->overall_rating     =   ($request->overalls_rating ?? '');
			$review->driver_rating      =   ($request->driver_rating ?? '');
			$review->professionality    =   ($request->professionality ?? '');
			$review->meet_schedule      =   ($request->meet_schedule ?? '');
			$SavedResponse              = $review->save();
		

			if (!$SavedResponse) {
				Session()->flash('error', trans("Something went wrong."));
				return Redirect()->back()->withInput();
			}
			Session()->flash('success', ucfirst("Review Rating has been updated successfully"));
			return Redirect()->route($this->model . ".index");
		}
		$user_id = '';
		if (!empty($enuserid)) {
			$user_id        = base64_decode($enuserid);
		
			$userDetails    = RatingReview::with('getUser', 'getTruckCompany', 'getPhotos')->where('id', $user_id)->first();
		
			return  View("admin.$this->model.edit", compact('userDetails'));
		} else {
			return redirect()->route($this->model . ".index");
		}
	}
}
