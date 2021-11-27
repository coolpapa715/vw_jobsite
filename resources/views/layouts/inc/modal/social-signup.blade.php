<div class="modal fade" id="socialFillSignup" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-sm">
		<div class="modal-content">
			
			<div class="modal-header">
				<!-- <h4 class="modal-title"><i class="icon-login fa"></i> {{ t('log_in') }} </h4> -->
				
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
			</div>
			
			<div class="signup-wrap">
				
				<input type="hidden" name="language_code" value="{{ config('app.locale') }}">
				<div class="modal-body">
					<div class="signup-heading">
						<img src="{{ asset('/images/site_logo.png') }}" title="" />
					</div>
					<!-- screen two -->
					<div class="social-signup-two">
						<div class="signup-heading">{{ t('sign_up') }}</div>
						<form role="form" method="POST" action="/auth/social/signup">
							{!! csrf_field() !!}
							<?php 
								$socialEmail = session()->has('email') ?  session()->get('email') : '';
							?>
							<input type="hidden" name="email" value="{{$socialEmail}}" />
							<!-- firstname -->
							<div class="form-group row required">
								<div class="col-md-12">
									<div class="input-group show-pwd-group">
										<?php 
											$socialFirstName = session()->has('first_name') ?  session()->get('first_name') : '';
										?>
										<input type="text" class="form-control" placeholder="{{ t('first_name') }}" autocomplete="off" value="{{$socialFirstName}}" disabled="disabled">
										<span class="icon-append show-pwd">
											<button type="button" class="first_name_w">
												<img src="{{ asset('/images/name_icon.png') }}" class="input-img" />
											</button>
										</span>
									</div>
								</div>
							</div>
							<!-- lastname -->
							<div class="form-group row required">
								<div class="col-md-12">
									<div class="input-group show-pwd-group">
										<?php 
											$socialLastName = session()->has('first_name') ?  session()->get('first_name') : '';
										?>
										<input type="text" class="form-control" placeholder="{{ t('last_name') }}" autocomplete="off" value="{{$socialLastName}}" disabled="disabled">
										<span class="icon-append show-pwd">
											<button type="button" class="last_name_w">
												<img src="{{ asset('/images/name_icon.png') }}" class="input-img" />
											</button>
										</span>
									</div>
								</div>
							</div>
							<!-- country -->
							<div class="form-group row required">
								<div class="col-md-12">
									<div class="input-group show-pwd-group">
										<input  name="country" type="text" class="form-control" placeholder="Philippines" autocomplete="off" value="Philippines" disabled="disabled">
										<span class="icon-append show-pwd">
											<button type="button" class="country_w">
												<img src="{{ asset('/images/country_icon.png') }}" class="input-img" />
											</button>
										</span>
									</div>
								</div>
							</div>
							<!-- country -->
							<div class="form-group row required">
								<div class="col-md-12">
									<div class="input-group show-pwd-group">
										<div class="input-group">
											<select id="city_s" name="city" class="form-control" >
												<?php $cities = \App\Helpers\PhilippineCities::getCities(); ?>
												@foreach( $cities as $city)
													<option value="{{$city->id}}"> {{$city->city}}</option>
												@endforeach
												
											</select>
											<span class="icon-append show-pwd">
												<button type="button" class="city_s">
													<img src="{{ asset('/images/city_icon.png') }}" class="input-img-city" />
												</button>
											</span>
										</div>
									</div>
								</div>
							</div>

							<div class="alert alert-danger print-error-msg-two" style="display:none">
						        <ul></ul>
						    </div>

							<!-- Button  -->
							<div class="form-group row mt-5 mb-5">
								<div class="col-md-12">
									<button type="submit" id="signupBtnWSecondSocial" class="btn btn-custom btn-lg "> {{ t('submit') }} </button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
