<?php
// messages_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<!-- Messages List Panel -->
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
            <div class="space-y-2">
                <!-- Contact 1 -->
                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors message-contact" data-contact-name="PROG PROJECT" data-contact-status="Active now">
                    <div class="relative mr-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            PP
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <p class="font-semibold text-sm truncate">PROG PROJECT</p>
                            <span class="text-xs text-gray-400 ml-2">1:38 PM</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate">hELLO PA CHECK KO SA ORDER SIR</p>
                    </div>
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </div>

                <!-- Contact 2 -->
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors message-contact" data-contact-name="Customer Support" data-contact-status="Online">
                    <div class="relative mr-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold">
                            CS
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <p class="font-semibold text-sm truncate">Customer Support</p>
                            <span class="text-xs text-gray-400 ml-2">2m ago</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate">RE: Issue with Order #5678 - I'm following up...</p>
                    </div>
                </div>

                <!-- Contact 3 -->
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors message-contact" data-contact-name="Threadly Boutique" data-contact-status="Last seen 1h ago">
                    <div class="relative mr-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold">
                            TB
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-gray-300 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <p class="font-semibold text-sm truncate">Threadly Boutique</p>
                            <span class="text-xs text-gray-400 ml-2">1h ago</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate">Your new shipment has been verified and is ready.</p>
                    </div>
                </div>

                <!-- Contact 4 -->
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors message-contact" data-contact-name="Josh Bowenn Tugahan" data-contact-status="Last seen 2h ago">
                    <div class="relative mr-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                            JB
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-gray-300 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <p class="font-semibold text-sm truncate">Josh Bowenn Tugahan</p>
                            <span class="text-xs text-gray-400 ml-2">2h ago</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate">Thanks for the quick delivery!</p>
                    </div>
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

<!-- Chat Window -->
<div id="chatWindow" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">
    
    <!-- Chat Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center flex-1 min-w-0">
            <button id="backToChatListBtn" class="mr-3 text-blue-500 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>
            <div class="relative mr-3 flex-shrink-0">
                <div id="chatContactAvatar" class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    PP
                </div>
                <span id="chatContactStatus" class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p id="chatContactName" class="font-semibold text-sm truncate">PROG PROJECT</p>
                <p id="chatContactStatusText" class="text-xs text-gray-500 truncate">Active now</p>
            </div>
        </div>
        <button id="closeChatBtn" class="text-gray-500 hover:text-gray-900 ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Chat Messages -->
    <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" style="height: calc(100vh - 140px);">
        
        <!-- Received Message -->
        <div class="flex items-start">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-2 flex-shrink-0">
                PP
            </div>
            <div class="flex flex-col max-w-xs">
                <div class="bg-white rounded-2xl rounded-tl-none px-4 py-2 shadow-sm">
                    <p class="text-sm">Pa confirm unya mi sir reymark </p>
                </div>
                <span class="text-xs text-gray-400 mt-1 ml-2">1:35 PM</span>
            </div>
        </div>

        <div class="flex items-start">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-2 flex-shrink-0">
                PP
            </div>
            <div class="flex flex-col max-w-xs">
                <div class="bg-white rounded-2xl rounded-tl-none px-4 py-2 shadow-sm">
                    <p class="text-sm">HELLO SIR</p>
                </div>
                <span class="text-xs text-gray-400 mt-1 ml-2">1:36 PM</span>
            </div>
        </div>

        <!-- Sent Message -->
        <div class="flex items-end justify-end">
            <div class="flex flex-col max-w-xs items-end">
                <div class="bg-blue-500 text-white rounded-2xl rounded-tr-none px-4 py-2 shadow-sm">
                    <p class="text-sm">NAABOT NA SIR</p>
                </div>
                <span class="text-xs text-gray-400 mt-1 mr-2">1:38 PM</span>
            </div>
        </div>

        <!-- Image Message -->
        <div class="flex items-end justify-end">
            <div class="flex flex-col max-w-xs items-end">
                <div class="bg-blue-500 rounded-2xl rounded-tr-none p-2 shadow-sm">
                    <div class="grid grid-cols-3 gap-1">
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                        <div class="w-20 h-20 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <span class="text-xs text-gray-400 mt-1 mr-2">1:38 PM</span>
            </div>
        </div>

    </div>

    <!-- Message Input -->
    <div class="border-t border-gray-200 p-3 bg-white">
        <div class="flex items-center space-x-2">
            <button class="text-blue-500 hover:text-blue-700 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
            <button class="text-blue-500 hover:text-blue-700 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </button>
            <input type="text" placeholder="Aa" class="flex-1 px-4 py-2 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <button class="text-blue-500 hover:text-blue-700 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                </svg>
            </button>
            <button class="text-blue-500 hover:text-blue-700 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                </svg>
            </button>
        </div>
    </div>

</div>

<div id="messagesOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Get all elements
    const messagesPanel = document.getElementById('messagesPanel');
    const chatWindow = document.getElementById('chatWindow');
    const closeMessagesBtn = document.getElementById('closeMessagesBtn');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const backToChatListBtn = document.getElementById('backToChatListBtn');
    const messagesOverlay = document.getElementById('messagesOverlay');
    const openMessagesIconBtn = document.getElementById('openMessagesBtn');
    const messageContacts = document.querySelectorAll('.message-contact');

    // Function to open messages list panel
    function openMessagesPanel() {
        messagesPanel.classList.remove('translate-x-full');
        messagesOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Function to close messages list panel
    function closeMessagesPanel() {
        messagesPanel.classList.add('translate-x-full');
        messagesOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Function to open chat window
    function openChatWindow(contactName, contactStatus) {
        // Update chat header info
        document.getElementById('chatContactName').textContent = contactName;
        document.getElementById('chatContactStatusText').textContent = contactStatus;
        
        // Get first letters for avatar
        const initials = contactName.split(' ').map(word => word[0]).join('').substring(0, 2);
        document.getElementById('chatContactAvatar').textContent = initials;
        
        // Hide messages list and show chat window
        messagesPanel.classList.add('translate-x-full');
        chatWindow.classList.remove('translate-x-full');
    }

    // Function to close chat window
    function closeChatWindow() {
        chatWindow.classList.add('translate-x-full');
        messagesOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Function to go back to chat list
    function backToChatList() {
        chatWindow.classList.add('translate-x-full');
        messagesPanel.classList.remove('translate-x-full');
    }

    // Event listeners
    if (openMessagesIconBtn) {
        openMessagesIconBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openMessagesPanel();
        });
    }

    if (closeMessagesBtn) {
        closeMessagesBtn.addEventListener('click', closeMessagesPanel);
    }

    if (closeChatBtn) {
        closeChatBtn.addEventListener('click', closeChatWindow);
    }

    if (backToChatListBtn) {
        backToChatListBtn.addEventListener('click', backToChatList);
    }

    if (messagesOverlay) {
        messagesOverlay.addEventListener('click', () => {
            closeMessagesPanel();
            closeChatWindow();
        });
    }

    // Click on contacts to open chat
    messageContacts.forEach(contact => {
        contact.addEventListener('click', () => {
            const contactName = contact.getAttribute('data-contact-name');
            const contactStatus = contact.getAttribute('data-contact-status');
            openChatWindow(contactName, contactStatus);
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!chatWindow.classList.contains('translate-x-full')) {
                closeChatWindow();
            } else if (!messagesPanel.classList.contains('translate-x-full')) {
                closeMessagesPanel();
            }
        }
    });
</script>