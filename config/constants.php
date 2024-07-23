<?php
$WEBSITE_URL				=	env("APP_URL");
$NODE_WEB_URL 				= env('NODE_APP_URL');
return [
	'ALLOWED_TAGS_XSS'   						=>  '<iframe><a><strong><b><p><br><i><font><img><h1><h2><h3><h4><h5><h6><span><div><em><table><ul><li><section><thead><tbody><tr><td><figure><article>',
	'DS'     									=>  '/',
	'ROOT'     									=>  base_path(),
	'APP_PATH'     								=>  app_path(),
	'WEBSITE_URL'                           	=>  $WEBSITE_URL,
    'NODE_WEBSITE_URL'                      	=>  $NODE_WEB_URL,
	'CURRENCY_SIGN' 							=> '₪',

	"LANGUAGE_IMAGE_PATH"						=>	$WEBSITE_URL.'public/uploads/language_image/',
	"LANGUAGE_IMAGE_ROOT_PATH"					=>	"public/uploads/language_image/",
	
	"CUSTOMER_IMAGE_PATH"						=>	$WEBSITE_URL.'public/uploads/Customer-image/',
	"CUSTOMER_IMAGE_ROOT_PATH"					=>	"public/uploads/Customer-image/",

	"CONTACT_PERSON_PROFILE_IMAGE_PATH"			=>	$WEBSITE_URL.'public/uploads/contact-person-profile-image/',
	"CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH"	=>	"public/uploads/contact-person-profile-image/",

	"GALLERY_MEDIA_IMAGE"						=>	$WEBSITE_URL.'public/uploads/gallery-image/',
	"GALLERY_MEDIA_IMAGE_ROOT_PATH"				=>	"public/uploads/gallery-image/",

	"COMPANY_LOGO_IMAGE_PATH"					=>	$WEBSITE_URL.'public/uploads/company-logo-image/',
	"COMPANY_LOGO__IMAGE_ROOT_PATH"				=>	"public/uploads/company-logo-image/",

	"USER_IMAGE_PATH"							=>	$WEBSITE_URL.'public/uploads/User-image/',
	"USER_IMAGE_ROOT_PATH"						=>	"public/uploads/User-image/",

	"TRUCK_INSURANCE_IMAGE_PATH"				=>	$WEBSITE_URL.'public/uploads/truck-insurance-picture/',
	"TRUCK_INSURANCE_IMAGE_ROOT_PATH"			=>	"public/uploads/truck-insurance-picture/",

	
	"TRUCK_LICENCE_NUMBER_IMAGE_PATH"			=>	$WEBSITE_URL.'public/uploads/truck-licence-number/',
	"TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH"		=>	"public/uploads/truck-licence-number/",

	"SEO_PAGE_IMAGE_IMAGE_PATH"		 			=>	$WEBSITE_URL.'public/uploads/sep-image/',
	"SEO_PAGE_IMAGE_ROOT_PATH"					=>	"public/uploads/sep-image/",

	"LICENCE_PICTURE_PATH"						=>	$WEBSITE_URL.'public/uploads/licence_picture/',
	"LICENCE_PICTURE_ROOT_PATH"					=>	"public/uploads/licence_picture/",


	"DRIVER_PICTURE_PATH"						=>	$WEBSITE_URL.'public/uploads/driver_picture/',
	"DRIVER_PICTURE_ROOT_PATH"					=>	"public/uploads/driver_picture/",


	"OURSERVICE_PATH"						    =>	$WEBSITE_URL.'public/uploads/our_services/',
	"OURSERVICE_ROOT_PATH"					    =>	"public/uploads/our_services/",

	"PLAN_IMAGE_PATH"					        	    =>	$WEBSITE_URL.'public/uploads/plan/',
	"PLAN_IMAGE_ROOT_PATH"			        		=>	"public/uploads/plan/",

	
	"SLIDER_IMAGE_PATH"							=>	$WEBSITE_URL.'public/uploads/slider-image/',
	"SLIDER_IMAGE_ROOT_PATH"					=>	"public/uploads/slider-image/",

	"ABOUT_US_IMAGE_PATH"						=>	$WEBSITE_URL.'public/uploads/AboutUs-image/',
	"ABOUT_US_IMAGE_ROOT_PATH"					=>	"public/uploads/AboutUs-image/",

	"ABOUT_US_GOAL_IMAGE_PATH"					=>	$WEBSITE_URL.'public/uploads/AboutUsgoal-image/',
	"ABOUT_US_GOAL_IMAGE_ROOT_PATH"				=>	"public/uploads/AboutUsgoal-image/",

	"CLIENT_IMAGE_PATH"							=>	$WEBSITE_URL.'public/uploads/Client-image/',
	"CLIENT_IMAGE_ROOT_PATH"					=>	"public/uploads/Client-image/",

	"TEAM_IMAGE_PATH"							=>	$WEBSITE_URL.'public/uploads/Team-image/',
	"TEAM_IMAGE_ROOT_PATH"						=>	"public/uploads/Team-image/",

	"ACHIEVMENT_IMAGE_PATH"						=>	$WEBSITE_URL.'public/uploads/Achievment-image/',
	"ACHIEVMENT_IMAGE_ROOT_PATH"				=>	"public/uploads/Achievment-image/",

	"TRUCK_IMAGE_PATH"							=>	$WEBSITE_URL.'public/uploads/truck-picture/',
	"TRUCK_IMAGE_ROOT_PATH"						=>	"public/uploads/truck-picture/",

	"NO_IMAGE_PATH"								=>	$WEBSITE_URL.'public/img/noimage.png',
	"NO_TRUCK_IMAGE_PATH"						=>	$WEBSITE_URL.'public/img/noTruckImage.png',

	

	"PASSWORD_VALIDATION_STRING"				=>	'/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$\-!%#?&])[A-Za-z\d@$\-!%#?&]{4,8}$/',
	"PASSWORD_VALIDATION_MESSAGE_STRING"		=>	"password_must_required_at_least_one_uppercase_one_lowercase_one_digit_and_one_special_character",

	"MOBILE_VALIDATION_STRING"					=>	'/^0\d{9}$/',
	"MOBILE_VALIDATION_MESSAGE_STRING"			=>	"phone_number_should_be_9_digits_and_should_be_start_with_0",

	"COMPANY_HP_NUMBER_STRING"					=> '/^[0-9]+$/',
	"COMPANY_HP_NUMBER_MESSAGE_STRING"			=> "only_digits_are_allowed",

	"MESSAGE_IMAGES_IMAGE_PATH"		            =>	$WEBSITE_URL.'public/uploads/message-images/',
	"MESSAGE_IMAGES_ROOT_PATH"	            	=>	"public/uploads/message-images/",

	"SIGNATURE_IMAGE_PATH"		            	=>	$WEBSITE_URL.'public/uploads/signature-images/',
	"SIGNATURE_IMAGES_ROOT_PATH"	            =>	"public/uploads/signature-images/",

	"INVOICE_FILE_PATH"							=>	$WEBSITE_URL.'public/uploads/invoices/',
	"INVOICE_FILE_ROOT_PATH"					=>	"public/uploads/invoices/",

	'MESSAGE' => [
		'INACTIVE_MEMBER_STAFF' => "You can't login in site panel, please contact to site admin!",
	],

	'GENDER' => [
		'1' 	=> "Men",
		'2' 	=> "Women",
		'0' 	=> "Other",
	],

	'CUSTOMER' => [
		'CUSTOMERS_TITLE' 	=> "Customer",
		'CUSTOMERS_TITLES' 	=> "Customers",
	],

	'HOMEPAGE' => [
		'HOMEPAGE_TITLE' 	=> "Homepage Slider",
		'HOMEPAGE_TITLES' 	=> "Homepage Sliders",
	],



	'PRIVATE_CUSTOMER' => [
		'PRIVATE_CUSTOMER_TITLE' 	=> "Private Customer",
		'PRIVATE_CUSTOMER_TITLES' 	=> "Private Customers",
	],

	'TEAM' => [
		'TEAM_TITLE' 	=> "Team",
		'TEAM_TITLES' 	=> "Teams",
	],

	'ACHIEVMENT' => [
		'ACHIEVMENT_TITLE' 	=> "Achievment",
		'ACHIEVMENT_TITLES' 	=> "Achievments",
	],

	'SEO' => [
		'SEO_TITLE' 	=> "Seo pages",
	],

	'CMS_MANAGER' => [
		'CMS_PAGES_TITLE' 	=> "Cms Pages",
		'CMS_PAGE_TITLE' 	=> "Cms Page",
		'VIEW_PAGE' 		=> "View Page",
	],

	'PLAN' => [
		'PLAN_TITLE'	 => "Plan",
		'PLANS_TITLE' => "Plans",
	],

	'PLAN_FEATURE' => [
		'PLAN_FEATURE_TITLE'	 => "Plan Feature",
		'PLAN_FEATURES_TITLE' => "Plan Features",
	],

	'CONTACT_ENQUIRY' =>[
		'CONTACT_ENQUIRY_TITLE' => 'Contact Enquiry',
		'CONTACT_ENQUIRY_TITLES' => 'Contact Enquiries'
	],

	'FAQ' => [
		'FAQ_TITLE'	 => "Faq",
		'FAQS_TITLE' => "Faq's",
		'VIEW_PAGE'  => "Faq View",
	],

	'FAQ_CATEGORY' => [
		'FAQ_CATEGORYS_TITLE' => "Faq Categorys",
		'FAQ_CATEGORY_TITLE' => "Faq Category ",
		'VIEW_PAGE' => "Faq Categorys View",
	],

	'OURSERVICE' => [
		'OURSERVICES_TITLE'	 => "Our Services",
		'OURSERVICE_TITLE'   => "Our Service",
	],

	
	'EMAIL_TEMPLATES' => [
		'EMAIL_TEMPLATES_TITLE' => "Email Templates",
		'EMAIL_TEMPLATE_TITLE' 	=> "Email Template",
	],

	'EMAIL_LOGS' => [
		'EMAIL_LOGS_TITLE' 		=> "Email Logs",
		'EMAIL_DETAIL_TITLE' 	=> "Email Detail",
	],

	'LANGUAGE_SETTING' => [
		'LANGUAGE_SETTINGS_TITLE' 	=> "Language Setting",
		'LANGUAGE_SETTING_TITLE' 	=> "Language Setting",
	],
	
	'ACL' => [
		'ACLS_TITLE' => "Acl",
		'ACL_TITLE' => "Acl Management",
	],

	'SETTING' => [
		'SETTINGS_TITLE' 	=> "Settings",
		'SETTING_TITLE' 	=> "Setting",
	],

	'DESIGNATION' => [
		'DESIGNATIONS_TITLE' 	=> "Roles",
		'DESIGNATION_TITLE' 	=> "Role",
	],

	'STAFF' => [
		'STAFFS_TITLE' 		=> "Staff's",
		'STAFF_TITLE' 		=> "Staff",
	],

	'CLIENT' => [
		'CLIENTS_TITLE' 		=> "Clients",
		'CLIENT_TITLE' 		=> "Client",
	],


	'TRANSACTION' => [
		'TRANSACTIONS_TITLE' 		=> "Transaction's",
		'TRANSACTION_TITLE' 		=> "Transaction",
	],

	'ROLE_ID' => [
		'ADMIN_ID' 					=> 1,
		'SUPER_ADMIN_ROLE_ID' 		=> 1,
		'CUSTOMER_ROLE_ID' 			=> 2,
		'STAFF_ROLE_ID' 			=> 3,
		'TRUCK_COMPANY_ID' 			=> 3,
		'TRUCK_COMPANY_DRIVER_ID' 	=> 4,
	],

	'DEFAULT_LANGUAGE' => [
		'FOLDER_CODE' 	=> 'he',
		'LANGUAGE_CODE' => 1,
		'LANGUAGE_NAME' => 'עִברִית',
	 'LANGUAGE_NAME_TWO_CHAR' => 'he'
	],
	// 'DEFAULT_LANGUAGE' => [
	// 	'FOLDER_CODE' 	=> 'he',
	// 	'LANGUAGE_CODE' => 2,
	// 	'LANGUAGE_NAME' => 'Hebrew',
	// 	'LANGUAGE_NAME_TWO_CHAR' => 'he'
	// ],

	// 'SHIPMENT_REQUEST_STATUS' => [
	// 	'new'              => '#ff7a1f',
	// 	'offers'           => '#1535B9',
	// 	'shipment'         => '#ff7a1f',
	// 	'offer_chosen'     => '#21760c',
	// 	'end'              => '#828db7',
	// 	'cancelled'        => '#EA3732',
	// ],

	// 'SHIPMENT_STATUS' => [
	// 	'shipment'         => '#ff7a1f',
	// 	'offer_chosen'     => '#21760c',
	// 	'new'              => '#ff7a1f',
	// 	'in_offer'         => '#1535B9',
	// ],

	'SETTING_FILE_PATH'	=> base_path() . "/" .'config'."/". 'settings.php',

	'WEBSITE_ADMIN_URL' => base_path() . "/" .'adminpnlx',

];
