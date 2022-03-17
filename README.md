## 1. Set up local development environment

* Ubuntu 21.10 preparations:

		$ sudo apt install apache2 php mysql-server composer curl php-curl php-mysql php-xml

* Versions in use:

	* Apache HTTP 2.4.48
	* PHP 8.0.8
	* MySQL 8.0.28
	* Composer 2.0.9

* MySQL preparations:

		CREATE USER 'demo-admin'@'localhost' IDENTIFIED BY '*****';
		CREATE DATABASE `tm-demo_job_listings`;
		GRANT ALL PRIVILEGES ON `tm-demo_job_listings`.* TO 'demo-admin'@'localhost';
		FLUSH PRIVILEGES;

## 2. Set up a fresh Laravel app

* Create work directory:

		$ mkdir /home/user/tm-demo && cd $_

* Create app directory *job_listings* with additional git directory:

		$ composer global require laravel/installer
		$ export PATH=$PATH:/home/user/.config/composer/vendor/bin
		$ laravel new job_listings --git
		$ cd job_listings
	
* Version in use:

	* Laravel 9.1.2

* Edit config file *.env*:

		APP_NAME="TM-Demo Job Listings"
		APP_URL=http://localhost
		DB_DATABASE=tm-demo_job_listings
		DB_USERNAME=demo-admin
		DB_PASSWORD=*****

* Run initial migrations:

		$ php artisan migrate
	
* Set up git:

		$ gh repo create tm-demo --public
		$ git init
		$ git add .
		$ git commit -m "Step 1. Set up a fresh Laravel app"
		$ git branch -M main
		$ git remote add tm-demo https://github.com/stefan-helmhagen/tm-demo.git
		$ git push -u tm-demo main

## 3. Set up Apache HTTP

* Edit */etc/apache2/sites-available/000-default.conf* to redirect *http://localhost/* to:

		DocumentRoot /home/user/tm-demo/job_listings/public

* Set file permissions:

		$ chmod 755 /home/user
		$ chmod -R 755 /home/user/tm-demo/job_listings/*
		$ chmod -R 777 /home/user/tm-demo/job_listings/storage/*

* Add directives to */etc/apache2/apache2.conf*:

		<Directory /home/user/tm-demo/job_listings/>
			Options FollowSymLinks
			AllowOverride All
			Require all granted
		</Directory>

* Enable *mod_rewrite* and restart to apply changes:

		$ sudo a2enmod rewrite
		$ sudo systemctl restart apache2

## 4. Design RESTful API

