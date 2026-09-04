<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\ContactInquiryRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(ContactInquiryRequest $request): RedirectResponse
    {
        Lead::create(array_merge($request->validated(), ['status' => LeadStatus::New]));

        return redirect()->route('contact')
            ->with('success', 'Thank you for contacting us. A member of our team will respond soon.');
    }
}
