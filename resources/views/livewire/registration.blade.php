<section class="z-10 relative drop-shadow-2xl">
    <div class="px-2 py-12 mx-auto md:px-12 lg:px-32 max-w-7xl">

    @if($canRegister)
        <div class="max-w-lg mx-auto md:max-w-xl md:w-full">
            <div class="mt-8 rounded-3xl">
                <div class="flex flex-col text-center mb-4">
                    <h1 class="text-2xl font-semibold tracking-tight" style="color: #16458a">
                        Employee Registration
                    </h1>
                    <p class="text-sm font-medium text-gray-500">Provide your information to register.</p>
                </div>
                <div class="p-4 md:p-10 bg-white/90 backdrop-blur-md border border-white shadow-lg rounded-2xl">
                    <!-- Step 1 -->
                    @if ($step === 1)
                        <div>
                            <h2 class="text-lg font-medium text-gray-500">
                                Step 1 out of 3: <span class="font-bold text-black">Personal Information</span>
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="firstname" class=" text-sm text-gray-700"><i class="bi bi-person"></i> First Name <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" id="first_name" wire:model.live="first_name"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('first_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="middlename" class=" text-sm text-gray-700"><i class="bi bi-person"></i> Middle Name</label>
                                    <input type="text" id="middle_name" wire:model.live="middle_name"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="surname" class=" text-sm text-gray-700"><i class="bi bi-person"></i> Surname <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" id="surname" wire:model.live="surname"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('surname')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="name_extension" class=" text-sm text-gray-700"><i class="bi bi-person"></i> Name Extension</label>
                                    <select id="name_extension" wire:model.live="name_extension"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div x-data="{ showOthers: @entangle('sex').defer === 'Others' }" class="w-full">
                                    <label for="sex" class="text-sm text-gray-700"><i class="bi bi-gender-ambiguous"></i> Gender <span
                                            class="text-red-600">*</span></label>

                                    <select id="sex" wire:model.live="sex"
                                        @change="showOthers = (event.target.value === 'Others')"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="No">Prefer not to say</option>
                                        <option value="Others">Others</option>
                                    </select>

                                    <input x-show="showOthers" wire:model="otherSex" type="text" id="others"
                                        placeholder="Please specify"
                                        class="mt-2 w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">

                                    @error('sex')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="w-full">
                                    <label for="date_of_birth" class=" text-sm text-gray-700"><i class="bi bi-calendar-check"></i> Birth Date <span
                                            class="text-red-600">*</span></label>
                                    <input type="date" id="date_of_birth" wire:model.live="date_of_birth"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('date_of_birth')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @error('otherSex')
                                <span class="text-red-500 text-sm">This field is required</span>
                            @enderror

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="place_of_birth" class=" text-sm text-gray-700"><i class="bi bi-geo-alt"></i> Place of Birth <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" id="place_of_birth" wire:model.live="place_of_birth"
                                        class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('place_of_birth')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="blood_type" class=" text-sm text-gray-700"><i class="bi bi-heart"></i> Blood type</label>
                                    <input type="text" id="blood_type" wire:model.live="blood_type"
                                        class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('blood_type')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                                <!-- Citizenship Radio Buttons -->
                                <div class="w-full">
                                    <label class="text-sm text-gray-700"><i class="bi bi-person"></i> Citizenship <span
                                            class="text-red-600">*</span></label>
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="citizenship" value="Filipino"
                                                wire:model.live="citizenship"
                                                class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2">Filipino</span>
                                        </label>
                                        <label class="inline-flex items-center ml-6">
                                            <input type="radio" name="citizenship" value="Dual Citizenship"
                                                wire:model.live="citizenship"
                                                class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2">Dual Citizenship</span>
                                        </label>
                                    </div>
                                    @error('citizenship')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Dual Citizenship Additional Fields -->
                                @if ($citizenship === 'Dual Citizenship')
                                    <!-- Dual Citizenship Type Radio Buttons -->
                                    <div class="w-full mt-4">
                                        <label class="text-sm text-gray-700">Dual Citizenship Type <span
                                                class="text-red-600">*</span></label>
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="dual_citizenship_type" value="By Birth"
                                                    wire:model="dual_citizenship_type"
                                                    class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2">By Birth</span>
                                            </label>
                                            <label class="inline-flex items-center ml-6">
                                                <input type="radio" name="dual_citizenship_type"
                                                    value="By Naturalization" wire:model="dual_citizenship_type"
                                                    class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2">By Naturalization</span>
                                            </label>
                                        </div>
                                        @error('dual_citizenship_type')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Country Select Field -->
                                    <div class="w-full mt-4">
                                        <label class="text-sm text-gray-700">Country <span
                                                class="text-red-600">*</span></label>
                                        <select wire:model="dual_citizenship_country"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->name }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('dual_citizenship_country')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                                <div class="w-full">
                                    <label for="civil_status" class=" text-sm text-gray-700"><i class="bi bi-person"></i> Civil Status <span
                                            class="text-red-600">*</span></label>
                                    <select id="civil_status" wire:model.live="civil_status"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Select Civil Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                    @error('civil_status')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="height" class=" text-sm text-gray-700"><i class="bi bi-rulers" style="font-size: 10px;"></i> Height (m) <span
                                            class="text-red-600">*</span></label>
                                    <input type="number" id="height" wire:model.live="height"
                                        class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('height')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="weight" class=" text-sm text-gray-700"><i class="bi bi-rulers" style="font-size: 10px;"></i> Weight (kg) <span
                                            class="text-red-600">*</span></label>
                                    <input type="number" id="weight" wire:model.live="weight"
                                        class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('weight')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-12 columns-1">
                                <div class="w-full relative">
                                    <button
                                        class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                        wire:click="toStep2" wire:loading.attr="disabled" wire:target="toStep2">
                                        <span wire:loading.remove wire:target="toStep2">Next</span>
                                        <span wire:loading wire:target="toStep2">Loading...</span>
                                    </button>

                                    <p class="text-sm text-center text-gray-600 mt-4">Already have an account? <a href="/" class="text-blue-500 hover:text-blue-600">SIGN IN</a></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 2 -->
                    @if ($step === 2)
                        <div>
                            <h2 class="text-lg font-medium text-gray-500">
                                Step 2 out of 3: <span class="font-bold text-black">Government IDs</span>
                            </h2>

                            <div class="mt-6 flex flex-col gap-4 columns-1">
                                {{-- <div class="w-full" x-data="gsisFormat()">
                                    <label for="gsis" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> GSIS ID No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="gsis1" type="number" maxlength="4" x-model="part1"
                                            @input="if ($event.target.value.length == 4) $refs.part2.focus(); limitInput($event, 4)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="gsis2" type="number" maxlength="7" x-model="part2"
                                            @input="if ($event.target.value.length == 7) $refs.part3.focus(); limitInput($event, 7)"
                                            x-ref="part2"
                                            class="w-2/2 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="gsis3" type="number" maxlength="1" x-model="part3"
                                            x-ref="part3"
                                            @input="limitInput($event, 1)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                    
                                    @error('gsis')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                                <div class="w-full" x-data="umidFormat()">
                                    <label for="umid" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> UMID ID No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="umid1" type="number" maxlength="4" x-model="part1"
                                            @input="if ($event.target.value.length == 4) $refs.part2.focus(); limitInput($event, 4)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="umid2" type="number" maxlength="7" x-model="part2"
                                            @input="if ($event.target.value.length == 7) $refs.part3.focus(); limitInput($event, 7)"
                                            x-ref="part2"
                                            class="w-2/2 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="umid3" type="number" maxlength="1" x-model="part3"
                                            x-ref="part3"
                                            @input="limitInput($event, 1)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                    
                                    @error('umid')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full" x-data="pagibigFormat()">
                                    <label for="pagibig" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> PAGIBIG ID No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="pagibig1" type="number" maxlength="4" x-model="part1"
                                            @input="if ($event.target.value.length == 4) $refs.part2.focus(); limitInput($event, 4)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="pagibig2" type="number" maxlength="4" x-model="part2"
                                            @input="if ($event.target.value.length == 4) $refs.part3.focus(); limitInput($event, 4)"
                                            x-ref="part2"
                                            class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="pagibig3" type="number" maxlength="4" x-model="part3"
                                            x-ref="part3"
                                            @input="limitInput($event, 4)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                
                                    @error('pagibig')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-4 columns-1">
                                <div class="w-full" x-data="philhealthFormat()">
                                    <label for="philhealth" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> PhilHealth ID No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="philhealth1" type="number" maxlength="2" x-model="part1"
                                            @input="if ($event.target.value.length == 2) $refs.part2.focus(); limitInput($event, 2)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="philhealth2" type="number" maxlength="9" x-model="part2"
                                            @input="if ($event.target.value.length == 9) $refs.part3.focus(); limitInput($event, 9)"
                                            x-ref="part2"
                                            class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="philhealth3" type="number" maxlength="1" x-model="part3"
                                            x-ref="part3"
                                            @input="limitInput($event, 1)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                
                                    @error('philhealth')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- <div class="w-full" x-data="sssFormat()">
                                    <label for="sss" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> SSS No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="sss1" type="number" maxlength="2" x-model="part1"
                                            @input="if ($event.target.value.length == 2) $refs.part2.focus(); limitInput($event, 2)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="sss2" type="number" maxlength="7" x-model="part2"
                                            @input="if ($event.target.value.length == 7) $refs.part3.focus(); limitInput($event, 7)"
                                            x-ref="part2"
                                            class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="sss3" type="number" maxlength="1" x-model="part3"
                                            x-ref="part3"
                                            @input="limitInput($event, 1)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                
                                    @error('sss')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                                <div class="w-full">
                                    <label for="philsys" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> PhilSys No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="philsys" type="number" class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                
                                    @error('philsys')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-4 columns-1">
                                <div class="w-full" x-data="tinFormat()">
                                    <label for="tin" class="text-sm text-gray-700">
                                        <i class="bi bi-credit-card-2-front"></i> TIN No. <span class="opacity-90">(Optional)</span>
                                    </label>
                                    <div class="flex space-x-2">
                                        <input wire:model.live="tin1" type="number" maxlength="3" x-model="part1"
                                            @input="if ($event.target.value.length == 3) $refs.part2.focus(); limitInput($event, 3)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="tin2" type="number" maxlength="3" x-model="part2"
                                            @input="if ($event.target.value.length == 3) $refs.part3.focus(); limitInput($event, 3)"
                                            x-ref="part2"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="tin3" type="number" maxlength="3" x-model="part3"
                                            @input="if ($event.target.value.length == 3) $refs.part4.focus(); limitInput($event, 3)"
                                            x-ref="part3"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <span class="text-gray-600 self-center">-</span>
                                        <input wire:model.live="tin4" type="number" maxlength="3" x-model="part4"
                                            x-ref="part4"
                                            @input="limitInput($event, 3)"
                                            class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    </div>
                                
                                    @error('tin')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="agency_employee_no" class="text-sm text-gray-700"><i class="bi bi-credit-card-2-front"></i> Agency Employee
                                        No. <span class="opacity-90">(Optional)</span></label>
                                    <input type="text" id="agency_employee_no"
                                        wire:model.live="agency_employee_no"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('agency_employee_no')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="lg:flex gap-2 mt-12 columns-2">
                                <div class="w-full relative">
                                    <button
                                        class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                        wire:click="prevStep" wire:loading.attr="disabled" wire:target="prevStep">
                                        <span wire:loading.remove wire:target="prevStep">Previous</span>
                                        <span wire:loading wire:target="prevStep">Loading...</span>
                                    </button>
                                </div>
                                <div class="w-full relative sm:mt-0 mt-4">
                                    <button onclick='gsisFormat()' 
                                        class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                        wire:click="toStep3" wire:loading.attr="disabled" wire:target="toStep3">
                                        <span wire:loading.remove wire:target="toStep3">Next</span>
                                        <span wire:loading wire:target="toStep3">Loading...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 3 -->
                    @if ($step === 3)
                        <div>
                            <h2 class="text-lg font-medium text-gray-500">
                                Step 3 out of 3: <span class="font-bold text-black">Other Information</span>
                            </h2>

                            <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full mb-4 mt-6">
                                <legend class="text-black px-2"><i class="bi bi-pin-map"></i> Permanent Address</legend>
                                <div class="mt-2">
                                    <div class="w-full mt-2">
                                        <label for="permanent_province" class="block text-sm text-gray-700">Province
                                            <span class="text-red-600">*</span></label>
                                        <select
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            wire:model.live="permanent_selectedProvince" id="permanent_province"
                                            name="permanent_selectedProvince" required>
                                            @if ($pprovinces)
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="" style="opacity: .6;">Select Province</option>
                                                @foreach ($pprovinces->sortBy('province_description') as $province)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="{{ $province->province_description }}">
                                                        {{ $province->province_description }}</option>
                                                @endforeach
                                            @else
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="">Select
                                                    a region</option>
                                            @endif
                                        </select>
                                        @error('permanent_selectedProvince')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <label for="permanent_city" class="block text-sm text-gray-700">City <span
                                                class="text-red-600">*</span></label>
                                        <select
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            wire:model.live="permanent_selectedCity" id="permanent_city"
                                            name="permanent_selectedCity" required>
                                            @if ($pcities)
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="">Select
                                                    City</option>
                                                @foreach ($pcities as $city)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="{{ $city->city_municipality_description }}">
                                                        {{ $city->city_municipality_description }}</option>
                                                @endforeach
                                            @else
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="">Select
                                                    a province</option>
                                            @endif
                                        </select>
                                        @error('permanent_selectedCity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <label for="permanent_barangay" class="block text-sm text-gray-700">Barangay
                                            <span class="text-red-600">*</span></label>
                                        <select
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            wire:model.live="permanent_selectedBarangay" id="permanent_barangay"
                                            name="permanent_selectedBarangay" required>
                                            @if ($pbarangays)
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="">Select
                                                    Barangay</option>
                                                @foreach ($pbarangays as $barangay)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="{{ $barangay->barangay_description }}">
                                                        {{ $barangay->barangay_description }}</option>
                                                @endforeach
                                            @else
                                                <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                    value="">Select
                                                    a city</option>
                                            @endif
                                        </select>
                                        @error('permanent_selectedBarangay')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <label for="p_house" class="block text-sm text-gray-700">House/Block/Lot No.
                                            <span class="text-red-600">*</span></label>
                                        <input type="text" id="p_house" wire:model.live="p_house"
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            name="p_house" required>
                                        @error('p_house')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <label for="p_street" class="block text-sm text-gray-700">Street <span
                                                class="text-red-600">*</span></label>
                                        <input type="text" id="p_street" wire:model.live="p_street"
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            name="p_street" required>
                                        @error('p_street')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <label for="p_subdivision"
                                            class="block text-sm text-gray-700">Subdivision/Village <span
                                                class="text-red-600">*</span></label>
                                        <input type="text" id="p_subdivision" wire:model.live="p_subdivision"
                                            class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                            name="p_subdivision" required>
                                        @error('p_subdivision')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-full mt-2">
                                        <div class="w-full">
                                            <label for="permanent_selectedZipcode" class="text-sm text-gray-700">Zip
                                                Code
                                                <span class="text-red-600">*</span></label>
                                            <input type="number" id="permanent_selectedZipcode"
                                                wire:model.live="permanent_selectedZipcode"
                                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                            @error('permanent_selectedZipcode')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="mt-4 mb-4 gap-2 columns-1">
                                <input type="checkbox" wire:model.live="same_as_above">
                                <label class="text-sm text-gray-700">Same As Above (Residential Address)</label>
                            </div>

                            @if (!$same_as_above)
                                <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full mb-4">
                                    <legend class="text-black px-2"><i class="bi bi-pin-map"></i> Residential Address</legend>
                                    <div class="mt-2">
                                        <div class="w-full mt-2">
                                            <label for="residential_province"
                                                class="block text-sm text-gray-700">Province <span
                                                    class="text-red-600">*</span></label>
                                            <select
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                wire:model.live="residential_selectedProvince"
                                                id="residential_province" name="residential_selectedProvince"
                                                required>
                                                @if ($pprovinces)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="" style="opacity: .6;">Select Province</option>
                                                    @foreach ($pprovinces->sortBy('province_description') as $province)
                                                        <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                            value="{{ $province->province_description }}">
                                                            {{ $province->province_description }}</option>
                                                    @endforeach
                                                @else
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="">Select province</option>
                                                @endif
                                            </select>
                                            @error('residential_selectedProvince')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <label for="residential_city" class="block text-sm text-gray-700">City
                                                <span class="text-red-600">*</span></label>
                                            <select
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                wire:model.live="residential_selectedCity" id="residential_city"
                                                name="residential_selectedCity" required>
                                                @if ($rcities)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="">Select
                                                        City</option>
                                                    @foreach ($rcities as $city)
                                                        <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                            value="{{ $city->city_municipality_description }}">
                                                            {{ $city->city_municipality_description }}</option>
                                                    @endforeach
                                                @else
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="">Select
                                                        a province</option>
                                                @endif
                                            </select>
                                            @error('residential_selectedCity')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <label for="residential_barangay"
                                                class="block text-sm text-gray-700">Barangay <span
                                                    class="text-red-600">*</span></label>
                                            <select
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                wire:model.live="residential_selectedBarangay"
                                                id="residential_barangay" name="residential_selectedBarangay"
                                                required>
                                                @if ($rbarangays)
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="">Select
                                                        Barangay</option>
                                                    @foreach ($rbarangays as $barangay)
                                                        <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                            value="{{ $barangay->barangay_description }}">
                                                            {{ $barangay->barangay_description }}</option>
                                                    @endforeach
                                                @else
                                                    <option class="text-base text-gray-700 capitalize block mb-1.5"
                                                        value="">Select
                                                        a city</option>
                                                @endif
                                            </select>
                                            @error('residential_selectedBarangay')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <label for="r_house" class="block text-sm text-gray-700">House/Block/Lot
                                                No. <span class="text-red-600">*</span></label>
                                            <input type="text" id="r_house" wire:model.live="r_house"
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                name="r_house" required>
                                            @error('r_house')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <label for="r_street" class="block text-sm text-gray-700">Street <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" id="r_street" wire:model.live="r_street"
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                name="r_street" required>
                                            @error('r_street')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <label for="r_subdivision"
                                                class="block text-sm text-gray-700">Subdivision/Village <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" id="r_subdivision" wire:model.live="r_subdivision"
                                                class="block w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-white border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                                name="r_subdivision" required>
                                            @error('r_subdivision')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="w-full mt-2">
                                            <div class="w-full">
                                                <label for="residential_selectedZipcode"
                                                    class="text-sm text-gray-700">Zip Code
                                                    <span class="text-red-600">*</span></label>
                                                <input type="number" id="residential_selectedZipcode"
                                                    wire:model.live="residential_selectedZipcode"
                                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                                @error('residential_selectedZipcode')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            @endif

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="tel_number" class="text-sm text-gray-700"><i class="bi bi-telephone"></i> Telephone No.</label>
                                    <input type="text" id="tel_number" wire:model.live="tel_number"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                </div>
                                <div class="w-full">
                                    <label for="mobile_number" class="text-sm text-gray-700"><i class="bi bi-phone"></i> Mobile No. <span
                                            class="text-red-600">*</span></label>
                                    <input type="number" id="mobile_number" wire:model.live="mobile_number"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('mobile_number')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div x-data="{ appointment: @entangle('appointment') }" class="mt-4 gap-2 columns-1">
                                <div class="w-full">
                                    <label for="office_division" class="text-sm text-gray-700">Nature of Appointment
                                        <span class="text-red-600">*</span></label>
                                    <select id="office_division" x-model="appointment"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Please Choose one</option>
                                        <option value="plantilla">Plantilla</option>
                                        <option value="cos">Contract of Service (COS)</option>
                                        <option value="ojt">OJT</option>
                                    </select>

                                </div>

                                <!-- Plantilla Item Number Field -->
                                {{-- <div class="w-full mt-4">
                                    <label for="itemNumber" class="text-sm text-gray-700">Item Number</label>
                                    <input id="itemNumber" wire:model="itemNumber"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                        type="text" placeholder="Enter Item Number">
                                    @error('itemNumber')
                                        <span class="text-red-500 text-sm">This field is required</span>
                                    @enderror
                                </div> --}}
                                <!-- Data of Assumption Field -->
                                {{-- <div x-show="appointment === 'pa'" class="w-full mt-4">
                                    <label for="data_of_assumption" class="text-sm text-gray-700">Data of Assumption <span class="text-red-600">*</span></label>
                                    <input id="data_of_assumption" wire:model="data_of_assumption"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"
                                        type="text" placeholder="Enter Data of Assumption">
                                    @error('data_of_assumption')
                                        <span class="text-red-500 text-sm">This field is required</span>
                                    @enderror
                                </div> --}}
                            </div>
                            @error('appointment')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            {{-- division unit position --}}
                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">

                                <div class="w-full">
                                    <label for="office_division" class="text-sm text-gray-700"><i class="bi bi-building"></i> Work Group <span
                                            class="text-red-600">*</span></label>
                                    <select id="office_division" wire:model.live="selectedOfficeDivision"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="" selected>Select a work group</option>
                                        @foreach ($officeDivisions as $officeDivision)
                                            <option value="{{ $officeDivision->id }}">
                                                {{ $officeDivision->office_division }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('selectedOfficeDivision')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <div class="w-full">
                                    <label for="unit" class="text-sm text-gray-700"><i class="bi bi-people"></i> Unit</label>
                                    <select id="unit" wire:model.live="selectedUnit"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="" selected>Select a unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('selectedUnit')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="mt-4 gap-2 columns-1">
                                <div class="w-full">
                                    <label for="position" class="text-sm text-gray-700"><i class="bi bi-person-gear"></i> Position <span
                                            class="text-red-600">*</span></label>
                                    <select id="position" wire:model.live="selectedPosition"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="" selected>Select a position</option>
                                        @foreach ($positions as $position)
                                            @if ($position->position !== 'Super Admin')
                                                <option value="{{ $position->id }}">{{ $position->position }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                </div>

                            </div>
                            @error('selectedPosition')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="date_hired" class="text-sm text-gray-700"><i class="bi bi-calendar-check"></i> Date of assumption <span
                                            class="text-red-600">*</span></label>
                                    <input type="date" id="date_hired" wire:model.live="date_hired"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('date_hired')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div x-data="{ appointment: @entangle('appointment'), empCode: @entangle('emp_code') }" class="w-full">
                                    <label for="emp_code" class="text-sm text-gray-700"><i class="bi bi-credit-card-2-front"></i> Employee Code<span
                                            class="text-red-600">*</span></label>
                                    <input type="text" id="emp_code" wire:model.live="emp_code"
                                        inputmode="numeric" pattern="[0-9]*"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('emp_code')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                                <div class="w-full">
                                    <label for="email" class="text-sm text-gray-700"><i class="bi bi-envelope-at"></i> Email Address<span
                                            class="text-red-600">*</span></label>
                                    <input type="text" id="email" wire:model.live="email"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('email')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="relative w-full mt-4" x-data="{ show: false }">
                                    <div class="relative inline-block" x-data="{ tooltip: false }">
                                        <!-- Info Icon -->
                                        <i class="bi bi-info-circle-fill text-blue-700 cursor-pointer"
                                            @mouseenter="tooltip = true" @mouseleave="tooltip = false"></i>
                                        <div x-show="tooltip"
                                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 z-10 w-auto px-4 py-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 transition-opacity duration-300"
                                            style="display: none;">
                                            <ul class="list-none space-y-2 whitespace-nowrap">
                                                <li>- At least 8 characters</li>
                                                <li>- One uppercase letter</li>
                                                <li>- One number</li>
                                                <li>- One special character</li>
                                            </ul>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <label for="password" class="text-sm text-gray-700"><i class="bi bi-lock"></i> Password <span
                                            class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'" id="password"
                                            wire:model.live="password"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm pr-12">
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3">
                                            <i :class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" @click="show = !show"
                                                class="cursor-pointer text-gray-500"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="relative w-full mt-4" x-data="{ show: false }">
                                    <div class="relative inline-block" x-data="{ tooltip: false }">
                                        <!-- Info Icon -->
                                        <i class="bi bi-info-circle-fill text-blue-700 cursor-pointer"
                                            @mouseenter="tooltip = true" @mouseleave="tooltip = false"></i>
                                        <div x-show="tooltip"
                                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 z-10 w-auto px-4 py-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 transition-opacity duration-300"
                                            style="display: none;">
                                            <ul class="list-none space-y-2 whitespace-nowrap">
                                                <li>- At least 8 characters</li>
                                                <li>- One uppercase letter</li>
                                                <li>- One number</li>
                                                <li>- One special character</li>
                                            </ul>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <label for="c_password" class="text-sm text-gray-700"><i class="bi bi-lock"></i> Confirm Password <span
                                            class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'" id="c_password"
                                            wire:model.live="c_password"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm pr-12">
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3">
                                            <i :class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" @click="show = !show"
                                                class="cursor-pointer text-gray-500"></i>
                                        </div>
                                    </div>
                                    @error('c_password')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Data Privacy Consent -->
                            <div class="relative w-full mt-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" 
                                            id="consented" 
                                            wire:model.live="consented"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <label for="consented" class="text-sm text-gray-700">
                                            I consent to the collection and processing of my personal data in accordance with the 
                                            <a href="/data-privacy-policy" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                                Data Privacy Policy
                                            </a>
                                            <span class="text-red-600">*</span>
                                        </label>
                                        @error('consented')
                                            <span class="text-red-500 text-sm block mt-1">
                                                You must agree to the Data Privacy Consent to continue.
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="lg:flex gap-2 mt-12 columns-2">
                                <div class="w-full relative">
                                    <button
                                        class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                        wire:click="prevStep" wire:loading.attr="disabled" wire:target="prevStep">
                                        <span wire:loading.remove wire:target="prevStep">Previous</span>
                                        <span wire:loading wire:target="prevStep">Loading...</span>
                                    </button>
                                </div>
                                <div class="w-full relative sm:mt-0 mt-4">
                                    <button
                                        class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                        wire:click="submit" wire:loading.attr="disabled" wire:target="submit">
                                        <span wire:loading.remove wire:target="submit">Submit</span>
                                        <span wire:loading wire:target="submit">Submitting...</span>
                                    </button>
                                    @error('submit')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    
    @endif
    
    @if(!$canRegister)
        <div class="max-w-lg mx-auto md:max-w-xl md:w-full">
            <div class="flex flex-col text-center">
                <h1 class="text-2xl font-semibold tracking-tigh" style="color: #16458a">
                    Verify Your Registration
                </h1>
                <p class="text-sm font-medium text-gray-500">Provide your email and OTP to proceed.</p>
            </div>
            <div class="mt-8">
                <div class="p-4 md:p-10 bg-white/90 backdrop-blur-md border border-white shadow-lg rounded-2xl">
                    <div>
                        <h2 class="text-lg font-medium text-gray-500">
                            <span class="font-bold text-black">Verify Registration</span>
                        </h2>

                        <div class="mt-4 columns-1">
                            <div class="w-full">
                                <label for="email" class=" text-sm text-gray-700">Email <span
                                        class="text-red-600">*</span></label>
                                <input type="text" id="email" wire:model.live="verificationEmail"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                @error('verificationEmail')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                            
                        <div class="mt-4 columns-1">
                            <div class="w-full">
                                <label for="otp" class=" text-sm text-gray-700">One-Time Password (OTP) 
                                    <span class="text-red-600">*</span></label>

                                <div x-data="{ otp: ['', '', '', '', '', ''] }" class="flex justify-between gap-2">
                                    @foreach(range(0, 5) as $index)
                                        <input 
                                            wire:model.live="otp.{{ $index }}" 
                                            type="text" 
                                            maxlength="1" 
                                            x-model="otp[{{ $index }}]" 
                                            @input="if ($event.target.value.length === 1) { 
                                                if ($refs[`otp{{ $index + 1 }}`]) { 
                                                    $refs[`otp{{ $index + 1 }}`].focus(); 
                                                } 
                                            }" 
                                            x-ref="otp{{ $index }}" 
                                            class="w-full h-10 text-center text-lg font-semibold text-black border rounded-lg bg-chalk border-zinc-300 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 sm:text-sm">
                                    @endforeach
                                </div>
                                @error('otp')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if($verificationError)
                            <div class="mt-4 columns-1">
                                <span class="text-red-500 text-sm text-center">{{ $verificationError }}</span>
                            </div>
                        @endif

                        <div class="mt-4 columns-1">
                            <div class="w-full relative">
                                <button
                                    class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                    wire:click="verify" wire:loading.attr="disabled" wire:target="verify">
                                    <span wire:loading.remove wire:target="verify">Verify</span>
                                    <span wire:loading wire:target="verify">Loading...</span>
                                </button>

                                <p class="text-sm text-center text-gray-600 mt-4">Already have an account? <a href="/" class="text-blue-500 hover:text-blue-600">SIGN IN</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    </div>
</section>


<script>
    function limitInput(event, maxLength) {
        if (event.target.value.length > maxLength) {
            event.target.value = event.target.value.slice(0, maxLength);
            this[event.target.getAttribute('x-model')] = event.target.value;
        }
    }
</script>