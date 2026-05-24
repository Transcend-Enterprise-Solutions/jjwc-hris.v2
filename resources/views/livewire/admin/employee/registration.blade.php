<div class="w-full">
    <div class="mt-8 rounded-3xl">
        <div class="flex flex-col text-center mb-4">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-800 dark:text-white  ">
                Employee Registration
            </h1>
            <p class="text-sm font-medium">Provide employee information to register.</p>
        </div>
        <div class="p-4 md:p-10 bg-white dark:bg-slate-800 rounded-2xl">

            <!-- Step 1 -->
            @if ($step === 1)
                <div>
                    <h2 class="text-md font-medium text-gary-600 dark:text-gray-300">
                        Step 1 out of 3: <span class="font-bold text-gary-700 dark:text-gray-200">Personal Information</span>
                    </h2>

                    <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                        <div class="w-full">
                            <label for="firstname" class=" text-sm"><i class="bi bi-person"></i> First Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="first_name" wire:model.live="first_name"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('first_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="middlename" class=" text-sm"><i class="bi bi-person"></i> Middle Name</label>
                            <input type="text" id="middle_name" wire:model.live="middle_name"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                        </div>
                    </div>

                    <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                        <div class="w-full">
                            <label for="surname" class=" text-sm"><i class="bi bi-person"></i> Surname <span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="surname" wire:model.live="surname"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('surname')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="name_extension" class=" text-sm"><i class="bi bi-person"></i> Name Extension</label>
                            <select id="name_extension" wire:model.live="name_extension"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                            <label for="sex" class="text-sm"><i class="bi bi-gender-ambiguous"></i> Gender <span
                                    class="text-red-600">*</span></label>

                            <select id="sex" wire:model.live="sex"
                                @change="showOthers = (event.target.value === 'Others')"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="No">Prefer not to say</option>
                                <option value="Others">Others</option>
                            </select>

                            <input x-show="showOthers" wire:model="otherSex" type="text" id="others"
                                placeholder="Please specify"
                                class="mt-2 w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">

                            @error('sex')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>



                        <div class="w-full">
                            <label for="date_of_birth" class=" text-sm"><i class="bi bi-calendar-check"></i> Birth Date <span
                                    class="text-red-600">*</span></label>
                            <input type="date" id="date_of_birth" wire:model.live="date_of_birth"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                            <label for="place_of_birth" class=" text-sm"><i class="bi bi-geo-alt"></i> Place of Birth <span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="place_of_birth" wire:model.live="place_of_birth"
                                class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('place_of_birth')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="blood_type" class=" text-sm"><i class="bi bi-heart"></i> Blood type <span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="blood_type" wire:model.live="blood_type"
                                class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('blood_type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                        <!-- Citizenship Radio Buttons -->
                        <div class="w-full">
                            <label class="text-sm"><i class="bi bi-person"></i> Citizenship <span
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
                                <label class="text-sm">Dual Citizenship Type <span
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
                                <label class="text-sm">Country <span
                                        class="text-red-600">*</span></label>
                                <select wire:model="dual_citizenship_country"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                            <label for="civil_status" class=" text-sm"><i class="bi bi-person"></i> Civil Status <span
                                    class="text-red-600">*</span></label>
                            <select id="civil_status" wire:model.live="civil_status"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                            <label for="height" class=" text-sm"><i class="bi bi-rulers" style="font-size: 10px;"></i> Height (m) <span
                                    class="text-red-600">*</span></label>
                            <input type="number" id="height" wire:model.live="height"
                                class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('height')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="weight" class=" text-sm"><i class="bi bi-rulers" style="font-size: 10px;"></i> Weight (kg) <span
                                    class="text-red-600">*</span></label>
                            <input type="number" id="weight" wire:model.live="weight"
                                class=" w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2 -->
            @if ($step === 2)
                <div>
                    <h2 class="text-md font-medium text-gary-600 dark:text-gray-300">
                        Step 2 out of 3: <span class="font-bold text-gary-700 dark:text-gray-200">Government IDs</span>
                    </h2>

                    <div class="mt-6 flex flex-col gap-4 columns-1">
                        <div class="w-full" x-data="umidFormat()">
                            <label for="umid" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> UMID ID No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="umid1" type="number" maxlength="4" x-model="part1"
                                    @input="if ($event.target.value.length == 4) $refs.part2.focus(); limitInput($event, 4)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="umid2" type="number" maxlength="7" x-model="part2"
                                    @input="if ($event.target.value.length == 7) $refs.part3.focus(); limitInput($event, 7)"
                                    x-ref="part2"
                                    class="w-2/2 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="umid3" type="number" maxlength="1" x-model="part3"
                                    x-ref="part3"
                                    @input="limitInput($event, 1)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                            
                            @error('umid')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full" x-data="pagibigFormat()">
                            <label for="pagibig" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> PAGIBIG ID No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="pagibig1" type="number" maxlength="4" x-model="part1"
                                    @input="if ($event.target.value.length == 4) $refs.part2.focus(); limitInput($event, 4)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="pagibig2" type="number" maxlength="4" x-model="part2"
                                    @input="if ($event.target.value.length == 4) $refs.part3.focus(); limitInput($event, 4)"
                                    x-ref="part2"
                                    class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="pagibig3" type="number" maxlength="4" x-model="part3"
                                    x-ref="part3"
                                    @input="limitInput($event, 4)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                        
                            @error('pagibig')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-4 columns-1">
                        <div class="w-full" x-data="philhealthFormat()">
                            <label for="philhealth" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> PhilHealth ID No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="philhealth1" type="number" maxlength="2" x-model="part1"
                                    @input="if ($event.target.value.length == 2) $refs.part2.focus(); limitInput($event, 2)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="philhealth2" type="number" maxlength="9" x-model="part2"
                                    @input="if ($event.target.value.length == 9) $refs.part3.focus(); limitInput($event, 9)"
                                    x-ref="part2"
                                    class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="philhealth3" type="number" maxlength="1" x-model="part3"
                                    x-ref="part3"
                                    @input="limitInput($event, 1)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                        
                            @error('philhealth')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- <div class="w-full" x-data="sssFormat()">
                            <label for="sss" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> SSS No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="sss1" type="number" maxlength="2" x-model="part1"
                                    @input="if ($event.target.value.length == 2) $refs.part2.focus(); limitInput($event, 2)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="sss2" type="number" maxlength="7" x-model="part2"
                                    @input="if ($event.target.value.length == 7) $refs.part3.focus(); limitInput($event, 7)"
                                    x-ref="part2"
                                    class="w-2/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="sss3" type="number" maxlength="1" x-model="part3"
                                    x-ref="part3"
                                    @input="limitInput($event, 1)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                        
                            @error('sss')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div> --}}
                        <div class="w-full">
                            <label for="philsys" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> PhilSys No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="philsys" type="number" class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                        
                            @error('philsys')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-4 columns-1">
                        <div class="w-full" x-data="tinFormat()">
                            <label for="tin" class="text-sm">
                                <i class="bi bi-credit-card-2-front"></i> TIN No. <span class="opacity-90">(Optional)</span>
                            </label>
                            <div class="flex space-x-2">
                                <input wire:model.live="tin1" type="number" maxlength="3" x-model="part1"
                                    @input="if ($event.target.value.length == 3) $refs.part2.focus(); limitInput($event, 3)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="tin2" type="number" maxlength="3" x-model="part2"
                                    @input="if ($event.target.value.length == 3) $refs.part3.focus(); limitInput($event, 3)"
                                    x-ref="part2"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="tin3" type="number" maxlength="3" x-model="part3"
                                    @input="if ($event.target.value.length == 3) $refs.part4.focus(); limitInput($event, 3)"
                                    x-ref="part3"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <span class="text-gray-600 self-center">-</span>
                                <input wire:model.live="tin4" type="number" maxlength="3" x-model="part4"
                                    x-ref="part4"
                                    @input="limitInput($event, 3)"
                                    class="w-1/4 h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            </div>
                        
                            @error('tin')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="agency_employee_no" class="text-sm"><i class="bi bi-credit-card-2-front"></i> Agency Employee
                                No. <span class="opacity-90">(Optional)</span></label>
                            <input type="text" id="agency_employee_no"
                                wire:model.live="agency_employee_no"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
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
                            <button onclick='umidFormat()' 
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
                    <h2 class="text-md font-medium text-gary-600 dark:text-gray-300">
                        Step 3 out of 3: <span class="font-bold text-gary-700 dark:text-gray-200">Other Information</span>
                    </h2>

                    <fieldset class="border border-gray-300 dark:border-gray-700 p-4 rounded-lg overflow-hidden w-full mb-4 mt-6">
                        <legend class="text-gray-700 dark:text-gray-200 px-2"><i class="bi bi-pin-map"></i> Permanent Address</legend>
                        <div class="mt-2">
                            <!-- Region Dropdown -->
                            <div class="w-full mt-2">
                                <label for="permanent_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Region <span class="text-red-600">*</span>
                                </label>
                                <select wire:model.live="permanent_selectedRegion" 
                                        id="permanent_region"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    <option value="">Select Region</option>
                                    @if($regions)
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->region_description }}">
                                                {{ $region->region_description }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('permanent_selectedRegion')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Province Dropdown -->
                            <div class="w-full mt-2">
                                <label for="permanent_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Province <span class="text-red-600">*</span>
                                </label>
                                <select wire:model.live="permanent_selectedProvince" 
                                        id="permanent_province"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @if ($pprovinces && $pprovinces->count() > 0)
                                        <option value="">Select Province</option>
                                        @foreach ($pprovinces->sortBy('province_description') as $province)
                                            <option value="{{ $province->province_description }}">
                                                {{ $province->province_description }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Select a region first</option>
                                    @endif
                                </select>
                                @error('permanent_selectedProvince')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- City Dropdown -->
                            <div class="w-full mt-2">
                                <label for="permanent_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    City <span class="text-red-600">*</span>
                                </label>
                                <select wire:model.live="permanent_selectedCity" 
                                        id="permanent_city"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @if ($pcities && $pcities->count() > 0)
                                        <option value="">Select City</option>
                                        @foreach ($pcities as $city)
                                            <option value="{{ $city->city_municipality_description }}">
                                                {{ $city->city_municipality_description }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Select a province first</option>
                                    @endif
                                </select>
                                @error('permanent_selectedCity')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Barangay Dropdown -->
                            <div class="w-full mt-2">
                                <label for="permanent_barangay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Barangay <span class="text-red-600">*</span>
                                </label>
                                <select wire:model.live="permanent_selectedBarangay" 
                                        id="permanent_barangay"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @if ($pbarangays && $pbarangays->count() > 0)
                                        <option value="">Select Barangay</option>
                                        @foreach ($pbarangays as $barangay)
                                            <option value="{{ $barangay->barangay_description }}">
                                                {{ $barangay->barangay_description }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Select a city first</option>
                                    @endif
                                </select>
                                @error('permanent_selectedBarangay')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full mt-2">
                                <label for="p_house" class="block text-sm font-medium text-gray-700 dark:text-gray-300">House/Block/Lot No.</label>
                                <input type="text" id="p_house" wire:model.live="p_house"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                        bg-chalk border-zinc-300 placeholder-zinc-400 
                                        focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                        dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                        dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                @error('p_house')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full mt-2">
                                <label for="p_street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street</label>
                                <input type="text" id="p_street" wire:model.live="p_street"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                        bg-chalk border-zinc-300 placeholder-zinc-400 
                                        focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                        dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                        dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                @error('p_street')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full mt-2">
                                <label for="p_subdivision" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subdivision/Village</label>
                                <input type="text" id="p_subdivision" wire:model.live="p_subdivision"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                        bg-chalk border-zinc-300 placeholder-zinc-400 
                                        focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                        dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                        dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                @error('p_subdivision')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full mt-2">
                                <label for="permanent_selectedZipcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Zip Code <span class="text-red-600">*</span>
                                </label>
                                <input type="number" id="permanent_selectedZipcode" wire:model.live="permanent_selectedZipcode"
                                    class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                        bg-chalk border-zinc-300 placeholder-zinc-400 
                                        focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                        dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                        dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                @error('permanent_selectedZipcode')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    <div class="mt-4 mb-4 gap-2 columns-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="same_as_above" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Same As Above (Residential Address)</span>
                        </label>
                    </div>

                    @if (!$same_as_above)
                        <fieldset class="border border-gray-300 dark:border-gray-700 p-4 rounded-lg overflow-hidden w-full mb-4">
                            <legend class="text-gray-700 dark:text-gray-200 px-2"><i class="bi bi-pin-map"></i> Residential Address</legend>
                            <div class="mt-2">
                                <!-- Region Dropdown -->
                                <div class="w-full mt-2">
                                    <label for="residential_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Region <span class="text-red-600">*</span>
                                    </label>
                                    <select wire:model.live="residential_selectedRegion" 
                                            id="residential_region"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                                bg-chalk border-zinc-300 placeholder-zinc-400 
                                                focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                                dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                                dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                        <option value="">Select Region</option>
                                        @if($regions)
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->region_description }}">
                                                    {{ $region->region_description }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('residential_selectedRegion')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Province Dropdown -->
                                <div class="w-full mt-2">
                                    <label for="residential_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Province <span class="text-red-600">*</span>
                                    </label>
                                    <select wire:model.live="residential_selectedProvince" 
                                            id="residential_province"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                                bg-chalk border-zinc-300 placeholder-zinc-400 
                                                focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                                dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                                dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                        @if ($rprovinces && $rprovinces->count() > 0)
                                            <option value="">Select Province</option>
                                            @foreach ($rprovinces->sortBy('province_description') as $province)
                                                <option value="{{ $province->province_description }}">
                                                    {{ $province->province_description }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">Select a region first</option>
                                        @endif
                                    </select>
                                    @error('residential_selectedProvince')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- City Dropdown -->
                                <div class="w-full mt-2">
                                    <label for="residential_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        City <span class="text-red-600">*</span>
                                    </label>
                                    <select wire:model.live="residential_selectedCity" 
                                            id="residential_city"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                                bg-chalk border-zinc-300 placeholder-zinc-400 
                                                focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                                dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                                dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                        @if ($rcities && $rcities->count() > 0)
                                            <option value="">Select City</option>
                                            @foreach ($rcities as $city)
                                                <option value="{{ $city->city_municipality_description }}">
                                                    {{ $city->city_municipality_description }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">Select a province first</option>
                                        @endif
                                    </select>
                                    @error('residential_selectedCity')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Barangay Dropdown -->
                                <div class="w-full mt-2">
                                    <label for="residential_barangay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Barangay <span class="text-red-600">*</span>
                                    </label>
                                    <select wire:model.live="residential_selectedBarangay" 
                                            id="residential_barangay"
                                            class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                                bg-chalk border-zinc-300 placeholder-zinc-400 
                                                focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                                dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                                dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                        @if ($rbarangays && $rbarangays->count() > 0)
                                            <option value="">Select Barangay</option>
                                            @foreach ($rbarangays as $barangay)
                                                <option value="{{ $barangay->barangay_description }}">
                                                    {{ $barangay->barangay_description }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">Select a city first</option>
                                        @endif
                                    </select>
                                    @error('residential_selectedBarangay')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="w-full mt-2">
                                    <label for="r_house" class="block text-sm font-medium text-gray-700 dark:text-gray-300">House/Block/Lot No.</label>
                                    <input type="text" id="r_house" wire:model.live="r_house"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @error('r_house')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="w-full mt-2">
                                    <label for="r_street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street</label>
                                    <input type="text" id="r_street" wire:model.live="r_street"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @error('r_street')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="w-full mt-2">
                                    <label for="r_subdivision" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subdivision/Village</label>
                                    <input type="text" id="r_subdivision" wire:model.live="r_subdivision"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @error('r_subdivision')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="w-full mt-2">
                                    <label for="residential_selectedZipcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Zip Code <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" id="residential_selectedZipcode" wire:model.live="residential_selectedZipcode"
                                        class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                            bg-chalk border-zinc-300 placeholder-zinc-400 
                                            focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                            dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                            dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                    @error('residential_selectedZipcode')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>
                    @endif

                    <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                        <div class="w-full">
                            <label for="tel_number" class="text-sm"><i class="bi bi-telephone"></i> Telephone No.</label>
                            <input type="text" id="tel_number" wire:model.live="tel_number"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                        </div>
                        <div class="w-full">
                            <label for="mobile_number" class="text-sm"><i class="bi bi-phone"></i> Mobile No. <span
                                    class="text-red-600">*</span></label>
                            <input type="number" id="mobile_number" wire:model.live="mobile_number"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('mobile_number')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div x-data="{ appointment: @entangle('appointment') }" class="mt-4 gap-2 columns-1">
                        <div class="w-full">
                            <label for="office_division" class="text-sm">Nature of Appointment
                                <span class="text-red-600">*</span></label>
                            <select id="office_division" x-model="appointment"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <option value="">Please Choose one</option>
                                <option value="plantilla">Plantilla</option>
                                <option value="cos">Contract of Service</option>
                                <option value="ct">Co-Terminus</option>
                                <option value="pa">Presidential appointee</option>
                            </select>
                        </div>
                    </div>
                    @error('appointment')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                        <div class="w-full">
                            <label for="office_division" class="text-sm"><i class="bi bi-building"></i> Work Group <span
                                    class="text-red-600">*</span></label>
                            <select id="office_division" wire:model.live="selectedOfficeDivision"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <option value="" selected>Select a work group</option>
                                @foreach ($officeDivisions as $officeDivision)
                                    <option value="{{ $officeDivision->id }}">
                                        {{ $officeDivision->office_division }}</option>
                                @endforeach
                            </select>
                            @error('selectedOfficeDivision')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="w-full">
                            <label for="unit" class="text-sm"><i class="bi bi-people"></i> Unit</label>
                            <select id="unit" wire:model.live="selectedUnit"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <option value="" selected>Select a unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit }}</option>
                                @endforeach
                            </select>
                            @error('selectedUnit')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 gap-2 columns-1">
                        <div class="w-full">
                            <label for="position" class="text-sm"><i class="bi bi-person-gear"></i> Position <span
                                    class="text-red-600">*</span></label>
                            <select id="position" wire:model.live="selectedPosition"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                                <option value="" selected>Select a position</option>
                                @foreach ($positions as $position)
                                    @if ($position->position !== 'Super Admin')
                                        <option value="{{ $position->id }}">{{ $position->position }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('selectedPosition')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                        <div class="w-full">
                            <label for="date_hired" class="text-sm"><i class="bi bi-calendar-check"></i> Date of assumption <span
                                    class="text-red-600">*</span></label>
                            <input type="date" id="date_hired" wire:model.live="date_hired"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('date_hired')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="w-full">
                            <label for="emp_code" class="text-sm"><i class="bi bi-credit-card-2-front"></i> Employee Code<span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="emp_code" wire:model.live="emp_code"
                                inputmode="numeric" pattern="[0-9]*"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('emp_code')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                        <div class="w-full">
                            <label for="email" class="text-sm"><i class="bi bi-envelope-at"></i> Email Address<span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="email" wire:model.live="email"
                                class="w-full h-10 px-4 py-2 text-black border rounded-lg appearance-none 
                                    bg-chalk border-zinc-300 placeholder-zinc-400 
                                    focus:border-zinc-400 focus:outline-none focus:ring-zinc-400 sm:text-sm
                                    dark:bg-gray-900 dark:text-white dark:border-gray-700 
                                    dark:placeholder-zinc-500 dark:focus:border-zinc-500 dark:focus:ring-zinc-500">
                            @error('email')
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
                            <button
                                class="inline-flex items-center justify-center w-full h-10 gap-3 px-5 py-3 font-medium text-white bg-green-700 rounded-xl hover:bg-green-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                wire:click="saveEmployeee" wire:loading.attr="disabled" wire:target="saveEmployeee">
                                <span wire:loading.remove wire:target="saveEmployeee">Submit</span>
                                <span wire:loading wire:target="saveEmployeee">Submitting...</span>
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

    {{-- Success Modal --}}
    <div
        x-data="{ show: @entangle('isSuccessful') }"
        x-show="show"
        id="successModal"
        class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 flex items-start justify-center"
        style="display: none;z-index: 9999;"
    >
        <div x-show="show" class="fixed inset-0 transform transition-all"  
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500/80 dark:bg-gray-900/80 backdrop-blur-sm"></div>
        </div>

        <div x-show="show"
            class="mb-6 bg-white dark:bg-gray-800 rounded-lg dark:border dark:border-slate-700 overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
            x-trap.inert.noscroll="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="p-6 sm:p-8">
                {{-- Success Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <svg class="w-16 h-16 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Title --}}
                <div class="text-center mb-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-gray-100">
                        Employee Registered
                    </h3>
                </div>

                {{-- Message --}}
                <div class="text-center mb-6">
                    <p class="text-base text-gray-600 dark:text-slate-400">
                        Employee was registered successfully and has been added to the system.
                    </p>
                </div>


                {{-- Toast Notification --}}
                <div x-data="{ show: false }" 
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-[-20px]"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-[-20px]"
                    @copy-success.window="show = true; setTimeout(() => show = false, 2500)"
                    class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
                    style="display: none;">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-medium">Text copied!</span>
                    </div>
                </div>

                {{-- Credentials Box --}}
                <div x-data="{
                    buttonText: 'Copy Credentials',
                    
                    copyCredentials(fullName, email, password) {
                        const text = `Fullname: ${fullName}\nEmail: ${email}\nPassword: ${password}`;
                        
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(() => {
                                this.showSuccess();
                            }).catch(err => {
                                console.error('Failed to copy:', err);
                                this.fallbackCopy(text);
                            });
                        } else {
                            this.fallbackCopy(text);
                        }
                    },
                    
                    fallbackCopy(text) {
                        const textarea = document.createElement('textarea');
                        textarea.value = text;
                        textarea.style.position = 'fixed';
                        textarea.style.left = '-999999px';
                        textarea.style.top = '-999999px';
                        document.body.appendChild(textarea);
                        textarea.focus();
                        textarea.select();
                        
                        try {
                            const successful = document.execCommand('copy');
                            document.body.removeChild(textarea);
                            
                            if (successful) {
                                this.showSuccess();
                            } else {
                                alert('Failed to copy text. Please copy manually.');
                            }
                        } catch (err) {
                            document.body.removeChild(textarea);
                            console.error('Failed to copy:', err);
                            alert('Failed to copy text. Please copy manually.');
                        }
                    },
                    
                    showSuccess() {
                        window.dispatchEvent(new CustomEvent('copy-success'));
                        this.buttonText = 'Copied!';
                        setTimeout(() => {
                            this.buttonText = 'Copy Credentials';
                        }, 2000);
                    }
                }" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-5 mb-6 border border-gray-200 dark:border-gray-700">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Fullname:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $fullName }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Email:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $email }}</span>
                        </div>
                        <div class="h-px bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Temporary Password:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $password }}</span>
                        </div>
                    </div>
                    
                    {{-- Copy Button --}}
                    <button type="button"
                            @click="copyCredentials('{{ $fullName }}', '{{ $email }}', '{{ $password }}')"
                            class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span x-text="buttonText"></span>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent='closePopup'>
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="relative inline-flex items-center justify-center px-8 py-3 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 min-w-[140px]">
                            <span wire:loading.remove wire:target="closePopup">
                                Continue
                            </span>
                            <span wire:loading wire:target="closePopup" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>


<script>
    function limitInput(event, maxLength) {
        if (event.target.value.length > maxLength) {
            event.target.value = event.target.value.slice(0, maxLength);
            this[event.target.getAttribute('x-model')] = event.target.value;
        }
    }
</script>