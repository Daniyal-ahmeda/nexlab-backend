<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexLab Admin Dashboard — Medical Diagnostic Platform</title>
    <!-- Google Fonts & Tailwind CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#F0F9FF',
                            500: '#0284C7',
                            600: '#0284C7',
                            700: '#0369A1',
                            accent: '#06B6D4',
                            emerald: '#10B981',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0B0F19;
            color: #F3F4F6;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-nav {
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="min-h-screen antialiased custom-scrollbar">

    <!-- LOGIN OVERLAY -->
    <div id="loginSection" class="fixed inset-0 z-50 flex items-center justify-center bg-[#0B0F19] bg-opacity-95 p-4">
        <div class="glass-card w-full max-w-md p-8 rounded-2xl shadow-2xl border border-cyan-500/20">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 mb-4 shadow-lg shadow-cyan-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.602 15.1a2 2 0 00-1.18.27l-1.392.928a1 1 0 00-.33 1.19l.7 1.4a1 1 0 001.27.47l2.8-.933a6 6 0 014.242.235l.95.476a6 6 0 004.242.235l2.4-.8a1 1 0 00.67-.942l.064-1.284a1 1 0 00-.47-.852l-1.332-.888z"></path>
                    </svg>
                </div>
                <h1 class="font-outfit text-3xl font-extrabold text-white tracking-tight">NexLab Admin</h1>
                <p class="text-sm text-gray-400 mt-1">Laboratory & Medical Booking Platform</p>
                <div class="inline-block mt-3 px-3 py-1 bg-cyan-500/10 border border-cyan-500/30 rounded-full text-xs font-semibold text-cyan-400">
                    Tripoli, Libya (LYD)
                </div>
            </div>

            <div id="loginError" class="hidden mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-xs text-red-400"></div>

            <form id="adminLoginForm" onsubmit="handleAdminLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-300 uppercase tracking-wider mb-2">Admin Email</label>
                    <input type="email" id="loginEmail" value="admin@nexlab.ly" required class="w-full px-4 py-3 bg-gray-900/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" id="loginPassword" value="password" required class="w-full px-4 py-3 bg-gray-900/80 border border-gray-700/80 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/25 transition-all transform active:scale-95">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN DASHBOARD LAYOUT -->
    <div id="dashboardSection" class="hidden min-h-screen flex flex-col">
        <!-- TOP NAV -->
        <header class="glass-nav sticky top-0 z-40 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center font-outfit font-extrabold text-white text-lg shadow-md shadow-cyan-500/30">
                    NX
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-lg text-white leading-tight">NexLab Admin Dashboard</h2>
                    <p class="text-xs text-cyan-400 font-medium">Tripoli Healthcare Network</p>
                </div>
            </div>

            <!-- TAB CONTROLS -->
            <nav class="hidden md:flex items-center space-x-1 bg-gray-900/90 p-1.5 rounded-xl border border-gray-800">
                <button onclick="switchTab('overview')" id="tab-overview" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all text-white bg-cyan-600">
                    📊 Overview
                </button>
                <button onclick="switchTab('bookings')" id="tab-bookings" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all text-gray-400 hover:text-white">
                    📋 Bookings
                </button>
                <button onclick="switchTab('results')" id="tab-results" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all text-gray-400 hover:text-white">
                    📑 Upload Results
                </button>
                <button onclick="switchTab('tests')" id="tab-tests" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all text-gray-400 hover:text-white">
                    🧪 Tests
                </button>
                <button onclick="switchTab('labs')" id="tab-labs" class="px-4 py-2 text-xs font-semibold rounded-lg transition-all text-gray-400 hover:text-white">
                    🏥 Labs
                </button>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <div id="adminUserName" class="text-xs font-bold text-white">Administrator</div>
                    <div class="text-[10px] text-gray-400">admin@nexlab.ly</div>
                </div>
                <button onclick="handleLogout()" class="px-3.5 py-2 text-xs font-medium bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 rounded-xl transition-all">
                    Logout
                </button>
            </div>
        </header>

        <!-- DASHBOARD BODY -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto space-y-8">

            <!-- OVERVIEW TAB -->
            <div id="view-overview" class="space-y-8">
                <!-- STAT CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Revenue</p>
                                <h3 id="statRevenue" class="font-outfit text-2xl font-bold text-white mt-1">0.00 LYD</h3>
                            </div>
                            <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-400 border border-emerald-500/20">
                                💵
                            </div>
                        </div>
                        <p class="text-[11px] text-emerald-400 mt-3 font-medium">All completed & pending LYD sales</p>
                    </div>

                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Bookings</p>
                                <h3 id="statBookings" class="font-outfit text-2xl font-bold text-white mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-cyan-500/10 rounded-xl text-cyan-400 border border-cyan-500/20">
                                📑
                            </div>
                        </div>
                        <div class="flex space-x-3 mt-3 text-[11px]">
                            <span class="text-yellow-400"><strong id="statPending">0</strong> Pending</span>
                            <span class="text-emerald-400"><strong id="statCompleted">0</strong> Done</span>
                        </div>
                    </div>

                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Registered Patients</p>
                                <h3 id="statPatients" class="font-outfit text-2xl font-bold text-white mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-blue-500/10 rounded-xl text-blue-400 border border-blue-500/20">
                                👥
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-3">Active Tripoli platform users</p>
                    </div>

                    <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Accredited Labs</p>
                                <h3 id="statLabs" class="font-outfit text-2xl font-bold text-white mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-purple-500/10 rounded-xl text-purple-400 border border-purple-500/20">
                                🏥
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-3"><strong id="statTests">0</strong> Diagnostic Tests Available</p>
                    </div>
                </div>

                <!-- RECENT BOOKINGS TABLE -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-outfit text-lg font-bold text-white">Recent Lab Bookings</h3>
                            <p class="text-xs text-gray-400">Latest appointment requests from patients</p>
                        </div>
                        <button onclick="switchTab('bookings')" class="text-xs font-semibold text-cyan-400 hover:text-cyan-300">View All →</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4 rounded-l-lg">Booking ID</th>
                                    <th class="py-3 px-4">Patient</th>
                                    <th class="py-3 px-4">Test</th>
                                    <th class="py-3 px-4">Partner Lab</th>
                                    <th class="py-3 px-4">Date & Slot</th>
                                    <th class="py-3 px-4">Total (LYD)</th>
                                    <th class="py-3 px-4 rounded-r-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentBookingsTbody" class="divide-y divide-gray-800/60 text-gray-300 font-medium">
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BOOKINGS TAB -->
            <div id="view-bookings" class="hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-outfit text-xl font-bold text-white">All Patient Bookings</h3>
                        <p class="text-xs text-gray-400">Filter and manage diagnostic appointments</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="loadBookings('')" class="px-3 py-1.5 text-xs rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">All</button>
                        <button onclick="loadBookings('pending')" class="px-3 py-1.5 text-xs rounded-lg bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</button>
                        <button onclick="loadBookings('completed')" class="px-3 py-1.5 text-xs rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Completed</button>
                        <button onclick="loadBookings('cancelled')" class="px-3 py-1.5 text-xs rounded-lg bg-red-500/20 text-red-400 border border-red-500/30">Cancelled</button>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-900/60 text-gray-400 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Patient Name</th>
                                    <th class="py-3 px-4">Test</th>
                                    <th class="py-3 px-4">Lab</th>
                                    <th class="py-3 px-4">Type</th>
                                    <th class="py-3 px-4">Date & Time</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Status Action</th>
                                </tr>
                            </thead>
                            <tbody id="allBookingsTbody" class="divide-y divide-gray-800/60 text-gray-300 font-medium">
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- UPLOAD RESULTS TAB -->
            <div id="view-results" class="hidden space-y-6">
                <div>
                    <h3 class="font-outfit text-xl font-bold text-white">Upload Patient Test Result</h3>
                    <p class="text-xs text-gray-400">Publish lab report PDF and attach biomarker analysis to patient record</p>
                </div>

                <div class="glass-card p-6 rounded-2xl max-w-3xl">
                    <form onsubmit="handleUploadResult(event)" class="space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Result ID</label>
                                <input type="text" id="resId" value="res_101" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white font-mono">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Patient User ID</label>
                                <input type="number" id="resUserId" value="1" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Diagnostic Test ID</label>
                                <input type="text" id="resTestId" value="t1" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Laboratory Name</label>
                                <input type="text" id="resLabName" value="Tripoli Central Diagnostic Lab" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Sample Test Date</label>
                                <input type="date" id="resTestDate" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-1 font-medium">Report Issued Date</label>
                                <input type="date" id="resReportDate" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-1 font-medium">Upload Lab Report File (PDF / Image)</label>
                            <input type="file" id="resPdfFile" accept=".pdf,.png,.jpg,.jpeg" class="w-full p-2 bg-gray-900 border border-gray-700 rounded-lg text-gray-300">
                        </div>

                        <!-- BIOMARKERS SECTION -->
                        <div class="pt-4 border-t border-gray-800">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-outfit font-bold text-white text-sm">Biomarker Results (Measurements)</h4>
                                <button type="button" onclick="addBiomarkerRow()" class="px-3 py-1 bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xs rounded-lg font-semibold hover:bg-cyan-500/20">
                                    + Add Biomarker Row
                                </button>
                            </div>
                            <div id="biomarkersContainer" class="space-y-2">
                                <div class="grid grid-cols-5 gap-2 biomarker-row">
                                    <input type="text" placeholder="Name (e.g. Hemoglobin)" value="Hemoglobin" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                                    <input type="text" placeholder="Value (e.g. 14.2)" value="14.2" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                                    <input type="text" placeholder="Unit (e.g. g/dL)" value="g/dL" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                                    <input type="text" placeholder="Ref Range (e.g. 13.5 - 17.5)" value="13.5 - 17.5" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                                    <select class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                                        <option value="Normal">Normal</option>
                                        <option value="Low">Low</option>
                                        <option value="High">High</option>
                                        <option value="Borderline">Borderline</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 py-3.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-500/25 transition-all">
                            Publish Patient Test Result & Biomarkers
                        </button>
                    </form>
                </div>
            </div>

            <!-- TESTS TAB -->
            <div id="view-tests" class="hidden space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-xl font-bold text-white">Diagnostic Tests Catalog</h3>
                        <p class="text-xs text-gray-400">Manage available blood, heart, thyroid, and package tests</p>
                    </div>
                    <button onclick="openTestModal()" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold text-xs rounded-xl shadow-lg shadow-cyan-500/25">
                        + Add New Test / Package
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="testsGrid">
                    <!-- JS Populated -->
                </div>
            </div>

            <!-- LABS TAB -->
            <div id="view-labs" class="hidden space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-xl font-bold text-white">Partner Laboratories</h3>
                        <p class="text-xs text-gray-400">Accredited diagnostic centers operating in Tripoli</p>
                    </div>
                    <button onclick="openLabModal()" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold text-xs rounded-xl shadow-lg shadow-cyan-500/25">
                        + Add Partner Lab
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="labsGrid">
                    <!-- JS Populated -->
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL: ADD TEST -->
    <div id="testModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 p-4">
        <div class="glass-card w-full max-w-lg p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-outfit font-bold text-white text-lg">Add Diagnostic Test</h4>
                <button onclick="closeTestModal()" class="text-gray-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="handleSaveTest(event)" class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-400 mb-1">Test ID (e.g. t10)</label>
                        <input type="text" id="testId" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Category</label>
                        <select id="testCategory" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                            <option value="General">General</option>
                            <option value="Blood">Blood</option>
                            <option value="Heart">Heart</option>
                            <option value="Thyroid">Thyroid</option>
                            <option value="Energy">Energy</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-400 mb-1">Test Name</label>
                    <input type="text" id="testName" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-gray-400 mb-1">Subtitle</label>
                    <input type="text" id="testSubtitle" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-gray-400 mb-1">Description</label>
                    <textarea id="testDescription" rows="2" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-gray-400 mb-1">Price (LYD)</label>
                        <input type="number" step="0.01" id="testPrice" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Reports (Hours)</label>
                        <input type="number" id="testReportsHours" value="24" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Sample Type</label>
                        <input type="text" id="testSampleType" value="Blood" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                </div>
                <div class="flex items-center space-x-6 pt-2">
                    <label class="flex items-center space-x-2 text-gray-300">
                        <input type="checkbox" id="testFasting" class="rounded bg-gray-900 border-gray-700 text-cyan-500">
                        <span>Fasting Required</span>
                    </label>
                    <label class="flex items-center space-x-2 text-gray-300">
                        <input type="checkbox" id="testIsPackage" class="rounded bg-gray-900 border-gray-700 text-cyan-500">
                        <span>Is Health Package</span>
                    </label>
                </div>
                <button type="submit" class="w-full mt-4 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl">Save Diagnostic Test</button>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD LAB -->
    <div id="labModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 p-4">
        <div class="glass-card w-full max-w-lg p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-outfit font-bold text-white text-lg">Add Partner Laboratory</h4>
                <button onclick="closeLabModal()" class="text-gray-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="handleSaveLab(event)" class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-400 mb-1">Lab ID (e.g. l3)</label>
                        <input type="text" id="labId" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Rating (0-5)</label>
                        <input type="number" step="0.1" id="labRating" value="4.8" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-400 mb-1">Lab Name</label>
                    <input type="text" id="labName" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-gray-400 mb-1">Tripoli Address</label>
                    <input type="text" id="labAddress" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-400 mb-1">Phone Number</label>
                        <input type="text" id="labPhone" value="+218 21 " required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-1">Working Hours</label>
                        <input type="text" id="labHours" value="08:00 AM - 08:00 PM" required class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    </div>
                </div>
                <div class="pt-2">
                    <label class="flex items-center space-x-2 text-gray-300">
                        <input type="checkbox" id="labHomeCollection" checked class="rounded bg-gray-900 border-gray-700 text-cyan-500">
                        <span>Offers Home Sample Collection</span>
                    </label>
                </div>
                <button type="submit" class="w-full mt-4 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl">Save Partner Lab</button>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT CONTROLLER -->
    <script>
        const API_BASE = '/api';
        let authToken = localStorage.getItem('nexlab_admin_token') || '';

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('resTestDate').value = today;
            document.getElementById('resReportDate').value = today;

            if (authToken) {
                initDashboard();
            }
        });

        async function handleAdminLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errDiv = document.getElementById('loginError');
            errDiv.classList.add('hidden');

            try {
                const res = await fetch(`${API_BASE}/admin/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Login failed');
                }

                authToken = data.token;
                localStorage.setItem('nexlab_admin_token', authToken);
                document.getElementById('adminUserName').innerText = data.user.name;
                initDashboard();
            } catch (err) {
                errDiv.innerText = err.message;
                errDiv.classList.remove('hidden');
            }
        }

        function handleLogout() {
            if (authToken) {
                fetch(`${API_BASE}/admin/logout`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
            }
            authToken = '';
            localStorage.removeItem('nexlab_admin_token');
            document.getElementById('dashboardSection').classList.add('hidden');
            document.getElementById('loginSection').classList.remove('hidden');
        }

        async function initDashboard() {
            document.getElementById('loginSection').classList.add('hidden');
            document.getElementById('dashboardSection').classList.remove('hidden');
            loadStats();
            loadBookings();
            loadTests();
            loadLabs();
        }

        async function loadStats() {
            try {
                const res = await fetch(`${API_BASE}/admin/dashboard/stats`, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.status === 401 || res.status === 403) return handleLogout();

                const data = await res.json();
                const s = data.stats;

                document.getElementById('statRevenue').innerText = `${s.total_revenue_lyd.toFixed(2)} LYD`;
                document.getElementById('statBookings').innerText = s.total_bookings;
                document.getElementById('statPending').innerText = s.pending_bookings;
                document.getElementById('statCompleted').innerText = s.completed_bookings;
                document.getElementById('statPatients').innerText = s.total_patients;
                document.getElementById('statLabs').innerText = s.total_labs;
                document.getElementById('statTests').innerText = s.total_tests;

                renderRecentBookings(data.recent_bookings);
            } catch (e) {
                console.error(e);
            }
        }

        function renderRecentBookings(bookings) {
            const tbody = document.getElementById('recentBookingsTbody');
            tbody.innerHTML = bookings.map(b => `
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="py-3.5 px-4 font-bold text-cyan-400">${b.id}</td>
                    <td class="py-3.5 px-4">${b.patient_name}</td>
                    <td class="py-3.5 px-4">${b.diagnostic_test ? b.diagnostic_test.name : 'N/A'}</td>
                    <td class="py-3.5 px-4">${b.partner_lab ? b.partner_lab.name : 'N/A'}</td>
                    <td class="py-3.5 px-4">${b.date} (${b.time_slot})</td>
                    <td class="py-3.5 px-4 font-bold text-emerald-400">${b.total_amount} LYD</td>
                    <td class="py-3.5 px-4">${getStatusBadge(b.status)}</td>
                </tr>
            `).join('');
        }

        async function loadBookings(status = '') {
            try {
                const url = status ? `${API_BASE}/admin/bookings?status=${status}` : `${API_BASE}/admin/bookings`;
                const res = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const data = await res.json();

                const tbody = document.getElementById('allBookingsTbody');
                tbody.innerHTML = data.data.map(b => `
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="py-3.5 px-4 font-bold text-cyan-400">${b.id}</td>
                        <td class="py-3.5 px-4">${b.patient_name}</td>
                        <td class="py-3.5 px-4">${b.diagnostic_test ? b.diagnostic_test.name : 'N/A'}</td>
                        <td class="py-3.5 px-4">${b.partner_lab ? b.partner_lab.name : 'N/A'}</td>
                        <td class="py-3.5 px-4">${b.is_home_collection ? '🏠 Home' : '🏥 Lab Visit'}</td>
                        <td class="py-3.5 px-4">${b.date} (${b.time_slot})</td>
                        <td class="py-3.5 px-4 font-bold text-white">${b.total_amount} LYD</td>
                        <td class="py-3.5 px-4">
                            <select onchange="updateBookingStatus('${b.id}', this.value)" class="bg-gray-900 border border-gray-700 text-xs rounded-lg px-2 py-1 font-semibold ${getStatusColor(b.status)}">
                                <option value="pending" ${b.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="completed" ${b.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${b.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                    </tr>
                `).join('');
            } catch (e) {
                console.error(e);
            }
        }

        async function updateBookingStatus(id, newStatus) {
            try {
                await fetch(`${API_BASE}/admin/bookings/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                loadStats();
                loadBookings();
            } catch (e) {
                alert('Failed to update status');
            }
        }

        function addBiomarkerRow() {
            const container = document.getElementById('biomarkersContainer');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-5 gap-2 biomarker-row';
            row.innerHTML = `
                <input type="text" placeholder="Name" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                <input type="text" placeholder="Value" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                <input type="text" placeholder="Unit" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                <input type="text" placeholder="Ref Range" required class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                <select class="p-2 bg-gray-900 border border-gray-700 rounded text-white text-xs">
                    <option value="Normal">Normal</option>
                    <option value="Low">Low</option>
                    <option value="High">High</option>
                    <option value="Borderline">Borderline</option>
                </select>
            `;
            container.appendChild(row);
        }

        async function handleUploadResult(e) {
            e.preventDefault();
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

                if (res.ok) {
                    alert('Patient test result and biomarkers published successfully!');
                    switchTab('overview');
                } else {
                    const err = await res.json();
                    alert(err.message || 'Failed to publish test result');
                }
            } catch (err) {
                alert('Network error publishing test result');
            }
        }

        async function loadTests() {
            try {
                const res = await fetch(`${API_BASE}/tests`);
                const data = await res.json();
                const grid = document.getElementById('testsGrid');

                grid.innerHTML = data.data.map(t => `
                    <div class="glass-card p-5 rounded-2xl flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">${t.category}</span>
                                <span class="font-outfit text-lg font-bold text-emerald-400">${t.price} LYD</span>
                            </div>
                            <h4 class="font-outfit font-bold text-white text-base">${t.name}</h4>
                            <p class="text-xs text-gray-400 mt-1">${t.subtitle}</p>
                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">${t.description}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-800 flex items-center justify-between text-xs text-gray-400">
                            <span>⏱️ ${t.reports_in_hours}h report</span>
                            <span>🧪 ${t.sample_type}</span>
                            <button onclick="deleteTest('${t.id}')" class="text-red-400 hover:text-red-300 font-medium">Delete</button>
                        </div>
                    </div>
                `).join('');
            } catch (e) { console.error(e); }
        }

        async function loadLabs() {
            try {
                const res = await fetch(`${API_BASE}/labs`);
                const data = await res.json();
                const grid = document.getElementById('labsGrid');

                grid.innerHTML = data.data.map(l => `
                    <div class="glass-card p-5 rounded-2xl space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-outfit font-bold text-white text-base">${l.name}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">📍 ${l.address}</p>
                            </div>
                            <div class="px-2.5 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 text-xs font-bold rounded-lg">
                                ⭐ ${l.rating} (${l.reviews_count})
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 space-y-1">
                            <p>📞 ${l.phone}</p>
                            <p>🕒 ${l.hours}</p>
                            <p class="text-cyan-400">${l.has_home_collection ? '✓ Offers Home Sample Collection' : '✕ Lab Visit Only'}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-800 text-right">
                            <button onclick="deleteLab('${l.id}')" class="text-xs text-red-400 hover:text-red-300 font-medium">Delete Partner Lab</button>
                        </div>
                    </div>
                `).join('');
            } catch (e) { console.error(e); }
        }

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

            const res = await fetch(`${API_BASE}/admin/tests`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${authToken}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                closeTestModal();
                loadTests();
                loadStats();
            } else {
                alert('Error creating test');
            }
        }

        async function deleteTest(id) {
            if (!confirm('Are you sure you want to delete this diagnostic test?')) return;
            await fetch(`${API_BASE}/admin/tests/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
            });
            loadTests();
            loadStats();
        }

        async function handleSaveLab(e) {
            e.preventDefault();
            const body = {
                id: document.getElementById('labId').value,
                name: document.getElementById('labName').value,
                rating: parseFloat(document.getElementById('labRating').value),
                reviews_count: 0,
                address: document.getElementById('labAddress').value,
                phone: document.getElementById('labPhone').value,
                hours: document.getElementById('labHours').value,
                has_home_collection: document.getElementById('labHomeCollection').checked,
            };

            const res = await fetch(`${API_BASE}/admin/labs`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${authToken}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                closeLabModal();
                loadLabs();
                loadStats();
            } else {
                alert('Error creating lab');
            }
        }

        async function deleteLab(id) {
            if (!confirm('Are you sure you want to delete this partner lab?')) return;
            await fetch(`${API_BASE}/admin/labs/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
            });
            loadLabs();
            loadStats();
        }

        function switchTab(tab) {
            ['overview', 'bookings', 'results', 'tests', 'labs'].forEach(t => {
                document.getElementById(`view-${t}`).classList.add('hidden');
                const btn = document.getElementById(`tab-${t}`);
                btn.classList.remove('bg-cyan-600', 'text-white');
                btn.classList.add('text-gray-400');
            });

            document.getElementById(`view-${tab}`).classList.remove('hidden');
            const activeBtn = document.getElementById(`tab-${tab}`);
            activeBtn.classList.add('bg-cyan-600', 'text-white');
            activeBtn.classList.remove('text-gray-400');
        }

        function getStatusBadge(status) {
            if (status === 'completed') return '<span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px]">Completed</span>';
            if (status === 'cancelled') return '<span class="px-2 py-0.5 rounded bg-red-500/20 text-red-400 border border-red-500/30 text-[10px]">Cancelled</span>';
            return '<span class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 text-[10px]">Pending</span>';
        }

        function getStatusColor(status) {
            if (status === 'completed') return 'text-emerald-400 border-emerald-500/40';
            if (status === 'cancelled') return 'text-red-400 border-red-500/40';
            return 'text-yellow-400 border-yellow-500/40';
        }

        function openTestModal() { document.getElementById('testModal').classList.remove('hidden'); }
        function closeTestModal() { document.getElementById('testModal').classList.add('hidden'); }
        function openLabModal() { document.getElementById('labModal').classList.remove('hidden'); }
        function closeLabModal() { document.getElementById('labModal').classList.add('hidden'); }
    </script>
</body>
</html>
