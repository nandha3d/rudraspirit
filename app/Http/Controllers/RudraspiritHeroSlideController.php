<?php

namespace App\Http\Controllers;

use App\Models\RudraspiritHeroSlide;
use Illuminate\Http\Request;

class RudraspiritHeroSlideController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:select_homepage']);
    }

    public function index()
    {
        $slides = RudraspiritHeroSlide::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('backend.website_settings.hero_slides.index', compact('slides'));
    }

    public function create()
    {
        return view('backend.website_settings.hero_slides.create');
    }

    public function store(Request $request)
    {
        $slide = new RudraspiritHeroSlide();
        $this->fill($slide, $request)->save();
        flash(translate('Hero slide created successfully'))->success();
        return redirect()->route('hero-slides.index');
    }

    public function edit($id)
    {
        $slide = RudraspiritHeroSlide::findOrFail($id);
        return view('backend.website_settings.hero_slides.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $slide = RudraspiritHeroSlide::findOrFail($id);
        $this->fill($slide, $request)->save();
        flash(translate('Hero slide updated successfully'))->success();
        return redirect()->route('hero-slides.index');
    }

    public function destroy($id)
    {
        RudraspiritHeroSlide::findOrFail($id)->delete();
        flash(translate('Hero slide deleted successfully'))->success();
        return redirect()->route('hero-slides.index');
    }

    private function fill(RudraspiritHeroSlide $slide, Request $request): RudraspiritHeroSlide
    {
        $slide->image = $request->image;
        $slide->kicker = $request->kicker;
        $slide->title = $request->title;
        $slide->title_em = $request->title_em;
        $slide->text = $request->text;
        $slide->cta_text = $request->cta_text;
        $slide->cta_link = $request->cta_link;
        $slide->sort_order = (int) $request->sort_order;
        $slide->status = $request->has('status') ? 1 : 0;
        return $slide;
    }
}
