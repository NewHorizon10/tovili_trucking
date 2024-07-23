<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Session;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\models\User;

class UserExport implements FromCollection , WithHeadings
{
    protected $id;
    function __construct($id) {
            $this->id = $id;
    }

    public function collection()
    {   
            $key = 1;
            $exportData = [];
            foreach($this->id as $record) {
                $exportData[$key]['count']  	       = $key;
                $exportData[$key]['country_name']  	   = $record;
                $key++;
            }
            $exportData = collect($exportData);
            return $exportData;
    }

   
    public function headings() :array
    { 
        $current_url = url()->full();
        if(url('/adminpnlx/truck-company-export-sample/sample') == $current_url) {
            return [
                ["Name","Email","Phone Number","Company Name","Company Number (HP)","Contact Person Name","Contact Person Email","Company Location",
                "Company Type","Contact Person Phone Number"],

                ["Jhon", "Jhon@gmail.com", "8765565976", "Owebest", "Company HP Number 23567", "Kamil", "Kamil@gmail.com", "Malviya Nagar", "Corporation", "9553257853"],
                ["Jack", "Jack@gmail.com", "9893889788", "Kyubok", "Company HP Number 25567", "Sohail", "sohail@gmail.com", "Pratap Nagar", "Partnership", "9553127853"],
                ["Nick", "Nick@gmail.com", "876444976", "NBT", "Company HP Number 34322", "Joya", "Joya@gmail.com", "Pratap Nagar", "Sole Proprietorship", "9556357853"],
               ];
        }else if(url('/adminpnlx/truck-company-export-sample/company-type') == $current_url) {
                return  ["Sr No", "Name"];
        }else{
            return  ["Name","Email","Phone Number","Company Name","Company Mobile Number","Contact Person Name","Contact Person Email","Company Location",
            "Company Type","Contact Person Phone Number"];
        }

    }


}
