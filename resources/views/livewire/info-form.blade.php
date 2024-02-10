<?php

use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    //
    public $months = [];
    public $days = [];
    public $years = [];

    public function saveUser(
        $firstName,
        $lastName,
        $address,
        $country,
        $city,
        $birthDate,
        $isMarried,
        $marriageDate,
        $marriageCountry,
        $isWidow,
        $hasBeenMarried
    )
    {
        $ageOfMarriage = 0;
        if ($marriageDate && $birthDate) {
            $ageOfMarriage = Carbon::parse($birthDate)->diffInYears($marriageDate);
        }

        $validated = \Validator::make([
            'first_name'          => $firstName,
            'last_name'           => $lastName,
            'address'             => $address,
            'country'             => $country,
            'city'                => $city,
            'date_of_birth'       => $birthDate,
            'is_married'          => $isMarried,
            'date_of_marriage'    => $marriageDate,
            'country_of_marriage' => $marriageCountry,
            'is_widowed'          => $isWidow,
            'has_been_married'    => $hasBeenMarried,
            'age_of_marriage'     => $ageOfMarriage,
        ], [
            'first_name'          => 'required|min:3',
            'last_name'           => 'required|min:3',
            'address'             => 'required|min:3',
            'country'             => 'required|min:3',
            'city'                => 'required|min:3',
            'date_of_birth'       => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'is_married'          => 'required|boolean',
            'date_of_marriage'    => 'required_if:is_married,true',
            'country_of_marriage' => 'required_if:is_married,true',
            'age_of_marriage'     => 'required_if:is_married,true|gte:18',
            'is_widowed'          => 'required_if:is_married,false',
            'has_been_married'    => 'required_if:is_married,false',
        ])->validate();

        $userInfo = \App\Models\UserInfo::create($validated);
        return redirect()->route('info', ['info' => $userInfo->id]);
    }

    public function mount()
    {
        $this->months = [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        $this->days = range(1, 31);
        $this->years = range(1980, 2021);
    }
}; ?>
<div
    class="bg-white p-6 rounded-md shadow-md"
    x-data="{
        page: 1,
        firstName: '',
        lastName: '',
        address: '',
        country: '', //selector
        city: '', //selector
        birthDate: {
            day: '',
            month: '',
            year: ''
        },
        isMarried: true,
        displayErrorAge: false,
        displayErrorMarriageAge: false,
        marriageDate: {
            day: '',
            month: '',
            year: ''
        },
        marriageCountry: '',
        isWidow: null,
        hasBeenMarried: null,
        submitting: false,
        movePage: function movePage(page) {
            if (page===2) {
                if (this.currentAge === null || this.currentAge < 18) {
                   this.displayErrorAge = true;
                } else {
                   this.page = 2
                }
            }
            else if (page===1) {
                this.page = 1
            }
        },
        differenceInYears: function differenceInYears(dateA, dateB) {
            const date1 = new Date(dateA);
            const date2 = new Date(dateB);

            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return Math.floor(diffDays / 365);
        },
        get currentAge() {
            if (this.birthDateFormatted) {
                const currentDateFormatted = new Date().toISOString().split('T')[0];
                return this.differenceInYears(this.birthDateFormatted, currentDateFormatted);
            }
            return null;
        },
        get ageOfMarriage() {
            if (this.marriageDateFormatted) {
                return this.differenceInYears(this.birthDateFormatted, this.marriageDateFormatted);
            }
            return null;
        },
        get birthDateFormatted() {
            if (this.birthDate.day && this.birthDate.month && this.birthDate.year) {
                return `${this.birthDate.year}-${this.birthDate.month}-${this.birthDate.day}`;
            }
            return null;
        },
        get marriageDateFormatted() {
            if (this.marriageDate.day && this.marriageDate.month && this.marriageDate.year) {
                return `${this.marriageDate.year}-${this.marriageDate.month}-${this.marriageDate.day}`;
            }
            return null;
        },
        submit: function submit() {
            if (this.isMarried) {
                if (!this.marriageDateFormatted || !this.birthDateFormatted || this.ageOfMarriage < 18) {
                    this.displayErrorMarriageAge = true;
                    return;
                }
            }

            this.submitting = true;
                this.$wire.saveUser(
                    this.firstName,
                    this.lastName,
                    this.address,
                    this.country,
                    this.city,
                    this.birthDateFormatted,
                    this.isMarried,
                    this.marriageDateFormatted,
                    this.marriageCountry,
                    this.isWidow,
                    this.hasBeenMarried
                )
                .then((response) => {})
                .catch((error) => {})
                .finally(() => {
                    this.submitting = false;
                });
        }
    }"

    x-init="
        $watch('currentAge', (value) => {
            if (value !== null) {
                if (value < 18) {
                    displayErrorAge = true;
                } else {
                    displayErrorAge = false;
                }
            }
        });

        $watch('ageOfMarriage', (value) => {
            if (value !== null) {
                if (value < 18) {
                    displayErrorMarriageAge = true;
                } else {
                    displayErrorMarriageAge = false;
                }
            }
        });
    "
