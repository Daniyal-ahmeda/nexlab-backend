<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexLab Clinical OS — Diagnostic Operations & Lab Management Console</title>
    <!-- Google Fonts & Tailwind CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        clinical: {
                            50: '#F0FDFA',
                            100: '#CCFBF1',
                            200: '#99F6E4',
                            300: '#5EEAD4',
                            400: '#2DD4BF',
                            500: '#14B8A6',
                            600: '#0D9488',
                            700: '#0F766E',
                            800: '#115E59',
                            900: '#134E4A',
                        },
                        surface: {
                            base: '#090D16',
                            card: '#0F1626',
                            elevated: '#151F36',
                            border: 'rgba(255, 255, 255, 0.08)',
                            borderHover: 'rgba(45, 212, 191, 0.25)',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #090D16;
            color: #F1F5F9;
        }
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 22, 38, 0.85) 0%, rgba(11, 17, 30, 0.95) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
        .glass-card {
            background: rgba(15, 22, 38, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(45, 212, 191, 0.25);
            transform: translateY(-1px);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(9, 13, 22, 0.5);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(45, 212, 191, 0.3);
        }
        .glow-dot {
            box-shadow: 0 0 10px #2DD4BF;
        }
        .badge-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body class="min-h-screen antialiased custom-scrollbar flex flex-col bg-[#090D16] selection:bg-teal-500/20 selection:text-teal-200">

    <!-- TOAST NOTIFICATIONS CONTAINER -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col space-y-2 pointer-events-none"></div>

    <!-- AUTHENTICATION OVERLAY -->
    <div id="loginSection" class="fixed inset-0 z-50 flex items-center justify-center bg-[#090D16]/95 backdrop-blur-md p-4">
        <div class="glass-panel w-full max-w-md p-8 rounded-3xl shadow-2xl border border-teal-500/20 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="text-center mb-8 relative">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-teal-500 to-cyan-600 mb-4 shadow-xl shadow-teal-500/20 ring-1 ring-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.602 15.1a2 2 0 00-1.18.27l-1.392.928a1 1 0 00-.33 1.19l.7 1.4a1 1 0 001.27.47l2.8-.933a6 6 0 014.242.235l.95.476a6 6 0 004.242.235l2.4-.8a1 1 0 00.67-.942l.064-1.284a1 1 0 00-.47-.852l-1.332-.888z"></path>
                    </svg>
                </div>
                <h1 class="font-outfit text-3xl font-extrabold text-white tracking-tight">NexLab Clinical OS</h1>
                <p class="text-xs text-slate-400 mt-1.5 font-medium">Enterprise Diagnostic & Partner Lab Platform</p>
                <div class="inline-flex items-center space-x-1.5 mt-3 px-3 py-1 bg-teal-500/10 border border-teal-500/25 rounded-full text-[11px] font-semibold text-teal-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 glow-dot"></span>
                    <span>Tripoli Central Cluster (LYD)</span>
                </div>
            </div>

            <div id="loginError" class="hidden mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 font-medium"></div>

            <form id="adminLoginForm" onsubmit="handleAdminLogin(event)" class="space-y-4 relative">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Admin Email</label>
                    <input type="email" id="loginEmail" value="admin@nexlab.ly" required class="w-full px-4 py-3 bg-slate-900/90 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="loginPassword" value="password" required class="w-full px-4 py-3 bg-slate-900/90 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 text-sm transition-all">
                </div>
                <button type="submit" id="loginSubmitBtn" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 via-teal-600 to-cyan-600 hover:from-teal-400 hover:to-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-teal-500/25 transition-all transform active:scale-[0.98] text-sm">
                    Authenticate to Clinical OS
                </button>
            </form>
            <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                <p class="text-[11px] text-slate-400">Default Demo Credentials: <span class="font-mono text-teal-400 font-semibold">admin@nexlab.ly</span> / <span class="font-mono text-teal-400 font-semibold">password</span></p>
            </div>
        </div>
    </div>

    <!-- MAIN CONSOLE LAYOUT -->
    <div id="dashboardSection" class="hidden min-h-screen flex flex-col">
        <!-- GLOBAL APP HEADER -->
        <header class="glass-panel sticky top-0 z-40 px-6 py-3.5 flex items-center justify-between border-b border-white/5 shadow-xl">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-cyan-600 flex items-center justify-center font-outfit font-black text-white text-lg shadow-lg shadow-teal-500/30 ring-1 ring-white/20">
                        NX
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="font-outfit font-extrabold text-base text-white tracking-tight">NexLab Clinical OS</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-400 border border-teal-500/25">v2.4 Pro</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium flex items-center space-x-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 glow-dot inline-block"></span>
                            <span>Tripoli Health Network • Active Live</span>
                        </p>
                    </div>
                </div>

                <!-- DESKTOP TOP NAV TABS -->
                <nav class="hidden lg:flex items-center space-x-1 bg-slate-900/90 p-1.5 rounded-2xl border border-slate-800/80 shadow-inner">
                    <button onclick="switchTab('overview')" id="tab-overview" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-white bg-teal-600 shadow-md">
                        📊 Dashboard
                    </button>
                    <button onclick="switchTab('bookings')" id="tab-bookings" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-slate-400 hover:text-white">
                        📋 Booking Dispatch
                    </button>
                    <button onclick="switchTab('results')" id="tab-results" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-slate-400 hover:text-white">
                        🔬 Publish Results
                    </button>
                    <button onclick="switchTab('tests')" id="tab-tests" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-slate-400 hover:text-white">
                        🧪 Test Catalog
                    </button>
                    <button onclick="switchTab('labs')" id="tab-labs" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-slate-400 hover:text-white">
                        🏥 Partner Labs
                    </button>
                </nav>
            </div>

            <!-- HEADER ACTIONS & ADMIN BADGE -->
            <div class="flex items-center space-x-3">
                <a href="/docs/api" target="_blank" class="hidden sm:inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 border border-slate-700/60 text-xs font-semibold transition-all">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    <span>API Swagger</span>
                </a>

                <button onclick="refreshCurrentView()" title="Sync Live Data" class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 border border-slate-700/60 text-xs transition-all">
                    <svg id="refreshSpinner" class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>

                <div class="flex items-center space-x-3 pl-3 border-l border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-700 flex items-center justify-center font-bold text-white text-xs">
                        AD
                    </div>
                    <div class="text-right hidden sm:block">
                        <div id="adminUserName" class="text-xs font-bold text-white leading-tight">Administrator</div>
                        <div class="text-[10px] text-teal-400 font-mono font-medium">admin@nexlab.ly</div>
                    </div>
                    <button onclick="handleLogout()" title="Sign Out" class="p-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-xl transition-all border border-rose-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- MOBILE TAB BAR -->
        <div class="lg:hidden flex items-center space-x-1 p-2 bg-slate-900 border-b border-slate-800 overflow-x-auto custom-scrollbar">
            <button onclick="switchTab('overview')" id="m-tab-overview" class="whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 text-white">📊 Overview</button>
            <button onclick="switchTab('bookings')" id="m-tab-bookings" class="whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400">📋 Bookings</button>
            <button onclick="switchTab('results')" id="m-tab-results" class="whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400">🔬 Results</button>
            <button onclick="switchTab('tests')" id="m-tab-tests" class="whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400">🧪 Tests</button>
            <button onclick="switchTab('labs')" id="m-tab-labs" class="whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400">🏥 Labs</button>
        </div>

        <!-- MAIN VIEW CONTENT -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto space-y-8">

            <!-- ================================================================= -->
            <!-- TAB 1: OVERVIEW & KPIS -->
            <!-- ================================================================= -->
            <div id="view-overview" class="space-y-8">
                <!-- WELCOME BANNER -->
                <div class="glass-panel p-6 rounded-3xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-teal-500/20">
                    <div class="space-y-1">
                        <h2 class="font-outfit text-2xl font-black text-white">Clinical Command Center</h2>
                        <p class="text-xs text-slate-400">Real-time surveillance of laboratory booking dispatches, patient flow, and biomarker publication in Tripoli.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="switchTab('results')" class="px-4 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 hover:from-teal-400 hover:to-cyan-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Publish Test Result</span>
                        </button>
                        <button onclick="openTestModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold rounded-xl transition-all">
                            + New Test
                        </button>
                    </div>
                </div>

                <!-- KPI STAT CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- REVENUE -->
                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Gross Platform Volume</p>
                                <h3 id="statRevenue" class="font-outfit text-2xl font-extrabold text-white mt-1.5 font-mono">0.00 LYD</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400">
                                💵
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-2 text-[11px]">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 font-semibold font-mono">Active LYD</span>
                            <span class="text-slate-400">Excludes cancelled orders</span>
                        </div>
                    </div>

                    <!-- TOTAL BOOKINGS -->
                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Diagnostic Bookings</p>
                                <h3 id="statBookings" class="font-outfit text-2xl font-extrabold text-white mt-1.5 font-mono">0</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/25 flex items-center justify-center text-teal-400">
                                📑
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-3 text-[11px]">
                            <span class="text-amber-400 font-semibold"><strong id="statPending" class="font-mono">0</strong> Pending</span>
                            <span class="text-emerald-400 font-semibold"><strong id="statCompleted" class="font-mono">0</strong> Completed</span>
                            <span class="text-rose-400 font-semibold"><strong id="statCancelled" class="font-mono">0</strong> Cancelled</span>
                        </div>
                    </div>

                    <!-- REGISTERED PATIENTS -->
                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Registered Patients</p>
                                <h3 id="statPatients" class="font-outfit text-2xl font-extrabold text-white mt-1.5 font-mono">0</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/25 flex items-center justify-center text-cyan-400">
                                👥
                            </div>
                        </div>
                        <p class="mt-4 text-[11px] text-slate-400 font-medium">Verified Tripoli health IDs</p>
                    </div>

                    <!-- ACCREDITED LABS -->
                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Partner Labs & Catalog</p>
                                <h3 id="statLabs" class="font-outfit text-2xl font-extrabold text-white mt-1.5 font-mono">0 Labs</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-400">
                                🏥
                            </div>
                        </div>
                        <p class="mt-4 text-[11px] text-slate-400 font-medium"><strong id="statTests" class="text-teal-400 font-mono">0</strong> Active diagnostic test panels</p>
                    </div>
                </div>

                <!-- RECENT BOOKINGS STREAM -->
                <div class="glass-panel p-6 rounded-3xl space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-outfit text-lg font-bold text-white">Live Booking Dispatch Queue</h3>
                            <p class="text-xs text-slate-400">Recent diagnostic appointment requests across Tripoli medical centers</p>
                        </div>
                        <button onclick="switchTab('bookings')" class="text-xs font-bold text-teal-400 hover:text-teal-300 flex items-center space-x-1">
                            <span>View Full Dispatch Table</span>
                            <span>→</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] font-mono">
                                <tr>
                                    <th class="py-3.5 px-4 rounded-l-xl">Dispatch Code</th>
                                    <th class="py-3.5 px-4">Patient</th>
                                    <th class="py-3.5 px-4">Diagnostic Panel</th>
                                    <th class="py-3.5 px-4">Laboratory</th>
                                    <th class="py-3.5 px-4">Slot</th>
                                    <th class="py-3.5 px-4">Fee (LYD)</th>
                                    <th class="py-3.5 px-4 rounded-r-xl text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentBookingsTbody" class="divide-y divide-slate-800/60 text-slate-300 font-medium">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 2: BOOKINGS DISPATCH TABLE -->
            <!-- ================================================================= -->
            <div id="view-bookings" class="hidden space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-outfit text-2xl font-bold text-white">All Appointment Bookings</h3>
                        <p class="text-xs text-slate-400">Real-time status management, patient scheduling, and laboratory dispatch</p>
                    </div>

                    <!-- FILTERS & SEARCH -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative">
                            <input type="text" id="bookingSearchInput" oninput="filterBookingsClientSide()" placeholder="Search patient, ID, test..." class="pl-8 pr-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-400 w-48 sm:w-64">
                            <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div class="flex items-center space-x-1 bg-slate-900 p-1 rounded-xl border border-slate-800">
                            <button onclick="loadBookings('')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-700">All</button>
                            <button onclick="loadBookings('pending')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20">Pending</button>
                            <button onclick="loadBookings('completed')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20">Completed</button>
                            <button onclick="loadBookings('cancelled')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500/20">Cancelled</button>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-6 rounded-3xl">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] font-mono">
                                <tr>
                                    <th class="py-3.5 px-4 rounded-l-xl">Dispatch ID</th>
                                    <th class="py-3.5 px-4">Patient Name</th>
                                    <th class="py-3.5 px-4">Diagnostic Test</th>
                                    <th class="py-3.5 px-4">Partner Lab</th>
                                    <th class="py-3.5 px-4">Sample Mode</th>
                                    <th class="py-3.5 px-4">Date & Slot</th>
                                    <th class="py-3.5 px-4">Fee</th>
                                    <th class="py-3.5 px-4 rounded-r-xl text-center">Status Action</th>
                                </tr>
                            </thead>
                            <tbody id="allBookingsTbody" class="divide-y divide-slate-800/60 text-slate-300 font-medium">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 3: PUBLISH MEDICAL RESULTS STUDIO -->
            <!-- ================================================================= -->
            <div id="view-results" class="hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-3 mb-1">
                            <h3 class="font-outfit text-2xl font-bold text-white">Publish Patient Diagnostic Results</h3>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-full flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Live Patient Sync Active</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">Generate digital health records with biomarker measurements and attach PDF lab reports</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- FORM CONTAINER -->
                    <div class="lg:col-span-2 glass-panel p-6 md:p-8 rounded-3xl space-y-6 border border-slate-800/80 shadow-2xl">
                        <!-- PRESETS SELECTOR -->
                        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-900/90 rounded-2xl border border-slate-800">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="text-xs font-bold text-slate-200">Quick Template Presets:</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="loadBiomarkerTemplate('cbc')" class="px-3 py-1.5 text-xs font-bold bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/30 rounded-xl transition-all flex items-center space-x-1">
                                    <span>🩸 CBC Panel</span>
                                </button>
                                <button type="button" onclick="loadBiomarkerTemplate('lipid')" class="px-3 py-1.5 text-xs font-bold bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 rounded-xl transition-all flex items-center space-x-1">
                                    <span>🫀 Lipid Profile</span>
                                </button>
                                <button type="button" onclick="loadBiomarkerTemplate('thyroid')" class="px-3 py-1.5 text-xs font-bold bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-xl transition-all flex items-center space-x-1">
                                    <span>🦋 Thyroid TSH</span>
                                </button>
                                <button type="button" onclick="loadBiomarkerTemplate('glucose')" class="px-3 py-1.5 text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-xl transition-all flex items-center space-x-1">
                                    <span>⚡ Glucose Fasting</span>
                                </button>
                            </div>
                        </div>

                        <form onsubmit="handleUploadResult(event)" class="space-y-5 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold">Result Reference ID</label>
                                    <input type="text" id="resId" value="res_101" required class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white font-mono text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                                        <span>Scheduled Appointment / Patient</span>
                                        <span class="text-[10px] text-teal-400 font-mono font-semibold" id="selectedPatientBadge">Auto-Fill Active</span>
                                    </label>
                                    <select id="resUserId" required onchange="onScheduledBookingSelectChange(this)" class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all font-semibold">
                                        <option value="">-- Loading Scheduled Appointments & Patients... --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold">Diagnostic Test Panel</label>
                                    <select id="resTestId" required onchange="onTestSelectChange(this)" class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all">
                                        <option value="">-- Loading Diagnostic Tests from Database... --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold">Laboratory Partner</label>
                                    <select id="resLabName" required class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all">
                                        <option value="">-- Loading Laboratories from Database... --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold">Sample Test Date</label>
                                    <input type="date" id="resTestDate" required class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-300 mb-1.5 font-bold">Report Issue Date</label>
                                    <input type="date" id="resReportDate" required class="w-full p-3 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-400 transition-all">
                                </div>
                            </div>

                            <!-- DRAG & DROP FILE UPLOADER -->
                            <div>
                                <label class="block text-slate-300 mb-1.5 font-bold">Attach Official PDF Report or Imaging Scan</label>
                                <div id="dropZoneContainer" onclick="document.getElementById('resPdfFile').click()" class="group relative border-2 border-dashed border-slate-700/80 hover:border-teal-400 bg-slate-900/70 hover:bg-slate-900/90 p-5 rounded-2xl text-center transition-all cursor-pointer">
                                    <input type="file" id="resPdfFile" accept=".pdf,.png,.jpg,.jpeg" onchange="handleFileSelected(this)" class="hidden">
                                    
                                    <div id="uploadPlaceholder" class="space-y-2">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-teal-500/20 transition-all">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-200">Click to upload report PDF or drag and drop</p>
                                            <p class="text-[11px] text-slate-500">PDF, PNG, JPG scans up to 15MB</p>
                                        </div>
                                    </div>

                                    <div id="fileSelectedBadge" class="hidden flex items-center justify-between p-3 bg-teal-500/10 border border-teal-500/30 rounded-xl text-left">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-teal-500/20 text-teal-400 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-teal-300 font-mono" id="fileNameDisplay">report.pdf</p>
                                                <p class="text-[10px] text-slate-400" id="fileSizeDisplay">Ready for upload</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="event.stopPropagation(); removeSelectedFile();" class="px-2.5 py-1 text-slate-400 hover:text-rose-400 text-xs font-bold bg-slate-900 rounded-lg border border-slate-700">Remove</button>
                                    </div>
                                </div>
                            </div>

                            <!-- DYNAMIC BIOMARKERS -->
                            <div class="pt-5 border-t border-slate-800/80">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="font-outfit font-bold text-white text-sm">Biomarker Measurements</h4>
                                        <p class="text-[11px] text-slate-400">Structured laboratory parameters analyzed in patient specimen</p>
                                    </div>
                                    <button type="button" onclick="addBiomarkerRow()" class="px-3.5 py-1.5 bg-gradient-to-r from-teal-500/20 to-cyan-500/20 text-teal-300 border border-teal-500/40 text-xs rounded-xl font-bold hover:from-teal-500/30 hover:to-cyan-500/30 transition-all flex items-center space-x-1.5 shadow-lg shadow-teal-500/10">
                                        <span>+ Add Biomarker</span>
                                    </button>
                                </div>
                                <div id="biomarkersContainer" class="space-y-3">
                                    <!-- Populated via JS -->
                                </div>
                            </div>

                            <button type="submit" id="submitResultBtn" class="w-full py-4 bg-gradient-to-r from-teal-500 via-cyan-500 to-blue-600 hover:from-teal-400 hover:to-blue-500 text-white font-extrabold rounded-2xl shadow-xl shadow-teal-500/25 transition-all transform active:scale-[0.99] text-sm flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Publish Medical Test Result & Notify Patient</span>
                            </button>
                        </form>
                    </div>

                    <!-- STANDALONE PDF UPLOADER & SUMMARY SIDEBAR -->
                    <div class="space-y-6">
                        <div class="glass-panel p-6 rounded-3xl space-y-4 border border-slate-800/80 shadow-xl">
                            <div class="flex items-center space-x-2">
                                <div class="p-2 rounded-xl bg-teal-500/10 text-teal-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <h4 class="font-outfit font-bold text-white text-sm">Standalone PDF Storage Upload</h4>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed">Upload a report PDF independently to generate a direct secure storage URL.</p>
                            <form onsubmit="handleDirectPdfUpload(event)" class="space-y-3">
                                <input type="file" id="standalonePdfInput" required accept=".pdf,.png,.jpg,.jpeg" class="w-full p-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-slate-300 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-teal-500/10 file:text-teal-400 hover:file:bg-teal-500/20">
                                <button type="submit" class="w-full py-2.5 bg-slate-800/90 hover:bg-slate-700/90 text-teal-400 border border-teal-500/30 text-xs font-bold rounded-xl transition-all flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload to Storage</span>
                                </button>
                            </form>
                            <div id="uploadedPdfResult" class="hidden p-3 bg-slate-900/90 rounded-xl border border-slate-800 text-[11px] break-all text-teal-300 font-mono"></div>
                        </div>

                        <div class="glass-card p-6 rounded-3xl space-y-4 border border-slate-800/80 shadow-xl">
                            <h4 class="font-outfit font-bold text-white text-sm flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                <span>Clinical Verification Standards</span>
                            </h4>
                            <ul class="text-xs text-slate-400 space-y-2.5 list-none">
                                <li class="flex items-start space-x-2">
                                    <span class="text-teal-400 font-bold">✓</span>
                                    <span>All numerical values must adhere to accredited laboratory reference ranges.</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-teal-400 font-bold">✓</span>
                                    <span>Status flags (<span class="text-emerald-400 font-bold">Normal</span>, <span class="text-rose-400 font-bold">High</span>, <span class="text-amber-400 font-bold">Low</span>, <span class="text-purple-400 font-bold">Borderline</span>) trigger smart health insights on the patient app.</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-teal-400 font-bold">✓</span>
                                    <span>Patients receive immediate instant push notification upon publication.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!-- ================================================================= -->
            <!-- TAB 4: DIAGNOSTIC TESTS CATALOG -->
            <!-- ================================================================= -->
            <div id="view-tests" class="hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-outfit text-2xl font-bold text-white">Diagnostic Tests Catalog</h3>
                        <p class="text-xs text-slate-400">Clinical test packages, biochemical profiles, and fasting requirements</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select id="testCategoryFilter" onchange="filterTestsByCategory(this.value)" class="p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:border-teal-400 focus:outline-none">
                            <option value="">All Categories</option>
                            <option value="General">General</option>
                            <option value="Blood">Blood</option>
                            <option value="Heart">Heart</option>
                            <option value="Thyroid">Thyroid</option>
                            <option value="Energy">Energy</option>
                        </select>
                        <button onclick="openTestModal()" class="px-4 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-500/25 hover:from-teal-400 hover:to-cyan-500 transition-all flex items-center space-x-1.5">
                            <span>+ Add Test / Package</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="testsGrid">
                    <!-- Populated via JS -->
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 5: PARTNER LABORATORIES -->
            <!-- ================================================================= -->
            <div id="view-labs" class="hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-outfit text-2xl font-bold text-white">Partner Laboratory Network</h3>
                        <p class="text-xs text-slate-400">Accredited diagnostic facilities and phlebotomy hubs across Tripoli</p>
                    </div>
                    <button onclick="openLabModal()" class="px-4 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-500/25 hover:from-teal-400 hover:to-cyan-500 transition-all flex items-center space-x-1.5">
                        <span>+ Register Partner Lab</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="labsGrid">
                    <!-- Populated via JS -->
                </div>
            </div>

        </main>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: ADD DIAGNOSTIC TEST -->
    <!-- ================================================================= -->
    <div id="testModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="glass-panel w-full max-w-lg p-6 rounded-3xl border border-teal-500/25 shadow-2xl">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-800">
                <h4 class="font-outfit font-extrabold text-white text-lg">Create Diagnostic Test Panel</h4>
                <button onclick="closeTestModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
            </div>
            <form onsubmit="handleSaveTest(event)" class="space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Test ID (e.g. t10)</label>
                        <input type="text" id="testId" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-teal-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Category</label>
                        <select id="testCategory" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                            <option value="General">General</option>
                            <option value="Blood">Blood</option>
                            <option value="Heart">Heart</option>
                            <option value="Thyroid">Thyroid</option>
                            <option value="Energy">Energy</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Panel Title</label>
                    <input type="text" id="testName" placeholder="e.g. Cardiac Marker Screen" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Subtitle / Summary</label>
                    <input type="text" id="testSubtitle" placeholder="e.g. Troponin I, CK-MB, Myoglobin" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Clinical Description</label>
                    <textarea id="testDescription" rows="2" placeholder="Clinical relevance..." required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Price (LYD)</label>
                        <input type="number" step="0.01" id="testPrice" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-teal-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Turnaround (Hrs)</label>
                        <input type="number" id="testReportsHours" value="24" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Sample Type</label>
                        <input type="text" id="testSampleType" value="Blood" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center space-x-6 pt-2">
                    <label class="flex items-center space-x-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" id="testFasting" class="rounded bg-slate-900 border-slate-700 text-teal-500 focus:ring-teal-400">
                        <span>Fasting Required</span>
                    </label>
                    <label class="flex items-center space-x-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" id="testIsPackage" class="rounded bg-slate-900 border-slate-700 text-teal-500 focus:ring-teal-400">
                        <span>Health Package</span>
                    </label>
                </div>
                <button type="submit" class="w-full mt-4 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 hover:from-teal-400 hover:to-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-teal-500/25 transition-all">
                    Commit Test to Catalog
                </button>
            </form>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: ADD PARTNER LAB -->
    <!-- ================================================================= -->
    <div id="labModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="glass-panel w-full max-w-lg p-6 rounded-3xl border border-teal-500/25 shadow-2xl">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-800">
                <h4 class="font-outfit font-extrabold text-white text-lg">Register Partner Laboratory</h4>
                <button onclick="closeLabModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
            </div>
            <form onsubmit="handleSaveLab(event)" class="space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Lab Identifier (e.g. l3)</label>
                        <input type="text" id="labId" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-teal-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Rating (0 - 5.0)</label>
                        <input type="number" step="0.1" id="labRating" value="4.9" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-teal-400 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Laboratory Name</label>
                    <input type="text" id="labName" placeholder="e.g. Al-Najah Diagnostic Lab" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Address in Tripoli</label>
                    <input type="text" id="labAddress" placeholder="e.g. Gargaresh Road, Tripoli" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Phone Contact</label>
                        <input type="text" id="labPhone" value="+218 21 777 8888" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-teal-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Operating Hours</label>
                        <input type="text" id="labHours" value="08:00 AM - 10:00 PM" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-teal-400 focus:outline-none">
                    </div>
                </div>
                <div class="pt-2">
                    <label class="flex items-center space-x-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" id="labHomeCollection" checked class="rounded bg-slate-900 border-slate-700 text-teal-500 focus:ring-teal-400">
                        <span>Offers Home Phlebotomy / Sample Collection</span>
                    </label>
                </div>
                <button type="submit" class="w-full mt-4 py-3 bg-gradient-to-r from-teal-500 to-cyan-600 hover:from-teal-400 hover:to-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-teal-500/25 transition-all">
                    Register Partner Lab
                </button>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT CLINICAL CONTROLLER -->
    <script>
        const API_BASE = '/api';
        let authToken = localStorage.getItem('nexlab_admin_token') || '';
        let cachedBookings = [];
        let cachedTests = [];
        let activeTab = 'overview';

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('resTestDate');
            const reportInput = document.getElementById('resReportDate');
            if (dateInput) dateInput.value = today;
            if (reportInput) reportInput.value = today;

            loadBiomarkerTemplate('cbc');

            if (authToken) {
                initDashboard();
            }
        });

        // TOAST SYSTEM
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-slate-900/95 border-teal-500 text-teal-200',
                error: 'bg-slate-900/95 border-rose-500 text-rose-200',
                info: 'bg-slate-900/95 border-cyan-500 text-cyan-200',
            };
            const icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');

            toast.className = `pointer-events-auto flex items-center space-x-3 px-4 py-3 rounded-2xl border shadow-2xl backdrop-blur-xl text-xs font-semibold transform transition-all duration-300 translate-y-2 opacity-0 ${colors[type] || colors.success}`;
            toast.innerHTML = `<span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center font-bold text-[11px]">${icon}</span><span>${message}</span>`;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // AUTHENTICATION
        async function handleAdminLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errDiv = document.getElementById('loginError');
            const submitBtn = document.getElementById('loginSubmitBtn');

            errDiv.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Verifying Credentials...';

            try {
                const res = await fetch(`${API_BASE}/admin/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Authentication failed');
                }

                authToken = data.token;
                localStorage.setItem('nexlab_admin_token', authToken);
                document.getElementById('adminUserName').innerText = data.user.name;
                showToast('Welcome back, ' + data.user.name, 'success');
                initDashboard();
            } catch (err) {
                errDiv.innerText = err.message;
                errDiv.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Authenticate to Clinical OS';
            }
        }

        function handleLogout() {
            if (authToken) {
                fetch(`${API_BASE}/admin/logout`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                }).catch(() => {});
            }
            authToken = '';
            localStorage.removeItem('nexlab_admin_token');
            document.getElementById('dashboardSection').classList.add('hidden');
            document.getElementById('loginSection').classList.remove('hidden');
            showToast('Signed out from console', 'info');
        }

        async function initDashboard() {
            document.getElementById('loginSection').classList.add('hidden');
            document.getElementById('dashboardSection').classList.remove('hidden');
            loadStats();
            loadBookings();
            loadTests();
            loadLabs();
        }

        function refreshCurrentView() {
            const spinner = document.getElementById('refreshSpinner');
            spinner.classList.add('animate-spin');
            loadStats();
            loadBookings();
            loadTests();
            loadLabs();
            setTimeout(() => {
                spinner.classList.remove('animate-spin');
                showToast('Data synced with Tripoli Cluster', 'info');
            }, 600);
        }

        function switchTab(tab) {
            activeTab = tab;
            const tabs = ['overview', 'bookings', 'results', 'tests', 'labs'];
            tabs.forEach(t => {
                const view = document.getElementById(`view-${t}`);
                const btn = document.getElementById(`tab-${t}`);
                const mBtn = document.getElementById(`m-tab-${t}`);

                if (view) {
                    if (t === tab) {
                        view.classList.remove('hidden');
                    } else {
                        view.classList.add('hidden');
                    }
                }

                if (btn) {
                    if (t === tab) {
                        btn.className = 'px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-white bg-teal-600 shadow-md';
                    } else {
                        btn.className = 'px-3.5 py-2 text-xs font-semibold rounded-xl transition-all text-slate-400 hover:text-white';
                    }
                }

                if (mBtn) {
                    if (t === tab) {
                        mBtn.className = 'whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 text-white';
                    } else {
                        mBtn.className = 'whitespace-nowrap px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400';
                    }
                }
            });
        }

        // STATS LOADER
        async function loadStats() {
            try {
                const res = await fetch(`${API_BASE}/admin/dashboard/stats`, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.status === 401 || res.status === 403) return handleLogout();

                const data = await res.json();
                const s = data.stats;

                document.getElementById('statRevenue').innerText = `${Number(s.total_revenue_lyd).toFixed(2)} LYD`;
                document.getElementById('statBookings').innerText = s.total_bookings;
                document.getElementById('statPending').innerText = s.pending_bookings;
                document.getElementById('statCompleted').innerText = s.completed_bookings;
                document.getElementById('statCancelled').innerText = s.cancelled_bookings;
                document.getElementById('statPatients').innerText = s.total_patients;
                document.getElementById('statLabs').innerText = `${s.total_labs} Labs`;
                document.getElementById('statTests').innerText = s.total_tests;

                renderRecentBookings(data.recent_bookings || []);
            } catch (e) {
                console.error(e);
            }
        }

        function getStatusBadge(status) {
            if (status === 'completed') {
                return `<span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span><span>Completed</span></span>`;
            } else if (status === 'cancelled') {
                return `<span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span><span>Cancelled</span></span>`;
            }
            return `<span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 badge-pulse"></span><span>Pending</span></span>`;
        }

        function renderRecentBookings(bookings) {
            const tbody = document.getElementById('recentBookingsTbody');
            if (!bookings.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-slate-500">No active bookings recorded in dispatch queue.</td></tr>`;
                return;
            }
            tbody.innerHTML = bookings.map(b => `
                <tr class="hover:bg-slate-800/40 transition-colors">
                    <td class="py-3.5 px-4 font-mono font-bold text-teal-400">${b.id}</td>
                    <td class="py-3.5 px-4 font-semibold text-white">${b.patient_name}</td>
                    <td class="py-3.5 px-4 text-slate-300">${b.diagnostic_test ? b.diagnostic_test.name : 'N/A'}</td>
                    <td class="py-3.5 px-4 text-slate-400">${b.partner_lab ? b.partner_lab.name : 'N/A'}</td>
                    <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">${b.date} • ${b.time_slot}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-emerald-400">${Number(b.total_amount).toFixed(2)} LYD</td>
                    <td class="py-3.5 px-4 text-right">${getStatusBadge(b.status)}</td>
                </tr>
            `).join('');
        }

        // BOOKINGS DISPATCH
        async function loadBookings(status = '') {
            try {
                const url = status ? `${API_BASE}/admin/bookings?status=${status}` : `${API_BASE}/admin/bookings`;
                const res = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const data = await res.json();
                cachedBookings = data.data || [];
                renderAllBookings(cachedBookings);
                await populateResultAppointmentDropdown();
            } catch (e) {
                console.error(e);
            }
        }

        // POPULATE SCHEDULED APPOINTMENT & PATIENT DROPDOWN (DYNAMIC SYNC)
        async function populateResultAppointmentDropdown() {
            const selectEl = document.getElementById('resUserId');
            if (!selectEl) return;

            let patients = [];
            try {
                const resP = await fetch(`${API_BASE}/admin/patients`, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (resP.ok) {
                    const dataP = await resP.json();
                    patients = dataP.data || [];
                }
            } catch (err) {
                console.error('Failed to load patients for dropdown', err);
            }

            let html = '<option value="">-- Select Scheduled Appointment / Patient --</option>';

            if (cachedBookings && cachedBookings.length > 0) {
                html += '<optgroup label="📋 Active Scheduled Appointments">';
                cachedBookings.forEach(b => {
                    const labName = b.partner_lab ? b.partner_lab.name : 'Tripoli Central Diagnostic Lab';
                    const testId = b.diagnostic_test_id || 't1';
                    const testName = b.diagnostic_test ? b.diagnostic_test.name : 'Diagnostic Test';
                    const patientName = b.patient_name || (b.user ? b.user.name : `Patient #${b.user_id}`);
                    const dateStr = b.date || new Date().toISOString().substring(0, 10);
                    const code = b.id || `NX-${Math.floor(10000 + Math.random() * 90000)}`;

                    html += `<option value="${b.user_id}" data-test-id="${testId}" data-lab="${labName}" data-date="${dateStr}" data-code="${code}">
                        ${patientName} — ${code} (${testName} @ ${labName})
                    </option>`;
                });
                html += '</optgroup>';
            }

            if (patients && patients.length > 0) {
                html += '<optgroup label="👤 All Registered Patients">';
                patients.forEach(p => {
                    const code = `NX-${p.id}${Math.floor(100 + Math.random() * 900)}`;
                    html += `<option value="${p.id}" data-test-id="t1" data-lab="Tripoli Central Diagnostic Lab" data-date="${new Date().toISOString().substring(0, 10)}" data-code="${code}">
                        ${p.name} (User #${p.id} — ${p.email})
                    </option>`;
                });
                html += '</optgroup>';
            }

            selectEl.innerHTML = html;
        }


        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) {
                alert(message);
                return;
            }
            const toast = document.createElement('div');
            const bgClass = type === 'success'
                ? 'bg-emerald-500/90 border-emerald-400 text-white'
                : (type === 'error' ? 'bg-rose-500/90 border-rose-400 text-white' : 'bg-teal-500/90 border-teal-400 text-white');
            toast.className = `px-4 py-3 rounded-xl border shadow-xl text-xs font-semibold backdrop-blur-md transition-all pointer-events-auto flex items-center justify-between space-x-3 ${bgClass}`;
            toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="text-white opacity-70 hover:opacity-100 font-bold ml-2">✕</button>`;
            container.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 4000);
        }

        function renderAllBookings(bookings) {
            const tbody = document.getElementById('allBookingsTbody');
            if (!bookings.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-500">No matching appointment records found.</td></tr>`;
                return;
            }

            tbody.innerHTML = bookings.map(b => `
                <tr class="hover:bg-slate-800/40 transition-colors">
                    <td class="py-3.5 px-4 font-mono font-bold text-teal-400">${b.id}</td>
                    <td class="py-3.5 px-4 font-semibold text-white">${b.patient_name}</td>
                    <td class="py-3.5 px-4 text-slate-300">${b.diagnostic_test ? b.diagnostic_test.name : 'N/A'}</td>
                    <td class="py-3.5 px-4 text-slate-400">${b.partner_lab ? b.partner_lab.name : 'N/A'}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold ${b.is_home_collection ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20' : 'bg-slate-800 text-slate-400'}">
                            ${b.is_home_collection ? '🏠 Home Phlebotomy' : '🏥 Lab Visit'}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">${b.date} (${b.time_slot})</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-white">${Number(b.total_amount).toFixed(2)} LYD</td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <select onchange="updateBookingStatus('${b.id}', this.value)" class="bg-slate-900 border border-slate-700 text-xs rounded-xl px-2.5 py-1 font-semibold focus:outline-none focus:border-teal-400 ${b.status === 'completed' ? 'text-emerald-400' : (b.status === 'cancelled' ? 'text-rose-400' : 'text-amber-400')}">
                                <option value="pending" ${b.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="completed" ${b.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${b.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                            ${b.status === 'completed' ? `
                                <button onclick="deleteBooking('${b.id}')" title="Delete Completed Booking" class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-lg transition-all border border-rose-500/20 text-xs">
                                    🗑️
                                </button>
                            ` : `
                                <button disabled title="Only completed bookings can be deleted" class="p-1.5 text-slate-600 opacity-25 cursor-not-allowed text-xs">
                                    🗑️
                                </button>
                            `}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filterBookingsClientSide() {
            const query = (document.getElementById('bookingSearchInput').value || '').toLowerCase();
            const filtered = cachedBookings.filter(b => 
                b.id.toLowerCase().includes(query) ||
                b.patient_name.toLowerCase().includes(query) ||
                (b.diagnostic_test && b.diagnostic_test.name.toLowerCase().includes(query)) ||
                (b.partner_lab && b.partner_lab.name.toLowerCase().includes(query))
            );
            renderAllBookings(filtered);
        }

        async function updateBookingStatus(id, newStatus) {
            try {
                const res = await fetch(`${API_BASE}/admin/bookings/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (res.ok) {
                    showToast(`Booking ${id} status updated to ${newStatus}`, 'success');
                    loadStats();
                    loadBookings();
                } else {
                    const err = await res.json();
                    showToast(err.message || 'Failed to update booking status', 'error');
                }
            } catch (e) {
                showToast('Network error updating status', 'error');
            }
        }

        async function deleteBooking(id) {
            if (!confirm(`Are you sure you want to delete completed booking ${id}?`)) return;
            try {
                const res = await fetch(`${API_BASE}/admin/bookings/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    showToast(`Booking ${id} deleted successfully`, 'success');
                    loadStats();
                    loadBookings();
                } else {
                    showToast(data.message || 'Failed to delete booking', 'error');
                }
            } catch (e) {
                showToast('Network error deleting booking', 'error');
            }
        }

        // BIOMARKER TEMPLATES & DYNAMIC ROWS
        const biomarkerTemplates = {
            cbc: [
                { name: 'Hemoglobin', value: '14.5', unit: 'g/dL', ref: '13.5 - 17.5', status: 'Normal' },
                { name: 'Red Blood Cells (RBC)', value: '4.9', unit: 'm/uL', ref: '4.3 - 5.9', status: 'Normal' },
                { name: 'White Blood Cells (WBC)', value: '7.2', unit: 'x10^3/uL', ref: '4.5 - 11.0', status: 'Normal' },
                { name: 'Platelets', value: '260', unit: 'x10^3/uL', ref: '150 - 450', status: 'Normal' },
            ],
            lipid: [
                { name: 'Total Cholesterol', value: '185', unit: 'mg/dL', ref: '< 200', status: 'Normal' },
                { name: 'HDL Cholesterol', value: '55', unit: 'mg/dL', ref: '> 40', status: 'Normal' },
                { name: 'LDL Cholesterol', value: '110', unit: 'mg/dL', ref: '< 130', status: 'Normal' },
                { name: 'Triglycerides', value: '130', unit: 'mg/dL', ref: '< 150', status: 'Normal' },
            ],
            thyroid: [
                { name: 'TSH (Thyroid Stimulating Hormone)', value: '2.1', unit: 'uIU/mL', ref: '0.4 - 4.0', status: 'Normal' },
                { name: 'Free T4', value: '1.2', unit: 'ng/dL', ref: '0.8 - 1.8', status: 'Normal' },
                { name: 'Free T3', value: '3.1', unit: 'pg/mL', ref: '2.3 - 4.2', status: 'Normal' },
            ],
            glucose: [
                { name: 'Fasting Plasma Glucose', value: '92', unit: 'mg/dL', ref: '70 - 99', status: 'Normal' },
                { name: 'HbA1c Glycated Hemoglobin', value: '5.2', unit: '%', ref: '< 5.7', status: 'Normal' },
            ]
        };

        function loadBiomarkerTemplate(key) {
            const container = document.getElementById('biomarkersContainer');
            container.innerHTML = '';
            const list = biomarkerTemplates[key] || biomarkerTemplates.cbc;
            list.forEach(bm => addBiomarkerRow(bm.name, bm.value, bm.unit, bm.ref, bm.status));
        }

                function onScheduledBookingSelectChange(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            if (!opt || !opt.value) return;

            const userId = opt.value;
            const testId = opt.getAttribute('data-test-id') || 't9';
            const labName = opt.getAttribute('data-lab') || 'Tripoli Central Diagnostic Lab';
            const dateStr = opt.getAttribute('data-date') || new Date().toISOString().substring(0, 10);
            const code = opt.getAttribute('data-code') || 'res_101';

            // 1. Auto-fill Result Reference ID
            document.getElementById('resId').value = `res_${code}`;

            // 2. Auto-select Diagnostic Test Panel
            const testSelect = document.getElementById('resTestId');
            if (testSelect) {
                testSelect.value = testId;
                onTestSelectChange(testSelect);
            }

            // 3. Auto-select Partner Laboratory
            const labSelect = document.getElementById('resLabName');
            if (labSelect) {
                labSelect.value = labName;
            }

            // 4. Auto-fill Sample & Report Dates
            document.getElementById('resTestDate').value = dateStr;
            document.getElementById('resReportDate').value = new Date().toISOString().substring(0, 10);

            // 5. Update Badge Status
            const badge = document.getElementById('selectedPatientBadge');
            if (badge) badge.innerText = `Auto-filled for User #${userId}`;
        }

        function onPatientSelectChange(sel) {
            const badge = document.getElementById('selectedPatientBadge');
            if (badge) badge.innerText = `ID #${sel.value}`;
        }

        function onTestSelectChange(sel) {
            const val = sel.value;
            if (val === 't1') loadBiomarkerTemplate('cbc');
            else if (val === 't2') loadBiomarkerTemplate('lipid');
            else if (val === 't3') loadBiomarkerTemplate('thyroid');
            else if (val === 't5') loadBiomarkerTemplate('glucose');
            else loadBiomarkerTemplate('cbc');
        }

        function handleFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('fileSelectedBadge').classList.remove('hidden');
                document.getElementById('fileNameDisplay').innerText = file.name;
                document.getElementById('fileSizeDisplay').innerText = `${(file.size / 1024).toFixed(1)} KB — Ready to attach`;
            }
        }

        function removeSelectedFile() {
            const input = document.getElementById('resPdfFile');
            input.value = '';
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
            document.getElementById('fileSelectedBadge').classList.add('hidden');
        }

        function addBiomarkerRow(name = '', value = '', unit = '', ref = '', status = 'Normal') {
            const container = document.getElementById('biomarkersContainer');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2.5 biomarker-row items-center p-3 bg-slate-900/90 border border-slate-800 rounded-2xl hover:border-slate-700 transition-all';
            
            const statusColors = {
                'Normal': 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
                'High': 'text-rose-400 bg-rose-500/10 border-rose-500/30',
                'Low': 'text-amber-400 bg-amber-500/10 border-amber-500/30',
                'Borderline': 'text-purple-400 bg-purple-500/10 border-purple-500/30'
            };

            row.innerHTML = `
                <div class="col-span-12 sm:col-span-3">
                    <input type="text" placeholder="Marker Name (e.g. Hemoglobin)" value="${name}" required class="w-full p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-semibold focus:border-teal-400 focus:outline-none">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <input type="text" placeholder="Value" value="${value}" required class="w-full p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-teal-300 font-mono text-xs font-bold focus:border-teal-400 focus:outline-none">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <input type="text" placeholder="Unit (g/dL)" value="${unit}" required class="w-full p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-300 text-xs focus:border-teal-400 focus:outline-none">
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <input type="text" placeholder="Ref (13.5-17.5)" value="${ref}" required class="w-full p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-400 font-mono text-xs focus:border-teal-400 focus:outline-none">
                </div>
                <div class="col-span-5 sm:col-span-2">
                    <select onchange="updateRowStatusColor(this)" class="w-full p-2 bg-slate-950 border border-slate-800 rounded-xl text-xs font-bold focus:border-teal-400 focus:outline-none ${statusColors[status] || statusColors['Normal']}">
                        <option value="Normal" ${status === 'Normal' ? 'selected' : ''} class="bg-slate-900 text-emerald-400">Normal</option>
                        <option value="Low" ${status === 'Low' ? 'selected' : ''} class="bg-slate-900 text-amber-400">Low</option>
                        <option value="High" ${status === 'High' ? 'selected' : ''} class="bg-slate-900 text-rose-400">High</option>
                        <option value="Borderline" ${status === 'Borderline' ? 'selected' : ''} class="bg-slate-900 text-purple-400">Borderline</option>
                    </select>
                </div>
                <div class="col-span-1 text-center">
                    <button type="button" onclick="this.closest('.biomarker-row').remove()" class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 hover:text-rose-300 font-bold transition-all flex items-center justify-center mx-auto text-xs">✕</button>
                </div>
            `;
            container.appendChild(row);
        }

        function updateRowStatusColor(sel) {
            const colors = {
                'Normal': 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
                'High': 'text-rose-400 bg-rose-500/10 border-rose-500/30',
                'Low': 'text-amber-400 bg-amber-500/10 border-amber-500/30',
                'Borderline': 'text-purple-400 bg-purple-500/10 border-purple-500/30'
            };
            sel.className = `w-full p-2 bg-slate-950 border rounded-xl text-xs font-bold focus:border-teal-400 focus:outline-none ${colors[sel.value] || colors['Normal']}`;
        }

        // PUBLISH RESULTS
        async function handleUploadResult(e) {
            e.preventDefault();
            const btn = document.getElementById('submitResultBtn');
            btn.disabled = true;
            btn.innerText = 'Publishing to Patient Portal...';

            const formData = new FormData();
            formData.append('id', document.getElementById('resId').value);
            formData.append('user_id', document.getElementById('resUserId').value);
            formData.append('diagnostic_test_id', document.getElementById('resTestId').value);
            formData.append('lab_name', document.getElementById('resLabName').value);
            formData.append('test_date', document.getElementById('resTestDate').value);
            formData.append('report_date', document.getElementById('resReportDate').value);

            const fileInput = document.getElementById('resPdfFile');
            if (fileInput.files.length > 0) {
                formData.append('pdf_file', fileInput.files[0]);
            }

            const rows = document.querySelectorAll('.biomarker-row');
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input, select');
                formData.append(`biomarkers[${index}][name]`, inputs[0].value);
                formData.append(`biomarkers[${index}][value]`, inputs[1].value);
                formData.append(`biomarkers[${index}][unit]`, inputs[2].value);
                formData.append(`biomarkers[${index}][reference_range]`, inputs[3].value);
                formData.append(`biomarkers[${index}][status]`, inputs[4].value);
            });

            try {
                const res = await fetch(`${API_BASE}/admin/results`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await res.json();
                if (res.ok) {
                    showToast('Patient diagnostic result published successfully!', 'success');
                    document.getElementById('resId').value = 'res_' + Math.floor(100 + Math.random() * 900);
                    switchTab('overview');
                } else {
                    showToast(data.message || 'Failed to publish result', 'error');
                }
            } catch (err) {
                showToast('Network error publishing results', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = '🚀 Publish Medical Test Result & Notify Patient';
            }
        }

        // STANDALONE PDF UPLOAD
        async function handleDirectPdfUpload(e) {
            e.preventDefault();
            const fileInput = document.getElementById('standalonePdfInput');
            if (!fileInput.files.length) return;

            const formData = new FormData();
            formData.append('pdf_file', fileInput.files[0]);

            try {
                const res = await fetch(`${API_BASE}/admin/results/upload-pdf`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (res.ok) {
                    const resultDiv = document.getElementById('uploadedPdfResult');
                    resultDiv.innerHTML = `<strong>Uploaded PDF URI:</strong><br><a href="${data.pdf_url}" target="_blank" class="underline text-teal-400">${data.pdf_url}</a>`;
                    resultDiv.classList.remove('hidden');
                    showToast('Report PDF stored successfully', 'success');
                } else {
                    showToast(data.message || 'Upload failed', 'error');
                }
            } catch (e) {
                showToast('Network error on file upload', 'error');
            }
        }

        // DIAGNOSTIC TESTS (FETCHED FROM DB)
        async function loadTests() {
            try {
                const res = await fetch(`${API_BASE}/tests`);
                const data = await res.json();
                cachedTests = data.data || [];
                renderTests(cachedTests);

                // Populate Diagnostic Test Select in Publish Results Form
                const testSelect = document.getElementById('resTestId');
                if (testSelect && cachedTests.length > 0) {
                    testSelect.innerHTML = cachedTests.map((t, idx) => `
                        <option value="${t.id}" ${idx === 0 ? 'selected' : ''}>${t.name} (${t.id})</option>
                    `).join('');
                }
            } catch (e) {
                console.error(e);
            }
        }

        function filterTestsByCategory(cat) {
            if (!cat) {
                renderTests(cachedTests);
            } else {
                renderTests(cachedTests.filter(t => t.category === cat));
            }
        }

        function renderTests(tests) {
            const grid = document.getElementById('testsGrid');
            if (!tests.length) {
                grid.innerHTML = `<div class="col-span-3 text-center py-12 text-slate-500">No diagnostic tests matching criteria.</div>`;
                return;
            }

            grid.innerHTML = tests.map(t => `
                <div class="glass-card p-6 rounded-3xl flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/25">${t.category}</span>
                            <span class="font-outfit text-xl font-extrabold text-emerald-400 font-mono">${Number(t.price).toFixed(2)} LYD</span>
                        </div>
                        <h4 class="font-outfit font-bold text-white text-base leading-snug">${t.name}</h4>
                        <p class="text-xs text-slate-400 mt-1">${t.subtitle}</p>
                        <p class="text-xs text-slate-500 mt-2.5 line-clamp-2">${t.description}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-800 space-y-3">
                        <div class="flex flex-wrap gap-1.5 text-[10px]">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono">⏱️ ${t.reports_in_hours}h TAT</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">🧪 ${t.sample_type}</span>
                            ${t.fasting_required ? '<span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20">Fasting</span>' : ''}
                            ${t.is_package ? '<span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Package</span>' : ''}
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1">
                            <span class="text-slate-500 font-mono text-[11px]">ID: ${t.id}</span>
                            <button onclick="deleteTest('${t.id}')" class="text-rose-400 hover:text-rose-300 font-semibold transition-colors">Delete Panel</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function openTestModal() { document.getElementById('testModal').classList.remove('hidden'); }
        function closeTestModal() { document.getElementById('testModal').classList.add('hidden'); }

        async function handleSaveTest(e) {
            e.preventDefault();
            const body = {
                id: document.getElementById('testId').value,
                name: document.getElementById('testName').value,
                subtitle: document.getElementById('testSubtitle').value,
                description: document.getElementById('testDescription').value,
                category: document.getElementById('testCategory').value,
                price: parseFloat(document.getElementById('testPrice').value),
                reports_in_hours: parseInt(document.getElementById('testReportsHours').value),
                sample_type: document.getElementById('testSampleType').value,
                fasting_required: document.getElementById('testFasting').checked,
                is_package: document.getElementById('testIsPackage').checked,
            };

            try {
                const res = await fetch(`${API_BASE}/admin/tests`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });

                if (res.ok) {
                    closeTestModal();
                    showToast('Diagnostic test added to catalog', 'success');
                    loadTests();
                    loadStats();
                } else {
                    const err = await res.json();
                    showToast(err.message || 'Failed to create test', 'error');
                }
            } catch (err) {
                showToast('Network error creating test', 'error');
            }
        }

        async function deleteTest(id) {
            if (!confirm(`Are you sure you want to remove test panel "${id}"?`)) return;
            try {
                const res = await fetch(`${API_BASE}/admin/tests/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    showToast('Test panel deleted', 'success');
                    loadTests();
                    loadStats();
                } else {
                    showToast('Failed to delete test', 'error');
                }
            } catch (e) {
                showToast('Network error deleting test', 'error');
            }
        }

        // PARTNER LABS (FETCHED FROM DB)
        async function loadLabs() {
            try {
                const res = await fetch(`${API_BASE}/labs`);
                const data = await res.json();
                const labs = data.data || [];
                const grid = document.getElementById('labsGrid');

                // Populate Lab Partner Select in Publish Results Form
                const labSelect = document.getElementById('resLabName');
                if (labSelect && labs.length > 0) {
                    labSelect.innerHTML = labs.map((l, idx) => `
                        <option value="${l.name}" ${idx === 0 ? 'selected' : ''}>${l.name} (${l.id})</option>
                    `).join('');
                }

                if (!labs.length) {
                    grid.innerHTML = `<div class="col-span-2 text-center py-12 text-slate-500">No partner labs registered.</div>`;
                    return;
                }

                grid.innerHTML = data.data.map(l => `
                    <div class="glass-card p-6 rounded-3xl space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-outfit font-bold text-white text-lg">${l.name}</h4>
                                <p class="text-xs text-slate-400 mt-1 flex items-center space-x-1">
                                    <span>📍</span>
                                    <span>${l.address}</span>
                                </p>
                            </div>
                            <div class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-extrabold rounded-xl font-mono">
                                ⭐ ${l.rating} (${l.reviews_count})
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-400 bg-slate-900/60 p-3 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 block text-[10px] font-semibold uppercase">Contact</span>
                                <span class="font-mono text-slate-300">${l.phone}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] font-semibold uppercase">Hours</span>
                                <span class="text-slate-300">${l.hours}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold ${l.has_home_collection ? 'bg-teal-500/10 text-teal-300 border border-teal-500/25' : 'bg-slate-800 text-slate-400'}">
                                ${l.has_home_collection ? '✓ Offers Home Collection' : '✕ Lab Visit Only'}
                            </span>
                            <button onclick="deleteLab('${l.id}')" class="text-rose-400 hover:text-rose-300 font-semibold transition-colors">
                                Deregister Lab
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (e) {
                console.error(e);
            }
        }

        function openLabModal() { document.getElementById('labModal').classList.remove('hidden'); }
        function closeLabModal() { document.getElementById('labModal').classList.add('hidden'); }

        async function handleSaveLab(event) {
            event.preventDefault();
            const body = {
                id: document.getElementById('labId').value,
                name: document.getElementById('labName').value,
                rating: parseFloat(document.getElementById('labRating').value),
                reviews_count: 50,
                address: document.getElementById('labAddress').value,
                phone: document.getElementById('labPhone').value,
                hours: document.getElementById('labHours').value,
                has_home_collection: document.getElementById('labHomeCollection').checked,
            };

            try {
                const res = await fetch(`${API_BASE}/admin/labs`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });

                if (res.ok) {
                    closeLabModal();
                    showToast('Partner laboratory registered successfully', 'success');
                    loadLabs();
                    loadStats();
                } else {
                    const err = await res.json();
                    showToast(err.message || 'Failed to create partner lab', 'error');
                }
            } catch (err) {
                showToast('Network error registering lab', 'error');
            }
        }

        async function deleteLab(id) {
            if (!confirm(`Are you sure you want to remove laboratory "${id}" from the network?`)) return;
            try {
                const res = await fetch(`${API_BASE}/admin/labs/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    showToast('Partner lab removed from network', 'success');
                    loadLabs();
                    loadStats();
                } else {
                    showToast('Failed to delete lab', 'error');
                }
            } catch (e) {
                showToast('Network error deleting lab', 'error');
            }
        }
    </script>
</body>
</html>
