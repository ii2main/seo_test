<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About project

Test project for the job application made by Ivan Broshchak for Softoria.

## Running project

For run this project locally, follow these steps:

1. Clone the repository: `git clone https://github.com/ii2main/seo_test.git`
2. Install dependencies: `composer install`
3. Configure environment variables: Copy `.env.example` to `.env` and update the necessary settings.
Additionally:

       DATAFORSEO_API_KEY=your_api_key or password
       DATAFORSEO_API_LOGIN=your_api_login or email
       DATAFORSEO_DEPTH=10...20

4. Run database migrations: `php artisan migrate`
5. Start the development server: `php artisan serve`

## How to use

1. Add your domain names on the Domains page.
2. Get locations from DataForSeo service using the Get/Refresh button on the Locations page.
3. Get languages from DataForSeo service using the Get/Refresh button on the Languages page.
4. To get the rank, use the "New Check" button on Ranks page. On the form that appears, select a domain, location, and language, and enter a keyword. After making a check task, you will be able to get the rank result in the ranks table using the "Get Result" button.
