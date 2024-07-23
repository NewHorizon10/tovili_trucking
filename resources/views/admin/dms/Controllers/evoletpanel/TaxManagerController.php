<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerEnquiry;
use App\Model\DropDown;
use App\Model\TaxManager;
use App\Model\BatteryDetail;
use App\Model\DealerInventory;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerEnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerEnquiryController\dashboard
*/
class TaxManagerController extends BaseController {
        

        /**
	* Function is 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function listTaxes(){
        $DB 					= 	TaxManager::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
			if ((Input::get())) {
				$searchData			=	Input::get(); 
				unset($searchData['display']);
				unset($searchData['_token']);
				if(isset($searchData['order'])){
					unset($searchData['order']);
				}
				if(isset($searchData['sortBy'])){
					unset($searchData['sortBy']);
				}
				if(isset($searchData['page'])){
					unset($searchData['page']);
				}
				foreach($searchData as $fieldName => $fieldValue){
					if($fieldValue != ""){
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
			$result 				= 	$DB
										->where('is_deleted',0)
										->orderBy($sortBy, $order)
										->paginate(Config::get("Reading.records_per_page")); 
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
        return View::make('admin.taxManager.index',compact('result' ,'searchVariable','sortBy','order','query_string'));
    }



     /**
	* Function is  used for add form
	*
	* @param null
	*
	* @return view page. 
	*/
    public function addTax(){ 
		return View::make('admin.taxManager.add');
    }
    

    /**
	* Function for save tax
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveTax(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
                    'tax_name' 		=>	 'required',
                    'tax_value' 		=>	 'required',
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				$tax 					= 	new TaxManager;
				$tax->tax_name			=	Input::get('tax_name');
				$tax->tax_value			=	Input::get('tax_value');
				$tax->save();
				$taxId					=	$tax->id;
				Session::flash("success",trans("Tax added successfully."));
				return Redirect::to('/adminpnlx/tax-management');
				//return Redirect::back();
			}
		}
    }
    
    public function editTax($id = ""){
		$taxDetails	=	TaxManager::where('id',$id)->first();
		if(empty($taxDetails)) {
			return Redirect::back();
		}	
		return View::make('admin.taxManager.edit', compact("taxDetails"));
	
		
    }
    

    public function updateTax($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
                    'tax_name' 		=>	 'required',
                    'tax_value' 	=>	 'required',
				)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$tax						= 	TaxManager::find($id);
				$tax->tax_name			        =	Input::get('tax_name');
				$tax->tax_value			        =	Input::get('tax_value');
				$tax->save();
				Session::flash('flash_notice', trans("Tax has been updated successfully.")); 
				return Redirect::to('/adminpnlx/tax-management');
			}
		}
    }
    
    public function updateTaxStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Tax is deactivated successfully.");
			$taxDetails		=	TaxManager::find($id); 
		}else{
			$statusMessage	=	trans("Tax is activated successfully.");
		}
		$this->_update_all_status("tax_manager",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateDealerNetworkstatus()
}