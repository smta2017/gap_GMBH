<?php

use App\Helper\Helpers;
use App\Http\Controllers\Api\ProductController;
use App\Models\BasicTranslation;
use App\Models\Difficulty;
use App\Models\Facility;
use App\Models\FieldType;
use App\Models\GolfCourseStyle;
use App\Models\RoomFieldType;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('api/docs', function () {
    return view('vendors.swaggar.index');
});

// Protected Administrative Routes
Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {

    // DaVinci Admin UI & Import Endpoints
    Route::get('/davinci', function () {
        return view('davinci.index');
    });

    Route::get('/davinci-custom-import', function () {
        return view('davinci.custom_import');
    });

    Route::post('import-davinci-packages', [ProductController::class, "manualImportDaVinciPackages"]);
    Route::post('custom-import-packages', [ProductController::class, "manualCustomImport"]);
    Route::post('/clean-davinci-packages', [ProductController::class, "manualCleanDaVinciPackages"]);
    Route::delete('/davinci-delete-package', [ProductController::class, "deleteDaVinciPackage"]);
    Route::post('/get-import-info', [ProductController::class, "getImportInfo"]);

    // Services Management
    Route::get('/services', function () {
        $services = Service::orderBy('type')->get();
        return view('services', compact('services'));
    });

    Route::post('save-service', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:services,id',
            'type' => 'required|string',
        ]);

        $service = Service::find($request->id);
        $service->type = $request->type;
        $service->active = isset($request->active) ? 1 : 0;
        $service->icon = $request->icon;
        $service->icon_name = $request->icon_name;
        $service->font_type = $request->font_type;
        $service->sorted = $request->sorted;
        $service->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-service', function (Request $request) {
        $request->validate([
            'type' => 'required|string',
        ]);

        $service = new Service();
        $service->type = $request->type;
        $service->active = 0;
        $service->save();

        $service->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $service->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Facility Management
    Route::get('facility', function () {
        $facilitys = Facility::orderBy('type')->get();
        return view('facility', compact('facilitys'));
    });

    Route::post('save-facility', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:facilities,id',
            'type' => 'required|string',
        ]);

        $facility = Facility::find($request->id);
        $facility->type = $request->type;
        $facility->status = isset($request->status) ? 1 : 0;
        $facility->icon = $request->icon;
        $facility->icon_name = $request->icon_name;
        $facility->font_type = $request->font_type;
        $facility->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-facility', function (Request $request) {
        $request->validate([
            'type' => 'required|string',
        ]);

        $facility = new Facility();
        $facility->type = $request->type;
        $facility->status = 0;
        $facility->save();

        $facility->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $facility->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Fields Management
    Route::get('field', function () {
        $items = FieldType::orderBy('category_id')->get();
        return view('field', compact('items'));
    });

    Route::post('save-field', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:field_types,id',
            'category_id' => 'required',
        ]);

        $field = FieldType::find($request->id);
        $field->category_id = $request->category_id;
        $field->status = isset($request->status) ? 1 : 0;
        $field->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-field', function (Request $request) {
        $request->validate([
            'category_id' => 'required',
        ]);

        $field = new FieldType();
        $field->category_id = $request->category_id;
        $field->status = 0;
        $field->save();

        $field->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $field->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Room Fields Management
    Route::get('room-field', function () {
        $items = RoomFieldType::get();
        return view('room-field', compact('items'));
    });

    Route::post('save-room-field', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:room_field_types,id',
        ]);

        $field = RoomFieldType::find($request->id);
        $field->status = isset($request->status) ? 1 : 0;
        $field->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-room-field', function (Request $request) {
        $field = new RoomFieldType();
        $field->status = 0;
        $field->save();

        $field->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $field->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Difficulty Management
    Route::get('difficulty', function () {
        $items = Difficulty::get();
        return view('difficulty', compact('items'));
    });

    Route::post('save-difficulty', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:difficulties,id',
        ]);

        $item = Difficulty::find($request->id);
        $item->status = isset($request->status) ? 1 : 0;
        $item->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-difficulty', function (Request $request) {
        $item = new Difficulty();
        $item->status = 0;
        $item->save();

        $item->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $item->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Golf Style Management
    Route::get('golf-style', function () {
        $items = GolfCourseStyle::get();
        return view('golf-style', compact('items'));
    });

    Route::post('save-golf-style', function (Request $request) {
        $request->validate([
            'id' => 'required|exists:golf_course_styles,id',
        ]);

        $item = GolfCourseStyle::find($request->id);
        $item->status = isset($request->status) ? 1 : 0;
        $item->save();

        if (is_array($request->translates)) {
            foreach ($request->translates as $key => $value) {
                $translation = BasicTranslation::find($key);
                if ($translation) {
                    $translation->name = $value;
                    $translation->save();
                }
            }
        }

        return '<h1> Saved </h1>';
    });

    Route::post('new-golf-style', function (Request $request) {
        $item = new GolfCourseStyle();
        $item->status = 0;
        $item->save();

        $item->translations()->create(['name' => 'title_en', 'language_id' => 1, 'locale' => 'en']);
        $item->translations()->create(['name' => 'title_de', 'language_id' => 2, 'locale' => 'de']);
        return '<h1> Saved </h1>';
    });

    // Pulk Publish Management
    Route::get('/pulk_publish', function () {
        return view('publish');
    });

    Route::post('/pulk_publish', function () {
        return Helpers::pulk_publish();
    });

    // Admin Log Viewer & State-Changing Clear
    Route::get('show-log', function (Request $request) {
        return view('log');
    });

    Route::get('get-log', function (Request $request) {
        $filename = basename($request->input('filename', 'laravel'));
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '', $filename);
        $logPath = storage_path('logs/' . $filename . '.log');

        if (!file_exists($logPath)) {
            abort(404, 'Log file not found.');
        }

        $log = file($logPath);

        return view('log', compact('log'));
    });

    Route::post('clear-log', function (Request $request) {
        $filename = basename($request->input('filename', 'laravel'));
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '', $filename);
        $logPath = storage_path('logs/' . $filename . '.log');

        if (!file_exists($logPath)) {
            abort(404, 'Log file not found.');
        }

        \File::put($logPath, '');

        Log::warning('Log file cleared by authorized administrator', [
            'user_id' => optional(auth()->user())->id,
            'filename' => $filename,
        ]);

        $log = file($logPath);

        return view('log', compact('log'));
    });

    // Reset Rank Administrative Endpoint
    Route::post('reset-rank', function (Request $request) {
        Artisan::call('gap:reset-rank');

        Log::info('Image rank reset executed by authorized administrator', [
            'user_id' => optional(auth()->user())->id,
        ]);

        return response()->json(['message' => 'Image ranks reset successfully.']);
    });

});
