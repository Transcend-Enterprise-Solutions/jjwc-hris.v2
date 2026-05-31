<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">

    <title>Contact Us - JJWC HRIS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #13072E;
            --navy-mid: #1e0f45;
            --gold: #C9A84C;
            --gold-light: #f0d98a;
            --cream: #f5f5f5;
            --text: #1a1a2e;
            --muted: #6b6b80;
            --border: rgba(0, 0, 0, 0.08);
        }

        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        /* Hero */
        .hero {
            background: var(--navy);
            border-radius: 20px;
            padding: 2.5rem 2rem 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            right: -60px;
            top: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(201, 168, 76, 0.12);
        }
        .hero::after {
            content: '';
            position: absolute;
            left: 40%;
            bottom: -40px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(201, 168, 76, 0.07);
        }

        .logos {
            display: flex;
            gap: 1rem;
            align-items: center;
            z-index: 1;
            flex-shrink: 0;
        }
        .logo-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .logo-sep {
            color: var(--gold);
            font-size: 18px;
            opacity: 0.7;
        }

        .hero-text { z-index: 1; }
        .hero-text h1 {
            font-family: 'DM Serif Display', serif;
            color: #fff;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            line-height: 1.2;
            margin-bottom: 0.4rem;
        }
        .hero-text p {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.88rem;
            font-weight: 300;
            letter-spacing: 0.02em;
        }
        .gold-line {
            width: 32px;
            height: 2px;
            background: var(--gold);
            border-radius: 2px;
            margin: 0 0 0.6rem;
        }

        /* Grids */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        /* Cards */
        .card {
            background: #fff;
            border-radius: 14px;
            border: 0.5px solid var(--border);
            padding: 1.25rem;
        }
        .card-accent { border-top: 3px solid var(--gold); }

        .card-icon {
            width: 36px;
            height: 36px;
            background: var(--navy);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
            flex-shrink: 0;
        }
        .card-icon svg {
            width: 17px;
            height: 17px;
            fill: var(--gold);
        }

        .card-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: 0.3rem;
        }
        .card-value {
            font-size: 0.92rem;
            color: var(--text);
            line-height: 1.6;
            font-weight: 400;
        }
        .card-value strong {
            font-weight: 500;
            color: var(--navy);
        }

        /* Hours */
        .hours-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.45rem 0;
            border-bottom: 0.5px solid var(--border);
        }
        .hours-row:last-child { border-bottom: none; }
        .hours-day { font-size: 0.83rem; color: var(--muted); }
        .badge-closed {
            font-size: 0.72rem;
            background: #fef2f2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }
        .badge-open {
            font-size: 0.72rem;
            background: #f0fdf4;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Social */
        .social-row {
            display: flex;
            gap: 0.6rem;
            margin-top: 0.8rem;
            flex-wrap: wrap;
        }
        .social-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 8px;
            border: 0.5px solid var(--border);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--navy);
            text-decoration: none;
            background: #fff;
            transition: background 0.15s;
        }
        .social-btn:hover { background: var(--cream); }
        .social-btn svg { width: 15px; height: 15px; }

        /* Map */
        .map-card {
            background: #fff;
            border-radius: 14px;
            border: 0.5px solid var(--border);
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .map-header {
            padding: 1rem 1.25rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-bottom: 0.5px solid var(--border);
        }
        .map-header span {
            font-family: 'DM Serif Display', serif;
            font-size: 1rem;
            color: var(--navy);
        }
        .map-pin {
            width: 8px;
            height: 8px;
            background: var(--gold);
            border-radius: 50%;
            flex-shrink: 0;
        }
        .map-frame {
            height: 260px;
            display: block;
            width: 100%;
            border: 0;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .grid-2 { grid-template-columns: 1fr; }
            .grid-3 { grid-template-columns: 1fr; }
            .hero { flex-direction: column; text-align: center; gap: 1rem; }
            .logos { justify-content: center; }
            .gold-line { margin: 0 auto 0.6rem; }
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- Hero --}}
        <div class="hero">
            <div class="logos">
                <div class="logo-circle">
                    <img src="/images/logo.png"
                         onerror="this.src='https://placehold.co/52x52/1e0f45/C9A84C?text=JJ'"
                         alt="JJWC Logo">
                </div>
                <div class="logo-sep">✦</div>
                <div class="logo-circle">
                    <img src="/images/bagong-pilipinas-logo.png"
                         onerror="this.src='https://placehold.co/52x52/1e0f45/C9A84C?text=BP'"
                         alt="Bagong Pilipinas Logo">
                </div>
            </div>
            <div class="hero-text">
                <div class="gold-line"></div>
                <h1>Contact Us</h1>
                <p>Juvenile Justice and Welfare Council — Philippines</p>
            </div>
        </div>

        {{-- Address & Phone --}}
        <div class="grid-2">
            <div class="card card-accent">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
                <div class="card-label">Office Address</div>
                <div class="card-value">
                    56 Matimtiman Street<br>
                    Teachers Village East<br>
                    <strong>Quezon City, Metro Manila 1101</strong>
                </div>
            </div>

            <div class="card card-accent">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                </div>
                <div class="card-label">Contact Numbers</div>
                <div class="card-value">
                    <strong>Landline</strong><br>
                    (+632) 8921-0565<br>
                    (+632) 8794-5972
                </div>
            </div>
        </div>

        {{-- Email, Hours, Social --}}
        <div class="grid-3">
            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </div>
                <div class="card-label">Email</div>
                <div class="card-value">
                    <strong>Secretariat</strong><br>
                    secretariat@jjwc.gov.ph
                </div>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm-1 15v-5H9l3-7v5h2l-3 7z"/></svg>
                </div>
                <div class="card-label">Office Hours</div>
                <div class="card-value">
                    <div class="hours-row">
                        <span class="hours-day">Mon – Fri</span>
                        <span class="badge-open">8AM – 5PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-day">Sat – Sun</span>
                        <span class="badge-closed">Closed</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>
                </div>
                <div class="card-label">Connect With Us</div>
                <div class="social-row">
                    <a href="https://www.facebook.com/JJWCOfficial" target="_blank" rel="noopener noreferrer" class="social-btn">
                        <svg viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        Facebook
                    </a>
                    <a href="https://x.com/JJWCOfficial" target="_blank" rel="noopener noreferrer" class="social-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        X
                    </a>
                    <a href="https://www.youtube.com/channel/UCVIgDFokwndloN-C00gzWNQ" target="_blank" rel="noopener noreferrer" class="social-btn">
                        <svg viewBox="0 0 24 24" fill="#FF0000"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-1.96C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.4 19.54C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 001.94-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
                        YouTube
                    </a>
                </div>
            </div>
        </div>

        {{-- Map --}}
        <div class="map-card">
            <div class="map-header">
                <div class="map-pin"></div>
                <span>Find Us Here</span>
            </div>
            <iframe
                class="map-frame"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.014389424115!2d121.0474923148485!3d14.65569938976542!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b757f2b1979b%3A0x5c1d0c5f5b6e5a3a!2s56%20Matimtiman%20St%2C%20Lungsod%20Quezon%2C%20Kalakhang%20Maynila!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
                allowfullscreen
                loading="lazy"
                title="JJWC Office Location - 56 Matimtiman Street, Teachers Village East, Quezon City">
            </iframe>
        </div>

    </div>

    @livewireScripts
</body>

</html>
