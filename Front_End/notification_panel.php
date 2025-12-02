<?php
// notification_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<div id="notificationPanel" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">

    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold">NOTIFICATIONS</h2>
        <button id="closeNotificationBtn" class="text-gray-500 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="notificationItemsContainer" class="p-4 overflow-y-auto h-[calc(100vh-64px)]">
        
        <?php if ($isLoggedIn): ?>
            <div class="space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="font-semibold text-sm">Sale Alert! 🛍️</p>
                    <p class="text-xs text-gray-600">Flash sale on all new arrivals! Up to 50% off. Hurry, ends tonight.</p>
                </div>
                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <p class="font-semibold text-sm">Order Status Update</p>
                    <p class="text-xs text-gray-600">Your order #12345 has been shipped and is on its way!</p>
                </div>
                <div class="text-center p-4 text-sm text-gray-500">
                    <p>No older notifications.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-gray-600">
                <p class="mb-4">Log in to receive personalized notifications and alerts.</p>
                <a href="login.php" class="text-white bg-amber-500 px-4 py-2 rounded-full font-semibold hover:bg-amber-600 transition-colors duration-200">
                    Log In Now
                </a>
            </div>
        <?php endif; ?>

    </div>

</div>

<div id="notificationOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Get the panel elements
    const notificationPanel = document.getElementById('notificationPanel');
    const closeNotificationBtn = document.getElementById('closeNotificationBtn');
    const notificationOverlay = document.getElementById('notificationOverlay');
    
    // ⭐ Get the Notification trigger ⭐
    const openNotificationIconBtn = document.getElementById('openNotificationBtn'); 

    // Function to open the panel
    function openNotificationPanel() {
        notificationPanel.classList.remove('translate-x-full'); // Slides in
        notificationOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    // Function to close the panel
    function closeNotificationPanel() {
        notificationPanel.classList.add('translate-x-full'); // Slides out
        notificationOverlay.classList.add('hidden');
        document.body.style.overflow = ''; 
    }

    // Set up listener for the main icon
    if (openNotificationIconBtn) {
        openNotificationIconBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            openNotificationPanel();
        });
    }

    // Event listeners to close the panel
    if (closeNotificationBtn) closeNotificationBtn.addEventListener('click', closeNotificationPanel);
    if (notificationOverlay) notificationOverlay.addEventListener('click', closeNotificationPanel); 

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && notificationPanel && !notificationPanel.classList.contains('translate-x-full')) {
            closeNotificationPanel();
        }
    });

</script>