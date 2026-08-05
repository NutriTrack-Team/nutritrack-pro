<nav class="flex items-center justify-between p-4 bg-blue-600 text-white">
    <h1 class="font-bold">NutriTrack Pro</h1>

    <!-- Notification Bell -->
    <div class="relative">
        <!-- Bell Button -->
        <button id="notificationBell" class="relative focus:outline-none">
            <i class="fa fa-bell text-xl"></i>

            <!-- Notification Count Badge -->
            <span
                id="notificationCount"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-1 hidden">
                0
            </span>
        </button>

        <!-- Notification Dropdown -->
        <div
            id="notificationDropdown"
            class="hidden absolute right-0 mt-3 w-80 bg-white text-black rounded-lg shadow-lg z-50">

            <div class="p-4 border-b font-semibold">
                Recent Alerts
            </div>

            <div id="notificationList" class="max-h-72 overflow-y-auto">
                <p class="p-4 text-sm text-gray-500">No new alerts</p>
            </div>
        </div>
    </div>
</nav>
