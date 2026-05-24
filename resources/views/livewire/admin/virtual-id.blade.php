<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
            {{-- <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Employees Virtual ID</h1> --}}

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-lg font-bold text-black dark:text-white">Employees Virtual ID</h1>
                <button wire:click="openSignatoryModal"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="bi bi-pen-fill mr-1"></i> Edit Signatory
                </button>
            </div>

            <!-- Employee Selection -->
            <div class="mb-6 relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Employee
                </label>

                <div class="relative">
                    <input type="text" wire:model.live="searchTerm" wire:focus="showEmployeeDropdown = true"
                        wire:click="showEmployeeDropdown = true" placeholder="Search employee by name or ID..."
                        class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">

                    <!-- Employee Dropdown -->
                    @if ($showEmployeeDropdown)
                        <div class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 max-h-60 overflow-y-auto"
                            wire:click.away="showEmployeeDropdown = false">
                            @forelse($employees as $employee)
                                <div wire:click="selectEmployee('{{ $employee->id }}')"
                                    class="p-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ $employee->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Employee Code: {{ $employee->emp_code }}
                                        </span>
                                    </div>
                                    @if ($employee->officeDivision)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $employee->officeDivision->office_division }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-3 text-gray-500 dark:text-gray-400">
                                    No employees found
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

            <x-modal id="showSignatoryModal" maxWidth="lg" centered wire:model="showSignatoryModal">
                <div class="p-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Edit Signatory Details
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signatory
                                    Name</label>
                                <input type="text" wire:model="signatoryName"
                                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                            </div>

                            <!-- Position Dropdown -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                <select wire:model="signatoryPositionId"
                                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                    <option value="">Select Position</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->position }}</option>
                                    @endforeach
                                </select>
                                @error('signatoryPositionId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Office Division Dropdown -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Office
                                    Division</label>
                                <select wire:model="signatoryOfficeDivisionId"
                                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                    <option value="">Select Office Division</option>
                                    @foreach ($officeDivisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->office_division }}</option>
                                    @endforeach
                                </select>
                                @error('signatoryOfficeDivisionId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signatory
                                    Signature</label>

                                @if ($tempSignatureUrl)
                                    <div class="mb-2">
                                        <img src="{{ $tempSignatureUrl }}" alt="Signature Preview" class="h-20 border">
                                    </div>
                                @endif

                                <input type="file" wire:model="signatorySignature"
                                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                <p class="text-xs text-gray-500 mt-1">Upload signature image (max 2MB)</p>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button wire:click="showSignatoryModal = false"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                                    Cancel
                                </button>
                                <button wire:click="saveSignatoryDetails"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-modal>

            <!-- ID Display Controls -->
            @if ($selectedEmployeeId)
                <div class="w-full mb-6 text-center relative">
                    <div class="flex items-center justify-center gap-2">
                        <h1 class="text-lg font-bold text-slate-800 dark:text-white">
                            {{ $idType === 'virtual' ? 'Virtual ID' : 'ARTA ID' }}
                        </h1>
                        <button wire:click="switchIdType" type="button" class="p-2 dark:bg-slate-600">
                            <i class="bi bi-arrow-repeat text-slate-800 dark:text-white"></i>
                        </button>
                    </div>

                    <!-- Export Buttons -->
                    <div class="absolute top-0 right-0">
                        <div class="relative">
                            <button wire:click="toggleDropdown"
                                class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 focus:outline-none">
                                <i class="bi bi-three-dots-vertical text-slate-800 dark:text-white"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            {{-- <div wire:click.away="closeDropdown"
                                class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-slate-700 ring-1 ring-black ring-opacity-5 z-50 {{ $showDropdown ? 'block' : 'hidden' }}">
                                <div class="p-2">
                                    @if ($idType === 'virtual')
                                        <!-- Virtual ID Options -->
                                        <button onclick="exportVirtualFront()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export Front ID
                                        </button>
                                        <button onclick="exportVirtualBack()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export Back ID
                                        </button>
                                    @else
                                        <!-- ARTA ID Options -->
                                        <button onclick="exportArtaId()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export ARTA ID
                                        </button>
                                    @endif
                                </div>
                            </div> --}}
                            <div wire:click.away="closeDropdown"
                                class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-slate-700 ring-1 ring-black ring-opacity-5 z-50 {{ $showDropdown ? 'block' : 'hidden' }}">
                                <div class="p-2">
                                    @if ($idType === 'virtual')
                                        <button onclick="printVirtualID()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Print Virtual ID (Front & Back)
                                        </button>
                                        <button onclick="exportVirtualFront()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export Front ID
                                        </button>
                                        <button onclick="exportVirtualBack()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export Back ID
                                        </button>
                                    @else
                                        <button onclick="printArtaID()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Print ARTA ID
                                        </button>
                                        <button onclick="exportArtaId()"
                                            class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md transition-all">
                                            Export ARTA ID
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ID Display -->
                @if ($idType === 'arta')
                    <!-- ARTA ID Layout -->
                    <div class="grid grid-cols-1 gap-6 mx-auto" style="width: 550px;">
                        <div id="arta-id-container"
                            class="w-full h-[600px] bg-white p-6 shadow-lg border rounded-lg relative"
                            style="background-image: url('/images/arta-bg-darker.png'); background-size: cover; background-position: center;">
                            <!-- Header -->
                            <div class="flex items-center justify-center mb-6">
                                <img src="/images/jjwc-logo-transparent.png" class="h-16" alt="Company Logo">
                                <div class="text-left">
                                    <h2 class="text-md font-bold text-black"
                                        style="font-family: 'Arial Black', Gadget, sans-serif;">Juvenile Justice and Welfare Council</h2>
                                    <p class="text-xs text-black -mt-1 font-bold"
                                        style="font-family: 'Arial', sans-serif;">
                                        JJWC Building, 116 Tordesillas St., Salcedo Village,
                                    </p>
                                    <p class="text-xs text-black -mt-1 font-bold"
                                        style="font-family: 'Arial', sans-serif;">
                                        Makati City, Philippines 1227
                                    </p>
                                </div>
                            </div>

                            <!-- Profile Photo -->
                            <div class="flex justify-center mb-4">
                                <div class="w-40 h-40 border border-gray-400 bg-white">
                                    @if ($profilePhotoUrl)
                                        <img src="{{ $profilePhotoUrl }}" alt="Profile Photo"
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null;this.innerHTML='<span class=\'text-green-500 flex items-center justify-center h-full\'>No Photo</span>';">
                                    @else
                                        <span class="text-green-500 flex items-center justify-center h-full">No
                                            Photo</span>
                                    @endif
                                </div>
                            </div>

                            <!-- E-Signature -->
                            <div class="flex justify-center mb-4" style="height: 40px;">
                                @if ($eSignatureUrl)
                                    <img src="{{ $eSignatureUrl }}" alt="E-Signature" class="h-full object-contain"
                                        onerror="this.onerror=null;this.innerHTML='<span class=\'text-red-500 text-sm\'>SIGN HERE</span>';">
                                @else
                                    <span class="text-red-500 text-sm">SIGN HERE</span>
                                @endif
                            </div>

                            <!-- Information -->
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-black"
                                    style="font-family: 'Arial Black', Gadget, sans-serif;">{{ $name }}
                                </h3>
                                <p class="text-sm text-center text-black tracking-tighter font-bold"
                                    style="font-family: 'Arial', sans-serif;">
                                    {{ $office_or_department }}</p>
                                <p class="text-sm mt-4 text-black">ID No: <span
                                        class="font-bold">{{ $emp_code }}</span>
                                </p>
                            </div>

                            <!-- QR Code -->
                            <div class="flex justify-center mb-2">
                                <div class="flex items-center justify-center bg-white p-1 border border-gray-200">
                                    {!! $this->getQrCodeHtml() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Virtual ID Layout -->
                    <div class="grid grid-cols-1 gap-6 mx-auto" style="width: 550px;">
                        <!-- Front Side -->
                        <div id="virtual-id-front"
                            class="w-full h-[340px] bg-white p-4 shadow-lg border rounded-lg relative"
                            style="background-image: url('/images/id-bg-darker.png');">
                            <h2 class="text-2xl font-bold text-black text-left ml-8 tracking-normal"
                                style="font-family: 'Arial Black', Gadget, sans-serif;">
                                {{ $name }}
                            </h2>
                            <p class="text-sm text-left ml-8 text-black tracking-tighter font-bold"
                                style="font-family: 'Arial', sans-serif;">
                                {{ $office_or_department }}</p>

                            <div class="flex items-center h-[250px] ml-4">
                                <div class="flex flex-col items-center space-y-1">
                                    <!-- Picture Box -->
                                    <div
                                        class="w-40 h-40 border border-gray-400 flex items-center justify-center bg-white mt-2">
                                        @if ($profilePhotoUrl)
                                            <img src="{{ $profilePhotoUrl }}" alt="Profile Photo"
                                                class="w-full h-full object-cover"
                                                onerror="this.onerror=null;this.innerHTML='<span class=\'text-green-500\'>Picture</span>';">
                                        @else
                                            <span class="text-green-500">Picture</span>
                                        @endif
                                    </div>

                                    <!-- SIGN HERE -->
                                    @if ($eSignatureUrl)
                                        <div class="flex items-center justify-center" style="height: 48px;">
                                            <img src="{{ $eSignatureUrl }}" alt="E-Signature"
                                                class="max-w-full max-h-full object-contain"
                                                onerror="this.onerror=null;this.parentElement.innerHTML='<p class=\'text-red-500 text-sm\'>SIGN HERE</p>';">
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center" style="height: 48px;">
                                            <p class="text-red-500 text-sm">SIGN HERE</p>
                                        </div>
                                    @endif

                                    <!-- ID Number -->
                                    <p class="text-sm text-black">ID No. {{ $emp_code }}</p>
                                </div>
                            </div>

                            <div
                                class="absolute top-[230px] right-7 transform -translate-y-1/2 flex flex-col items-center text-center">
                                <img src="/images/jjwc-logo-transparent.png" class="h-24" alt="">
                                <p class="text-xs text-black -mt-2">
                                    JJWC Building, 116 Tordesillas St.,<br> Salcedo Village, Makati City, Philippines
                                    1227
                                </p>
                                <p class="text-xs text-black">T (632) 8840-4838 to 62</p>
                            </div>
                        </div>

                        <!-- Back Side -->
                        <div id="virtual-id-back"
                            class="w-full h-[340px] bg-white p-4 shadow-lg border rounded-lg relative"
                            style="background-image: url('/images/id-bg-darker.png');">
                            <div class="flex justify-between items-center m-4">
                                <div class="w-[70%]">
                                    <h2 class="text-sm font-bold text-black"
                                        style="font-family: 'Arial Black', Gadget, sans-serif;">IN CASE OF
                                        EMERGENCY,
                                        PLEASE NOTIFY:</h2>
                                    <p class="text-sm font-bold text-black" style="font-family: 'Arial', sans-serif;">
                                        NAME: <span class="font-normal"
                                            style="font-family: 'Arial Black', Gadget, sans-serif;">{{ $emergencyContactName }}</span>
                                    </p>
                                    <p class="text-sm font-bold text-black" style="font-family: 'Arial', sans-serif;">
                                        TEL. NO.: <span class="font-normal"
                                            style="font-family: 'Arial Black', Gadget, sans-serif;">{{ $emergencyContactNumber }}</span>
                                    </p>
                                </div>

                                <div class="w-[30%] flex items-center justify-center">
                                    <div class="w-24 h-24 flex items-center justify-center">
                                        {!! $this->getQrCodeHtml() !!}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center m-4">
                                <div class="w-[65%]">
                                    <p class="text-sm text-black text-justify tracking-tight leading-none"
                                        style="font-family: 'Arial Black', Gadget, sans-serif;">
                                        This certifies that the person whose name, picture, and signature appear on
                                        this
                                        card is an
                                        employee of the <span class="font-bold">National Development
                                            Company.</span>
                                    </p>
                                </div>

                                <div class="w-[30%] flex items-center justify-center space-x-2">
                                    <div class="w-[60px] h-[60px] flex items-center justify-center">
                                        <img src="/images/dti-logo.png" alt="dti-logo">
                                    </div>
                                    <div class="w-[55px] h-[55px] flex items-center justify-center">
                                        <img src="/images/tuv.png" alt="tuv-logo">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center m-4">
                                <div class="w-[65%]">
                                    <p class="text-sm text-black text-justify tracking-tight leading-none"
                                        style="font-family: 'Arial Black', Gadget, sans-serif;">
                                        Report loss of card to the Human Resources Unit for immediate replacement.
                                        Finder of this is
                                        requested to return it to the Juvenile Justice and Welfare Council or call (02)
                                        8840-4838.
                                    </p>
                                </div>

                                <div class="w-[35%] flex flex-col items-center text-center justify-center space-x-2">
                                    @if ($this->getSignatorySignatureUrl())
                                        <img src="{{ $this->getSignatorySignatureUrl() }}" alt="Signatory Signature"
                                            class="h-12 -m-2">
                                    @else
                                        <p class="text-red-500 font-bold">SIGN</p>
                                    @endif
                                    <p class="text-[11px] text-black font-bold uppercase">
                                        {{ $signatoryName ?? 'Atty. RHOEL Z. MABAZZA' }}
                                    </p>
                                    <p class="text-[10px] text-black font-bold">
                                        {{ $defaultSignatory->position->position ?? 'Assistant General Manager' }}
                                    </p>
                                    <p class="text-[10px] text-black font-bold">
                                        {{ $defaultSignatory->officeDivision->office_division ?? 'Corporate Support Group' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    // Make sure html2canvas is loaded
    function ensureHtml2CanvasIsLoaded() {
        return new Promise((resolve, reject) => {
            if (typeof html2canvas === 'undefined') {
                // If html2canvas is not loaded, dynamically load it
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Failed to load html2canvas'));
                document.head.appendChild(script);
            } else {
                resolve();
            }
        });
    }

    // Export Virtual ID Front
    function exportVirtualFront() {
        console.log("Exporting Virtual ID Front");
        ensureHtml2CanvasIsLoaded().then(() => {
            const element = document.getElementById('virtual-id-front');
            if (!element) {
                console.error("Front ID container not found!");
                return;
            }

            html2canvas(element, {
                scale: 2, // Higher quality
                logging: true, // Helpful for debugging
                useCORS: true // For external images
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png', 1.0); // Highest quality
                link.download = 'Front-ID.png';
                link.click();
            }).catch(err => {
                console.error("Error generating front ID:", err);
            });
        }).catch(err => {
            console.error("Failed to load html2canvas:", err);
        });
    }

    // Export Virtual ID Back
    function exportVirtualBack() {
        console.log("Exporting Virtual ID Back");
        ensureHtml2CanvasIsLoaded().then(() => {
            const element = document.getElementById('virtual-id-back');
            if (!element) {
                console.error("Back ID container not found!");
                return;
            }

            html2canvas(element, {
                scale: 2,
                logging: true,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png', 1.0);
                link.download = 'Back-ID.png';
                link.click();
            }).catch(err => {
                console.error("Error generating back ID:", err);
            });
        }).catch(err => {
            console.error("Failed to load html2canvas:", err);
        });
    }

    // Export ARTA ID
    function exportArtaId() {
        console.log("Exporting ARTA ID");
        ensureHtml2CanvasIsLoaded().then(() => {
            const element = document.getElementById('arta-id-container');
            if (!element) {
                console.error("ARTA ID container not found!");
                return;
            }

            html2canvas(element, {
                scale: 2,
                logging: true,
                useCORS: true,
                backgroundColor: null // For transparent backgrounds
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png', 1.0);
                link.download = 'ARTA-ID.png';
                link.click();
            }).catch(err => {
                console.error("Error generating ARTA ID:", err);
            });
        }).catch(err => {
            console.error("Failed to load html2canvas:", err);
        });
    }

    function printVirtualID() {
        // Clone the original elements with all their styles
        const frontElement = document.getElementById('virtual-id-front').cloneNode(true);
        const backElement = document.getElementById('virtual-id-back').cloneNode(true);

        // Create a print window
        const printWindow = window.open('', '_blank');

        // Add all original styles to the print window
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Virtual ID</title>
            <style>
                ${getAllCSS()}
                @page { size: auto; margin: 0mm; }
                body { 
                    margin: 16px; 
                    padding: 16px; 
                    display: flex;
                    flex-direction: column;
                    align-items: end;
                    justify-content: center;
                    min-height: 100vh;
                }
                .print-page {
                    width: 550px;
                    height: 340px;
                    page-break-after: always;
                    margin: 0 auto;
                    padding: 16px;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                }
                
                /* Force background images to print */
                * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                
                /* Specific background image fixes */
                .print-page {
                    background-image: url('/images/id-bg-darker.png') !important;
                    background-size: cover !important;
                    background-position: center !important;
                    background-repeat: no-repeat !important;
                    position: relative !important;
                }
                
                /* Fix absolute positioning for print */
                .print-page .absolute {
                    position: absolute !important;
                }
                
                .print-page .top-\\[230px\\] {
                    top: 230px !important;
                }
                
                .print-page .right-7 {
                    right: 28px !important; /* 1.75rem = 28px */
                }
            </style>
        </head>
        <body>
            <div class="print-page">${frontElement.innerHTML}</div>
            <div class="print-page">${backElement.innerHTML}</div>
            <script>
                // Auto-print after content loads
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
        printWindow.document.close();
    }

    // Function to get all CSS from the page
    function getAllCSS() {
        let css = '';
        const styleSheets = document.styleSheets;

        for (let i = 0; i < styleSheets.length; i++) {
            try {
                const rules = styleSheets[i].cssRules;
                for (let j = 0; j < rules.length; j++) {
                    css += rules[j].cssText + '\n';
                }
            } catch (e) {
                // Skip cross-origin stylesheets that throw errors
            }
        }

        // Add inline styles from the page
        const inlineStyles = document.querySelectorAll('style');
        inlineStyles.forEach(style => {
            css += style.innerHTML + '\n';
        });

        return css;
    }

    // Function to print ARTA ID
    function printArtaID() {
        const element = document.getElementById('arta-id-container').cloneNode(true);

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print ARTA ID</title>
            <style>
                ${getAllCSS()}
                @page { size: auto; margin: 0mm; }
                body { 
                    margin: 0; 
                    padding: 0; 
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
                .print-page {
                    width: 550px;
                    height: 600px;
                    margin: 0 auto;
                    padding: 24px;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                }
                
                /* Force background images to print */
                * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                
                /* Specific background image fixes */
                .print-page {
                    background-image: url('/images/arta-bg-darker.png') !important;
                    background-size: cover !important;
                    background-position: center !important;
                    background-repeat: no-repeat !important;
                    position: relative !important;
                }
                
                /* Fix absolute positioning for print */
                .print-page .absolute {
                    position: absolute !important;
                }
            </style>
        </head>
        <body>
            <div class="print-page">${element.innerHTML}</div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>
