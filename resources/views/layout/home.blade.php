<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Management System</title>
    
    {{-- Tautkan Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Style untuk Sidebar aktif */
        .active-link {
            background-color: #f3f4f6; /* bg-gray-100 */
            color: #111827; /* text-gray-900 */
        }
        .dark .active-link {
            background-color: #374151; /* bg-gray-700 */
            color: #ffffff; /* text-white */
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">

    {{-- Memanggil Komponen Navbar (Diasumsikan ada di component.navbar) --}}
    @include('component.navbar')

    {{-- Sidebar --}}
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium pt-4">
                
                {{-- 1. Dashboard (Icon: Pie Chart) --}}
                <li>
                    <a href="{{ route('pages.dashboard') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('pages.dashboard') ? 'active-link' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>

                {{-- 2. Data Transaksi Wisata (Icon: Ticket/Receipt) --}}
                <li>
                    <a href="{{ route('transaksi.index') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('transaksi.index') ? 'active-link' : '' }}">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm4.008 0a2 2 0 0 1 3.984 0H14a2 2 0 1 1 4 0h2v12H18a2 2 0 1 1-4 0h-4.008a2 2 0 1 1-3.984 0H4V6h2.008Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Data Transaksi Wisata</span>
                    </a>
                </li>

                {{-- 3. Data Booking Parkir (Icon: Car) --}}
                <li>
                    <a href="{{ route('parkir.index') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('parkir.index') ? 'active-link' : '' }}">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M17 7a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v1H4.972a2 2 0 0 0-1.982 2.307l1.178 7.657A2 2 0 0 0 6.146 20H17.854a2 2 0 0 0 1.978-1.693l1.178-7.657A2 2 0 0 0 19.028 8H17V7Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Data Booking Parkir</span>
                    </a>
                </li>
                {{-- 4. Data Wisata (Icon: Map/Compass) --}}
                <li>
                    <a href="{{ route('wisata.index') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('wisata.index') ? 'active-link' : '' }}">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Data Wisata</span>
                    </a>
                </li>
                {{-- 4. Data User (Icon: User Group) --}}
                <li>
                    <a href="{{ route('pages.data-user') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('pages.data-user') ? 'active-link' : '' }}">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Data User</span>
                    </a>
                </li>

                {{-- 5. Data Admin (Icon: User Shield/Lock) --}}
                <li>
                    <a href="{{ route('pages.data-admin') }}" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('pages.data-admin') ? 'active-link' : '' }}">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-7.633 12.54a7.002 7.002 0 0 1 11.202-2.427 4.96 4.96 0 0 0-2.348 2.037 4.993 4.993 0 0 0-5.78 3.408A6.975 6.975 0 0 1 4.367 14.54Zm8.777 3.325a2.999 2.999 0 0 1 3.356-.284.75.75 0 0 1 .244 1.12 3.003 3.003 0 0 1-3.878.44c-.31-.225-.516-.56-.547-.94a.75.75 0 0 1 .825-.816l-.001.48Zm.206-4.865a3.003 3.003 0 0 1 3.324 3.906l-2.05-2.05a2.988 2.988 0 0 1-1.274-1.856Zm5.97 1.076a1 1 0 1 1-2 0v-1a1 1 0 1 1 2 0v1Z" clip-rule="evenodd"/>
                            <path d="M19 14v-1a3 3 0 0 0-6 0v1H19Z"/> 
                            <path d="M13 14h6v5a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-5Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Data Admin</span>
                    </a>
                </li>

                {{-- 6. Log Out (Icon: Arrow Right From Bracket) --}}
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="flex items-center w-full p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            
                            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" 
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap text-left">Log Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    {{-- Area Konten Utama --}}
    <div class="p-4 sm:ml-64">
        {{-- Area konten dinamis, menghilangkan border-dashed --}}
        <div class="mt-16">
            @yield('content')
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>