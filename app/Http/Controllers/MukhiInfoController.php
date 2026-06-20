<?php

namespace App\Http\Controllers;

use App\Models\MukhiInfo;
use Illuminate\Http\Request;

class MukhiInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:select_homepage']);
    }

    public function index()
    {
        $mukhi_infos = MukhiInfo::orderBy('mukhi_number', 'asc')->get();
        return view('backend.website_settings.mukhi_info.index', compact('mukhi_infos'));
    }

    public function create()
    {
        return view('backend.website_settings.mukhi_info.create');
    }

    public function store(Request $request)
    {
        $request->validate(['mukhi_number' => 'required|integer|min:1|unique:mukhi_infos,mukhi_number']);
        $mukhi = new MukhiInfo();
        $this->fill($mukhi, $request)->save();
        flash(translate('Mukhi info created successfully'))->success();
        return redirect()->route('mukhi-info.index');
    }

    public function edit($id)
    {
        $mukhi_info = MukhiInfo::findOrFail($id);
        return view('backend.website_settings.mukhi_info.edit', compact('mukhi_info'));
    }

    public function update(Request $request, $id)
    {
        $mukhi = MukhiInfo::findOrFail($id);
        $request->validate(['mukhi_number' => 'required|integer|min:1|unique:mukhi_infos,mukhi_number,' . $mukhi->id]);
        $this->fill($mukhi, $request)->save();
        flash(translate('Mukhi info updated successfully'))->success();
        return redirect()->route('mukhi-info.index');
    }

    public function destroy($id)
    {
        MukhiInfo::findOrFail($id)->delete();
        flash(translate('Mukhi info deleted successfully'))->success();
        return redirect()->route('mukhi-info.index');
    }

    private function fill(MukhiInfo $mukhi, Request $request): MukhiInfo
    {
        $mukhi->mukhi_number = $request->mukhi_number;
        $mukhi->deity = $request->deity;
        $mukhi->planet = $request->planet;
        $mukhi->mantra = $request->mantra;
        $mukhi->chakra = $request->chakra;
        $mukhi->significance = $request->significance;
        $mukhi->benefits_spiritual = $request->benefits_spiritual;
        $mukhi->benefits_mental = $request->benefits_mental;
        $mukhi->benefits_physical = $request->benefits_physical;
        $mukhi->wearing_day = $request->wearing_day;
        $mukhi->status = $request->has('status') ? 1 : 0;
        return $mukhi;
    }
}