* Requests to implement:

		[GET|POST]					/api/jobs
		[GET|PATCH|PUT|DELETE]		/api/jobs/{job}
		[GET|POST]					/api/companies
		[GET|PATCH|PUT|DELETE]		/api/companies/{company}
		[GET|POST]					/api/users
		[GET|PATCH|PUT|DELETE]		/api/users/{user

		[GET]						/api/jobs/{job}/companies
		[GET]						/api/companies/{company}/jobs
		[GET]						/api/companies/{company}/users
		[GET]						/api/users/{user}/companies

## 5. Define routes

* Edit */home/user/tm-demo/job_listings/routes/api.php*
		
		use App\Http\Controllers\JobController;
		use App\Http\Controllers\CompanyController;
		use App\Http\Controllers\UserController;

		Route::get('jobs', [JobController::class, 'index']);											// show all jobs
		Route::post('jobs', [JobController::class, 'store']);											// store new jobs
		Route::get('jobs/{job}', [JobController::class, 'show']);										// show one job in detail
		Route::match(['patch', 'put'],'jobs/{job}', [JobController::class, 'update']);					// edit one job
		Route::delete('jobs/{job}', [JobController::class, 'destroy']);									// delete one job

		Route::get('companies', [CompanyController::class, 'index']);									// show all companies
		Route::post('companies', [CompanyController::class, 'store']);									// store new companies
		Route::get('companies/{company}', [CompanyController::class, 'show']);							// show one company in detail && all of its jobs
		Route::match(['patch', 'put'],'companies/{company}', [CompanyController::class, 'update']);		// edit one company
		Route::delete('companies/{company}', [CompanyController::class, 'destroy']);					// delete one company

		Route::get('users', [UserController::class, 'index']);											// show all users
		Route::post('users', [UserController::class, 'store']);											// store new users
		Route::get('users/{user}', [UserController::class, 'show']);									// show one user in detail && all of his/her jobs
		Route::match(['patch', 'put'],'users/{user}', [UserController::class, 'update']);				// edit one user
		Route::delete('users/{user}', [UserController::class, 'destroy']);								// delete one user

		Route::get('jobs/{job}/companies/', [JobController::class, 'showCompany']);						// show the company of a job
		Route::get('companies/{company}/jobs', [CompanyController::class, 'showJobs']);					// show all jobs of a company
		Route::get('companies/{company}/users', [CompanyController::class, 'showUsers']);				// show all users of a company
		Route::get('users/{user}/companies', [UserController::class, 'showCompanies']);					// show all companies of a user

## 6. Create stubs

* Create stubs of models (with migration, controller and resources) and policies for *Job* and *Company*:

		$ php artisan make:model Job -mcr
		$ php artisan make:policy JobPolicy
		$ php artisan make:model Company -mcr
		$ php artisan make:policy CompanyPolicy

* Create stubs of migration for the pivot table between *companies* and *users*:

		$ php artisan make:migration create_companies_users_table

* For *User* a model stub, migrations and a table *users* have already been created during the initial installation of Laravel and will be utilized as well. Just controller and policy need to be added, and an additional stub of migration to alter the table for uniformity:

		$ php artisan make:controller UserController --resource
		$ php artisan make:policy UserPolicy
		$ php artisan make:migration add_deleted_at_to_users_table

## 7. Define models

* Define columns in */home/user/tm-demo/job_listings/database/migrations/Y_m_d_His_create_jobs_table.php*:

		public function up()
		{
			Schema::create('jobs', function (Blueprint $table) {
				$table->id();
				$table->unsignedBigInteger('company_id');
				$table->unsignedBigInteger('created_by_user_id');
				$table->unsignedBigInteger('updated_by_user_id');
				$table->string('title');
				$table->longText('description');
				$table->string('location');
				$table->string('contact_name')->nullable();
            	$table->string('contact_email')->nullable();
            	$table->string('contact_phone')->nullable();
				$table->timestamps();
				$table->softdeletes();
			});
		}

* Define columns in */home/user/tm-demo/job_listings/database/migrations/Y_m_d_His_create_companies_table.php*:

		public function up()
		{
			Schema::create('companies', function (Blueprint $table) {
				$table->id();
				$table->string('name');
				$table->string('location');
				$table->timestamps();
				$table->softdeletes();
			});
		}

* Define columns in */home/user/tm-demo/job_listings/database/migrations/Y_m_d_His_create_companies_jobs_table.php*:

		public function up()
		{
			Schema::create('companies_users', function (Blueprint $table) {
				$table->unsignedBigInteger('company_id');
				$table->unsignedBigInteger('user_id');
			});
		}

* Add a column for softdeletes to */home/user/tm-demo/job_listings/database/migrations/Y_m_d_His_add_deleted_at_to_users_table* and a corresponding rollback:

		public function up()
		{
			Schema::table('users', function (Blueprint $table) {
				$table->softdeletes();
			});
		}

		public function down()
		{
			Schema::table('users', function (Blueprint $table) {
				$table->dropColumn('deleted_at');
			});
		}

* Run migrations:

		$ php artisan migrate

## 8. Incorporate relationships into models

* Company <-> Job is "one to many"
* Company <-> User is "many to many"

* add to */home/user/tm-demo/job_listings/app/Models/Job.php* a function to access the company that a job belongs to:

		protected $fillable = ['company_id', 'created_by_user_id', 'updated_by_user_id', 'title', 'description', 'location'];
    	protected $guarded = ['id'];

    	public function company() {
        	return $this->belongsTo(Company::class);
    	}

* add to */home/user/tm-demo/job_listings/app/Models/Company.php* a function to access the jobs that belong to the company, and another one to access its users:

		protected $fillable = ['name', 'location'];
		protected $guarded = ['id'];

		public function jobs() {
			return $this->hasMany(Job::class);
		}

		public function users() {
			return $this->belongsToMany(User::class, 'companies_users');
		}

* add to */home/user/tm-demo/job_listings/app/Models/User.php* a function to access the companies that belong to the user:

		public function companies() {
			return $this->belongsToMany(Company::class, 'companies_users');
		}

## 9. Seed some initial datasets

* Create a stub of a seeder for the table *jobs*:

		$ php artisan make:seeder JobSeeder

* Add datasets in */home/user/tm-demo/job_listings/database/seeders/JobSeeder.php*:

		use Illuminate\Support\Facades\DB;
		...
		public function run()
		{
			DB::table('jobs')->insert([
				'company_id'			=> '1',
				'created_by_user_id'	=> '1',
				'updated_by_user_id'	=> '1',
				'title'                 => 'Web Developer',
				'description'           => 'We are looking for a frontend developer for our team. At ACME we work on our own projects and develop internal tools.',
				'location'              => 'Anytown',
				'contact_name'          => 'John Doe',
				'contact_email'         => 'john.doe@acme.com',
				'contact_phone'         => '555-1',
				'created_at'            => now(),
				'updated_at'            => now()
			]);
			DB::table('jobs')->insert([
				'company_id'         	=> '2',
				'created_by_user_id'   	=> '2',
				'updated_by_user_id'   	=> '2',
				'title'                 => 'Content Manager',
				'description'           => 'We are looking for a content manager for our team. At Contoso we work on our own projects and develop internal tools.',
				'location'              => 'Redmond',
				'contact_name'          => 'John Random',
				'contact_email'         => 'john.random@contoso.com',
				'contact_phone'         => '555-2',
				'created_at'            => now(),
				'updated_at'            => now()
			]);
			DB::table('jobs')->insert([
				'company_id'          	=> '3',
				'created_by_user_id'   	=> '3',
				'updated_by_user_id'   	=> '3',
				'title'                 => 'Database Administrator',
				'description'           => 'We are looking for a database administrator for our team. At Oceanic Airlines we work on our own projects and develop internal tools.',
				'location'              => 'Westpoint',
				'contact_name'          => 'John Smith',
				'contact_email'         => 'john.smith@oceanic.com',
				'contact_phone'         => '555-3',
				'created_at'            => now(),
				'updated_at'            => now()
			]);
		}

* Create a stub of a seeder for the table *companies*:

		$ php artisan make:seeder CompanySeeder

* Add datasets in */home/user/tm-demo/job_listings/database/seeders/CompanySeeder.php*:

		use Illuminate\Support\Facades\DB;
		...
		public function run()
		{
			DB::table('companies')->insert([
				'name'          => 'ACME',
				'location'      => 'Allentown',
				'created_at'    => now(),
				'updated_at'    => now()
			]);
			DB::table('companies')->insert([
				'name'          => 'Contoso',
				'location'      => 'Charlotte',
				'created_at'    => now(),
				'updated_at'    => now()
			]);
			DB::table('companies')->insert([
				'name'          => 'Oceanic Airlines',
				'location'      => 'Odessa',
				'created_at'    => now(),
				'updated_at'    => now()
			]);
		}

* Create a stub of a seeder for the pivot table *companies_users*:

		$ php artisan make:seeder CompanyUserSeeder

* Add datasets in */home/user/tm-demo/job_listings/database/seeders/CompanyUserSeeder.php*:

		use Illuminate\Support\Facades\DB;
		...
		public function run()
		{
			DB::table('companies_users')->insert([
				'company_id'	=> '1',
				'user_id'		=> '1'
			]);
			DB::table('companies_users')->insert([
				'company_id'	=> '2',
				'user_id'		=> '2'
			]);
			DB::table('companies_users')->insert([
				'company_id'	=> '3',
				'user_id'		=> '3'
			]);
		}

* Add the seeders to */home/tm-demo/job_listings/database/seeders/DatabaseSeeder.php* and let a factory generate 3 users:

		public function run()
		{
			$this->call(JobSeeder::class);
			$this->call(CompanySeeder::class);
			$this->call(CompanyUserSeeder::class);
			\App\Models\User::factory(3)->create();
		}

* Run seeds:

		$ php artisan db:seed

## 10. Define controllers

* Edit */home/user/tm-demo/job_listings/app/Http/Controllers/JobController.php*:

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

* Edit */home/user/tm-demo/job_listings/app/Http/Controllers/CompanyController.php*:

		<?php

		namespace App\Http\Controllers;

		use Illuminate\Http\Request;
		use App\Models\Company;
		use Illuminate\Support\Facades\DB;

		class CompanyController extends Controller
		{
			/**
			* Display a listing of the resource.
			*
			* @return \Illuminate\Http\Response
			*/
			public function index()
			{
				return DB::table('companies')->get();
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
				$company = new Company([
					'name'      => $request->name,
					'location'  => $request->location
				]);
				$company->save();
			}

			/**
			* Display the specified resource.
			*
			* @param  Company $company
			* @return \Illuminate\Http\Response
			*/
			public function show(Company $company)
			{
				return $company;
			}

			/**
			* Display all jobs related to the specified company.
			* 
			* @param  Company $company
			* @return \Illuminate\Http\Response
			*/
			public function showJobs(Company $company)
			{
				return $company->jobs;
			}

			/**
			* Display all users related to the specified company.
			* 
			* @param  Company $company
			* @return \Illuminate\Http\Response
			*/
			public function showUsers(Company $company)
			{
				return $company->users;
			}

			/**
			* Show the form for editing the specified resource.
			*
			* @param  \App\Models\Company  $company
			* @return \Illuminate\Http\Response
			*/
			public function edit(Company $company)
			{
				//
			}

			/**
			* Update the specified resource in storage.
			*
			* @param  \Illuminate\Http\Request  $request
			* @param  \App\Models\Company  $company
			* @return \Illuminate\Http\Response
			*/
			public function update(Request $request, Company $company)
			{
				is_null($request->name) ?       : $company->name = $request->name;
				is_null($request->location) ?   : $company->location = $request->location;
				$company->save();
			}

			/**
			* Remove the specified resource from storage.
			*
			* @param  \App\Models\Company  $company
			* @return \Illuminate\Http\Response
			*/
			public function destroy(Company $company)
			{
				$company->delete();
			}
		}

* Edit */home/user/tm-demo/job_listings/app/Http/Controllers/UserController.php*:

		<?php

		namespace App\Http\Controllers;

		use Illuminate\Http\Request;
		use App\Models\User;
		use Illuminate\Support\Facades\DB;

		class UserController extends Controller
		{
			/**
			* Display a listing of the resource.
			*
			* @return \Illuminate\Http\Response
			*/
			public function index()
			{
				return DB::table('users')->get();
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
				//
			}

			/**
			* Display the specified resource.
			*
			* @param  \App\Models\User  $user
			* @return \Illuminate\Http\Response
			*/
			public function show(User $user)
			{
				return $user;
			}

			/**
			* Display the companies of the specified user.
			*
			* @param  \App\Models\User  $user
			* @return \Illuminate\Http\Response
			*/
			public function showCompanies(User $user)
			{
				return $user->companies;
			}

			/**
			* Show the form for editing the specified resource.
			*
			* @param  \App\Models\User  $user
			* @return \Illuminate\Http\Response
			*/
			public function edit(User $user)
			{
				//
			}

			/**
			* Update the specified resource in storage.
			*
			* @param  \Illuminate\Http\Request  $request
			* @param  \App\Models\User  $user
			* @return \Illuminate\Http\Response
			*/
			public function update(Request $request, User $user)
			{
				//
			}

			/**
			* Remove the specified resource from storage.
			*
			* @param  \App\Models\User  $user
			* @return \Illuminate\Http\Response
			*/
			public function destroy(User $user)
			{
				$user->delete();
			}
		}

## 11. Testing with curl

* To retrieve all jobs:

		curl -X GET http://localhost/api/jobs

* To retrieve one job with specific id:

		curl -X GET http://localhost/api/jobs/1

* To retrieve the company related to a job with specific id:

		curl -X GET http://localhost/api/jobs/1/companies

* To retrieve all companies:

		curl -X GET http://localhost/api/companies

* To retrieve one company with specific id:

		curl -X GET http://localhost/api/companies/1

* To retrieve the users related to a company with specific id:

		curl -X GET http://localhost/api/companies/1/users

* To retrieve all users: [TODO] includes 'password' and 'remember_token' in output!

		curl -X GET http://localhost/api/users

* To retrieve one user with specific id:

		curl -X GET http://localhost/api/users/1

* To retrieve the companies related to a user with specific id:

		curl -X GET http://localhost/api/users/1/companies

* To create new jobs: [TODO] default values, validation, error handling, authorization & authentication

		curl -X POST -d '{"company_id":1,"title":"Database Developer","description":"Insert long description here","location":"Newcastle"}' localhost/api/jobs -H 'Content-Type: application/json'

* To create new companies: [TODO] default values, validation, error handling, authorization & authentication

		curl -X POST -d '{"name":"NewCo","location":"Newcastle"}' localhost/api/companies -H 'Content-Type: application/json'

* To create new user: [TODO]

* To update a job with specific id: [TODO] default values, validation, error handling, authorization & authentication

		curl -X PUT -d '{"company_id":1,"title":"Updated Web Developer","description":"Updated long description here","location":"Updated location"}' localhost/api/jobs/1 -H 'Content-Type: application/json'

* To update a company with specific id: [TODO] default values, validation, error handling, authorization & authentication

		curl -X PUT -d '{"name":"Updated Company Name","location":"Updated location"}' localhost/api/companies/1 -H 'Content-Type: application/json'

* To update a user with specific id: [TODO]

* To delete a job with specific id: [TODO] error handling, authorization & authentication

		curl -X DELETE http://localhost/api/jobs/1

* To delete a company with specific id: [TODO] error handling, authorization & authentication

		curl -X DELETE http://localhost/api/companies/1

* To delete a user with specific id: [TODO] error handling, authorization & authentication

		curl -X DELETE http://localhost/api/users/1