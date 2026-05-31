<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">

    <title>Data Privacy Consent - JJWC HRIS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .privacy-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .agency-logo, .bp-logo {
            text-align: center;
            flex: 1;
        }

        .agency-logo img, .bp-logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        .agency-name {
            font-size: 14px;
            color: #4b5563;
            margin-top: 8px;
            font-weight: 500;
        }

        .divider {
            font-size: 24px;
            color: #d1d5db;
            padding: 0 20px;
        }

        .privacy-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .privacy-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .privacy-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .privacy-content {
            max-height: 500px;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        .privacy-section {
            margin-bottom: 1.5rem;
        }

        .privacy-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .privacy-section p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .privacy-section ul {
            list-style: disc;
            padding-left: 1.5rem;
            color: #4b5563;
            line-height: 1.6;
        }

        .consent-box {
            background: #f3f4f6;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }

        .checkbox-container input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            cursor: pointer;
        }

        .checkbox-container label {
            font-size: 16px;
            color: #1f2937;
            line-height: 1.5;
            cursor: pointer;
        }

        .checkbox-container .required {
            color: #ef4444;
        }

        .button-container {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #9ca3af;
            color: white;
        }

        .btn-secondary:hover {
            background: #6b7280;
            transform: translateY(-2px);
        }

        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .privacy-container {
                margin: 1rem;
                padding: 1rem;
            }

            .logo-container {
                flex-direction: column;
                gap: 1rem;
            }

            .divider {
                display: none;
            }

            .button-container {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body class="font-inter antialiased">
    <div class="privacy-container">
        <!-- Logo Section -->
        <div class="logo-container">
            <div class="agency-logo">
                <!-- Agency Logo Placeholder -->
                <div class="w-24 h-24 mx-auto flex items-center justify-center overflow-hidden">
                    <img src="/images/logo.png" alt="JJWC Logo" 
                         onerror="this.src='https://placehold.co/100x100/e5e7eb/6b7280?text=JJWC'"
                         class="w-full h-full object-cover">
                </div>
                <div class="agency-name">
                    <strong>Juvenile Justice and Welfare Council</strong>
                </div>
            </div>
            
            <div class="divider">✦</div>
            
            <div class="bp-logo">
                <!-- Bagong Pilipinas Logo Placeholder -->
                <div class="w-24 h-24 mx-auto flex items-center justify-center overflow-hidden">
                    <img src="/images/bagong-pilipinas-logo.png" alt="Bagong Pilipinas Logo" 
                         onerror="this.src='https://placehold.co/100x100/e5e7eb/6b7280?text=Bagong+Pilipinas'"
                         class="w-full h-full object-cover">
                </div>
                <div class="agency-name">
                    <strong>Bagong Pilipinas</strong>
                </div>
            </div>
        </div>

        <!-- Privacy Policy Content -->
        <div class="privacy-header">
            <h1>Data Privacy Policy</h1>
            <p>Republic Act No. 10173 - Data Privacy Act of 2012</p>
        </div>

        <div class="privacy-content" x-data="{ expanded: false }">
            <div class="privacy-section">
                <h3>1. Collection of Personal Information</h3>
                <p>JJWC collects personal information necessary for employment, records management, and compliance with government regulations. This includes but is not limited to:</p>
                <ul>
                    <li>Personal details (name, date of birth, contact information)</li>
                    <li>Employment history and qualifications</li>
                    <li>Government-issued IDs and numbers</li>
                    <li>Educational background</li>
                    <li>Family and emergency contact details</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h3>2. Purpose of Data Collection</h3>
                <p>Your personal data will be used for the following purposes:</p>
                <ul>
                    <li>Employment processing and administration</li>
                    <li>Compliance with government reporting requirements (GSIS, PhilHealth, Pag-IBIG, BIR)</li>
                    <li>Payroll and benefits processing</li>
                    <li>Performance evaluation and career development</li>
                    <li>Emergency and security purposes</li>
                    <li>Communication of official matters</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h3>3. Data Protection Measures</h3>
                <p>JJWC implements appropriate security measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction. These include:</p>
                <ul>
                    <li>Secure servers and encrypted databases</li>
                    <li>Role-based access controls</li>
                    <li>Regular security audits</li>
                    <li>Confidentiality agreements for personnel handling data</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h3>4. Data Sharing and Disclosure</h3>
                <p>Your information may be shared only when necessary and as required by law:</p>
                <ul>
                    <li>Government agencies (Civil Service Commission, COA, etc.)</li>
                    <li>Partner institutions with your consent</li>
                    <li>Legal requirements or court orders</li>
                </ul>
                <p>JJWC does not sell or trade your personal information to third parties.</p>
            </div>

            <div class="privacy-section">
                <h3>5. Data Retention</h3>
                <p>Your personal data will be retained for as long as necessary to fulfill the purposes stated above, or as required by applicable laws and regulations. After the retention period, your data will be securely disposed of.</p>
            </div>

            <div class="privacy-section">
                <h3>6. Your Rights as a Data Subject</h3>
                <p>Under the Data Privacy Act, you have the right to:</p>
                <ul>
                    <li>Be informed of how your data is processed</li>
                    <li>Access your personal data</li>
                    <li>Correct inaccurate or incomplete data</li>
                    <li>Object to processing under certain circumstances</li>
                    <li>Request data portability</li>
                    <li>File a complaint with the National Privacy Commission</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h3>7. Contact Information</h3>
                <p>For questions or concerns regarding your data privacy, contact us:</p>
                  <div class="w-[100%] mx-auto flex items-center justify-center overflow-hidden bg-[#13072E] p-10">
                    <img src="/images/contact-us.png" alt="JJWC Contact Information" 
                         onerror="this.src='https://placehold.co/100x100/e5e7eb/6b7280?text=JJWC'"
                         class="w-[400px]">
                </div>
            </div>
        </div>


    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('privacyConsent', () => ({
                consented: false,
                attempted: false,
                submitConsent() {
                    if (this.consented) {
                        // Handle consent submission
                        fetch('{{ route("data-privacy-policy") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                consent: true
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '{{ route("/dashboard") }}';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                    }
                }
            }));
        });
    </script>

    @livewireScripts
</body>

</html>
