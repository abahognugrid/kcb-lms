<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerOva;
use Illuminate\Http\Request;
use App\Models\PartnerApiSetting;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderByDesc("id")->get();
        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function edit(Request $request, Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function show(Request $request, Partner $partner)
    {
        $ova_settings = $partner->ova_setting;

        if (! $ova_settings) {
            $ova_settings = new PartnerOva();
        }

        $api_setting = $partner->api_setting;

        if (! $api_setting) {
            $api_setting = new PartnerApiSetting();
        }

        return view('partners.show', compact('partner', 'ova_settings', 'api_setting'));
    }

    public function saveOvaConfigs(Request $request)
    {
        $records = $request->validate(PartnerOva::rules());

        PartnerOva::updateOrCreate([
            "partner_id" => $request->user()->partner_id
        ], $records);
        // We clear ova settings as they are cached in the Payment Manager.

        $request->user()?->partner?->switches->each(function ($switch) {
            cache()->forget("ova_settings_{$switch->partner_id}_" . strtolower($switch->name));
        });

        session()->flash("success", "Partner OVA config updated successfully");

        return redirect()->route("partner.show", $request->user()->partner_id);
    }

    public function saveApiConfigs(Request $request, Partner $partner)
    {
        $api = $partner->api_setting;

        if (! $api) {
            $api = new PartnerApiSetting();
        }

        try {
            $details = $request->validate(PartnerApiSetting::rules());
            $details['api_key'] = str()->random(32);
            $details['partner_id'] = $partner->id;

            $api->fill($details);
            $api->save();

            session()->flash("success", "Partner api key updated successfully");
        } catch (\Throwable $th) {
            session()->flash("error", $th->getMessage());
        }
        return redirect()->route("partner.show", Auth::user()->partner_id);
    }

    public function deleteApiConfigs(Request $request)
    {
        try {
            $partner = Partner::where('id', Auth::user()->partner_id)->first();
            $api_settings = $partner->api_setting;
            if (!$api_settings) {
                session()->flash("info", "No further action performed here");
            } else {
                $api_settings->delete();
                session()->flash("success", "Partner api key deleted successfully");
            }
        } catch (\Throwable $th) {
            session()->flash("error", $th->getMessage());
        }

        return redirect()->route("partner.show", Auth::user()->partner_id);
    }

    public function delete(Request $request, Partner $partner)
    {
        try {
            $partner->delete();
            session()->flash("success", "Partner deleted successfully");
        } catch (\Throwable $th) {
            session()->flash("error", "Partner cannot be deleted as it is in use");
        }
        return back();
    }
}
