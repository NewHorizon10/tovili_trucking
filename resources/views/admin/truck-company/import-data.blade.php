@extends('admin.layouts.layout')
@section('content')
<style>
    .invalid-feedback {
        display: inline;
    }
</style>

<?php 
$segment2	=	Request::segment(1);
$segment3	=	Request::segment(2); 
$segment4	=	Request::segment(3);
if($segment4 == 'individual-user'){
	$pageTitle = "Individual User";
	$bredcrumTitle = "Individual User";
	$addBtnTitle = " Add Individual User";
}
 ?>
<style>
	.chosen-container-single .chosen-single{
		padding: 5px 5px 5px 8px;
		height: 35px;
	}
</style>
<script type="text/javascript"> 
	$(document).ready(function(){
		$(".chosen-select").chosen({width: "100%"});
	}); 
</script>
<section class="content">
    <div class="row" style="padding:0 15px">
        <form action="{{route('truck-company.importListdata')}}" method="post" class="my-4">
        {{ csrf_field() }}
            
                <div class="box" >
                    <div class="box-body">
                        <table
                            class="table table-hover"
                            id="taskTable">
                            <thead>
                                <tr class="text-capitalize">
                                    @if ($import_data && array_key_exists(0, $import_data))
                                        @foreach (array_keys($import_data[0]) as $key_name)
                                            
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($import_data as $key => $val)
                                    <tr>
                                        @foreach ($val as $k => $v)
                                            @if (!array_key_exists($key, $errors))

                                                <input type="hidden" name="keys[{{ $key }}][]"
                                                    value="{{ $k }}" />
                                                <input type="hidden" name="values[{{ $key }}][]"
                                                    value="{{ $v }}" />
                                            @endif
                                            <td>{{ $v }}
                                                @if (array_key_exists($key, $errors) && $errors[$key]->has($k))
                                                    <p class="text-danger d-block" style="font-size: 15px">
                                                        {{ $errors[$key]->first($k) }}
                                                    </p>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
         
            <div class="loginFormbx">
                @if (count($import_data)>1 && count($errors) == 0)
                    <button class="btn btn-success loginBtn themeBtn mx-auto mt-3">Continue with valid records</button>
                @endif

                <a href="{{route('truck-company.index')}}" class="btn btn-danger loginBtn themeBtn mx-auto mt-3 cancel_btn">Cancel</a>
            </div>
        </form>
    </div>
</section>
@stop

