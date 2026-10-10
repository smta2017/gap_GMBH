<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;

class LocaleController extends Controller
{
    public function index()
    {
        $languages = Language::where('active', '1')->select(['name', 'code'])->get();

        return response()->json([
            'status' => true,
            'languages' => $languages
        ]);
    }

    public function set_locale($locale)
    {
        $language = Language::where('active', '1')->where('code', $locale)->first();

        if(!$language)
        {
            return response()->json([
                'status' => false
            ]);
        }

        $user = request()->user();

        if($user)
        {
            $user->setLocale($locale);
        }

        app()->setLocale($locale);

        return response()->json([
            'status' => true
        ]);
    }
}
