<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

Route::get("/search", function () {
    return view("search");
});

// Route::get("/api/favicon", function () {
//     $scraper = new \Wonoly\IconScraper\Scraper();
//     $url = $_GET["url"];

//     if (!empty($url) and filter_var($url, FILTER_VALIDATE_URL)) {
//         $icons = $scraper->get($url);

//         if (sizeof($icons) > 0) {
//             return redirect($icons[0]->getHref());
//         }
//     }

//     return redirect("/default.svg");
// });
