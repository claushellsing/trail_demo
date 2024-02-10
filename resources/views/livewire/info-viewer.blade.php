<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    public $info;

    public function returnForm()
    {
        return redirect()->route('form');
    }

    public function mount($info)
    {
        $this->info = $info;
    }
}; ?>

<div class="bg-white p-6 rounded-md shadow-md">
    <h1 class="text-2xl font-bold mb-4">Info Viewer</h1>
    <div>
        <div class="mb-1">
            <label class="font-bold">First Name:</label>
            <span>{{ $info->first_name }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">Last Name:</label>
            <span>{{ $info->last_name }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">Address:</label>
            <span>{{ $info->address }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">Country:</label>
            <span>{{ $info->country }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">City:</label>
            <span>{{ $info->city }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">Day of Birth</label>
            <span>{{ $info->date_of_birth->format("j M, Y") }}</span>
        </div>
        <div class="mb-1">
            <label class="font-bold">Is married ?:</label>
            <span>{{ $info->is_married? 'Yes':'No' }}</span>
        </div>
        @if($info->is_married)
            <div class="mb-1">
                <label class="font-bold">Date of Marriage:</label>
                <span>{{ $info->date_of_marriage->format("j M, Y") }}</span>
            </div>
            <div class="mb-1">
                <label class="font-bold">Country of Marriage:</label>
                <span>{{ $info->country_of_marriage }}</span>
            </div>
        @else
            <div class="mb-1">
                <label class="font-bold">Is Widower?:</label>
                <span>{{ $info->is_widowed? 'Yes':'No' }}</span>
            </div>
            <div class="mb-1">
                <label class="font-bold">Has been married?:</label>
                <span>{{ $info->has_been_married? 'Yes':'No' }}</span>
            </div>
        @endif
    </div>
    <div class="w-full text-center mt-4">
        <button
            wire:click="returnForm"
            class="bg-sky-400 w-md h-md p-2 rounded-full text-white hover:bg-sky-600 transition cursor-pointer shadow-md"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </button>
    </div>
</div>