>
    <div x-show="page === 1">
        <div>
            <h1 class="text-2xl font-bold mb-6">Personal Information</h1>
            <div class="flex flex-col mb-6">
                <label class="mb-1">First Name</label>
                <input
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    type="text"
                    x-model="firstName"
                    placeholder="Your Name"
                />
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Last Name</label>
                <input
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    type="text"
                    x-model="lastName"
                    placeholder="Your Last Name"
                />
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Address</label>
                <textarea
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    placeholder="Your Address"
                    x-model="address"
                >
                </textarea>
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Country</label>
                <select
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    x-model="country"
                >
                    <option value="">Select Country</option>
                    <option value="USA">USA</option>
                    <option value="Canada">Canada</option>
                </select>
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">City</label>
                <select
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    x-model="city"
                >
                    <option value="">Select City</option>
                    <option value="New York">New York</option>
                    <option value="los angeles">Los Angeles</option>
                </select>
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Birth Date</label>
                <div>
                    <select
                        x-model="birthDate.month"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Month</option>
                        @foreach($months as $key => $month)
                            <option value="{{ $key }}">{{ $month }}</option>
                        @endforeach
                    </select>
                    <select
                        x-model="birthDate.day"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Day</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                    <select
                        x-model="birthDate.year"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Year</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div x-show="displayErrorAge" class="flex flex-col mb-6">
                <div class="bg-rose-200 p-4 rounded-md relative text-center">
                    <span class="text-red-500">You must be 18 years old to continue</span>
                    <div
                        class="absolute top-0 right-0 cursor-pointer text-red-500"
                        @click="displayErrorAge = false"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                <button
                    class="flex border-1 bg-sky-500 text-white p-2 rounded-md shadow-md hover:bg-sky-700 cursor-pointer hover:shadow-lg transition duration-300"
                    @click="movePage(2)"

                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span>
                        Next
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div x-show="page === 2">
        <h1 class="text-2xl font-bold mb-6">Marital Information</h1>
        <div class="flex flex-col mb-6">
            <label class="mb-1">Are you married ?</label>
            <div class="inline-flex">
                <div
                    @click="isMarried = true"
                    class="border-b-2 px-2 py-2 mr-4 cursor-pointer"
                    x-bind:class="{ 'border-blue-500 font-bold': isMarried===true }"
                >
                    <span>Yes</span>
                </div>
                <div
                    @click="isMarried = false"
                    class="border-b-2 px-2 py-2 cursor-pointer"
                    x-bind:class="{ 'border-blue-500 font-bold': isMarried===false }"
                >
                    <span>No</span>
                </div>
            </div>
        </div>
        <div x-show="isMarried === true">
            <div class="flex flex-col mb-6">
                <label class="mb-1">Marriage Date</label>
                <div>
                    <select
                        x-model="marriageDate.month"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Month</option>
                        @foreach($months as $key => $month)
                            <option value="{{ $key }}">{{ $month }}</option>
                        @endforeach
                    </select>
                    <select
                        x-model="marriageDate.day"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Day</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                    <select
                        x-model="marriageDate.year"
                        class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    >
                        <option value="">Year</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Country of Marriage</label>
                <select
                    class="bg-transparent outline-none border-b-2 p-2 pl-0"
                    x-model="marriageCountry"
                >
                    <option value="">Select Country</option>
                    <option value="USA">USA</option>
                    <option value="Canada">Canada</option>
                </select>
            </div>
        </div>
        <div x-show="isMarried === false">
            <div class="flex flex-col mb-6">
                <label class="mb-1">Are you widowed?</label>
                <div class="inline-flex">
                    <div
                        @click="isWidow = true"
                        class="border-b-2 px-2 py-1 mr-4 cursor-pointer"
                        x-bind:class="{ 'border-blue-500 font-bold': isWidow === true }"
                    >
                        <span>Yes</span>
                    </div>
                    <div
                        @click="isWidow = false"
                        class="border-b-2 px-2 py-1 cursor-pointer"
                        x-bind:class="{ 'border-blue-500 font-bold': isWidow === false }"
                    >
                        <span>No</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col mb-6">
                <label class="mb-1">Have you been married before?</label>
                <div class="inline-flex">
                    <div
                        @click="hasBeenMarried = true"
                        class="border-b-2 px-2 py-1 mr-4 cursor-pointer"
                        x-bind:class="{ 'border-blue-500 font-bold': hasBeenMarried === true }"
                    >
                        <span>Yes</span>
                    </div>
                    <div
                        @click="hasBeenMarried = false"
                        class="border-b-2 px-2 py-1 cursor-pointer"
                        x-bind:class="{ 'border-blue-500 font-bold': hasBeenMarried === false }"
                    >
                        <span>No</span>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="displayErrorMarriageAge" class="flex flex-col mb-6">
            <div class="bg-rose-200 p-4 rounded-md relative text-center">
                <span class="text-red-500">You are not eligible to apply because your marriage occurred before your 18th birthday.</span>
                <div
                    class="absolute top-0 right-0 cursor-pointer text-red-500"
                    @click="displayErrorMarriageAge = false"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div
                x-ref="errors"
                class="bg-rose-200 p-4 rounded-md relative mb-4 pl-8"
            >
                <ul class="list-disc">
                    @foreach ($errors->all() as $error)
                        <li class="mb-1 text-red-500">{{ $error }}</li>
                    @endforeach
                </ul>
                <div
                    class="absolute top-0 right-0 cursor-pointer text-red-500"
                    @click="$refs.errors.style.display = 'none'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        @endif
        <div class="flex">
            <button
                class="flex border-1 bg-sky-500 text-white p-2 mr-4 rounded-md shadow-md hover:bg-sky-700 cursor-pointer hover:shadow-lg transition duration-300"
                @click="movePage(1)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                </svg>
                <span>Previous</span>
            </button>
            <button
                class="flex border-1 bg-emerald-500 text-white p-2 rounded-md shadow-md hover:bg-emerald-700 cursor-pointer hover:shadow-lg transition duration-300"
                @click="submit()"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
                <span>
                    Submit
                </span>
            </button>
        </div>
    </div>
</div>
