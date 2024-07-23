<div class="row align-items-center main-pagination">
	<div class="col-sm-12 col-xl-5">
		<div class="dataTables_info">
			{{trans("messages.Showing")}} {{ $results->firstItem() }} {{trans("messages.to")}} {{ $results->lastItem() }} {{trans("messages.of")}} {{$results->total()}} {{trans("messages.entries")}}
		</div>
	</div>
	<div class="col-sm-12 col-xl-7">
		<div class="d-flex justify-content-end align-items-center dataTabel_responsive">
			<div class="dataTables_length data-table-block mr-4">
				<label class="mb-0">
					<input type="hidden" name="type" value="{{request('type')}}">
					{{-- {{trans("messages.Show")}} --}}
					<select name="per_page" class="class' => 'custom-select custom-select-sm form-control form-control-sm" onchange='page_limit()' id="per_page">
						<option Value="10" {{ Request::get('per_page') == 10 ? 'selected' : '' }}>{{trans("messages.Default")}}</option>
						<option Value="15" {{ Request::get('per_page') == 15 ? 'selected' : '' }}>15</option>
						<option Value="20" {{ Request::get('per_page') == 20 ? 'selected' : '' }}>20</option>
						<option Value="30" {{ Request::get('per_page') == 30 ? 'selected' : '' }}>30</option>
						<option Value="50" {{ Request::get('per_page') == 50 ? 'selected' : '' }}>50</option>
						<option Value="100"{{ Request::get('per_page') == 100 ? 'selected' : '' }}>100</option>
					</select>
				</label>
			</div>
			<?php
			$link_limit = 6;
			?>
			@if ($results->lastPage() > 1)
			<div class="dataTables_paginate paging_full_numbers">
				<ul class="pagination">
					@if ($results->onFirstPage())
					<li class="paginate_button page-item previous disabled">
						<a href="javascript:void(0);" class="page-link">
							<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					@else
					<li class="paginate_button page-item first">
						<a href="{{ $results->url(1)  }}" class="page-link">
							<i class="fas fa-angle-double-right"></i>
						</a>
					</li>
					<li class="paginate_button page-item previous">
						<a href="{{ $results->previousPageUrl() }}" class="page-link">
							<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					@endif
					@for ($i = 1; $i <= $results->lastPage(); $i++)
						<?php
						$half_total_links = floor($link_limit / 2);
						$from = $results->currentPage() - $half_total_links;
						$to = $results->currentPage() + $half_total_links;
						if ($results->currentPage() < $half_total_links) {
							$to += $half_total_links - $results->currentPage();
						}
						if ($results->lastPage() - $results->currentPage() < $half_total_links) {
							$from -= $half_total_links - ($results->lastPage() - $results->currentPage()) - 1;
						}
						?>
						@if ($from < $i && $i < $to) <li class="paginate_button page-item {{ ($results->currentPage() == $i) ? ' active' : '' }}">
							<a href='{{ $results->url("$i") }}' class="page-link">{{ $i }}</a>
							</li>
							@endif
							@endfor
							@if ($results->hasMorePages())
							<li class="paginate_button page-item next ">
								<a href="{{ $results->nextPageUrl() }}" class="page-link">
									<i class="fas fa-chevron-left"></i>
									
								</a>
							</li>
							<li class="paginate_button page-item last">
								<a href="{{ $results->url($results->lastPage()) }}" class="page-link">
								
									<i class="fas fa-angle-double-left"></i>
								</a>
							</li>
							@else
							<li class="paginate_button page-item next disabled">
								<a href="#" class="page-link">
									<i class="fas fa-chevron-left"></i>
								</a>
							</li>
							@endif
				</ul>
			</div>
			@endif
		</div>
	</div>
</div>

<script>
	function page_limit() {
    $(".pagination_form").submit();
}
</script>