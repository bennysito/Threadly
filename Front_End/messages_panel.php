<?php
// messages_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<div id="messagesPanel" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">

    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold">MESSAGES</h2>
        <button id="closeMessagesBtn" class="text-gray-500 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="messagesContainer" class="p-4 overflow-y-auto h-[calc(100vh-64px)]">
        
        <?php if ($isLoggedIn): ?>
            <div class="space-y-4">
                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                    <span class="relative flex h-3 w-3 mr-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Customer Support</p>
                        <p class="text-xs text-gray-600 truncate">RE: Issue with Order #5678 - I'm following up on this now...</p>
                    </div>
                    <span class="text-xs text-gray-400 ml-2">2m ago</span>
                </div>
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                    <span class="relative flex h-3 w-3 mr-3">
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-300"></span>
                    </span>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Seller: Threadly Boutique</p>
                        <p class="text-xs text-gray-600 truncate">Your new shipment has been verified and is ready.</p>
                    </div>
                    <span class="text-xs text-gray-400 ml-2">1h ago</span>
                </div>
                <div class="text-center p-4 text-sm text-gray-500">
                    <p>End of messages.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-gray-600">
                <p class="mb-4">Log in to chat with support or sellers about your orders.</p>
                <a href="login.php" class="text-white bg-amber-500 px-4 py-2 rounded-full font-semibold hover:bg-amber-600 transition-colors duration-200">
                    Log In Now
                </a>
            </div>
        <?php endif; ?>

    </div>

</div>

<div id="messagesOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Get the panel elements
    const messagesPanel = document.getElementById('messagesPanel');
    const closeMessagesBtn = document.getElementById('closeMessagesBtn');
    const messagesOverlay = document.getElementById('messagesOverlay');
    
    // ⭐ Get the Messages trigger ⭐
    const openMessagesIconBtn = document.getElementById('openMessagesBtn'); 

    // Function to open the panel
    function openMessagesPanel() {
        messagesPanel.classList.remove('translate-x-full'); // Slides in
        messagesOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    // Function to close the panel
    function closeMessagesPanel() {
        messagesPanel.classList.add('translate-x-full'); // Slides out
        messagesOverlay.classList.add('hidden');
        document.body.style.overflow = ''; 
    }

    // Set up listener for the main icon (assuming the chat icon in nav_bar has the id 'openMessagesBtn')
    if (openMessagesIconBtn) {
        openMessagesIconBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            openMessagesPanel();
        });
    }

    // Event listeners to close the panel
    if (closeMessagesBtn) closeMessagesBtn.addEventListener('click', closeMessagesPanel);
    if (messagesOverlay) messagesOverlay.addEventListener('click', closeMessagesPanel); 

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && messagesPanel && !messagesPanel.classList.contains('translate-x-full')) {
            closeMessagesPanel();
        }
    });

</script>