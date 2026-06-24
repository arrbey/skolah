<div x-data="floatingChat()" 
     class="fixed bottom-6 right-6 z-[60]"
     @click.away="isOpen = false">
    
    {{-- Floating Button --}}
    <button @click="toggleChat()" 
            class="relative w-14 h-14 bg-primary-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:bg-primary-700 hover:scale-110 active:scale-95 transition-all duration-300">
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg x-show="isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        {{-- Unread Badge --}}
        <div x-show="totalUnread > 0" 
             class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white"
             x-text="totalUnread" x-cloak></div>
    </button>

    {{-- Chat Window --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-10"
         class="absolute bottom-16 right-0 w-[350px] max-w-[calc(100vw-2rem)] h-[500px] bg-white rounded-2xl shadow-2xl border border-slate-100 flex flex-col overflow-hidden"
         x-cloak>
        
        {{-- Header --}}
        <div class="p-4 bg-primary-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <template x-if="selectedUser">
                    <button @click="selectedUser = null" class="mr-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                </template>
                <h3 class="font-bold" x-text="selectedUser ? selectedUser.name : 'Pesan'"></h3>
            </div>
            <button @click="isOpen = false" class="text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Content: User List --}}
        <div x-show="!selectedUser" class="flex-grow overflow-y-auto divide-y divide-slate-50">
            <template x-for="user in users" :key="user.id">
                <div @click="selectUser(user)" 
                     class="p-4 flex items-center gap-3 hover:bg-slate-50 cursor-pointer transition-colors">
                    <div class="relative">
                        <img :src="user.avatar" class="w-10 h-10 rounded-full object-cover border border-slate-100">
                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 border-2 border-white rounded-full"
                             :class="isOnline(user.id) ? 'bg-green-500' : 'bg-slate-300'"></div>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-sm font-semibold text-slate-800" x-text="user.name"></p>
                        <p class="text-xs text-slate-500 truncate" x-text="user.role"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Content: Conversation --}}
        <div x-show="selectedUser" class="flex-grow flex flex-col overflow-hidden">
            <div class="flex-grow overflow-y-auto p-4 space-y-3 bg-slate-50/50" x-ref="miniMsgContainer">
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex" :class="msg.sender_id == {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[85%] p-2.5 rounded-2xl text-xs"
                             :class="msg.sender_id == {{ Auth::id() }} ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-700 rounded-tl-none shadow-sm'">
                            <template x-if="msg.image_url">
                                <img :src="msg.image_url" class="rounded-lg mb-2 max-w-full h-auto">
                            </template>
                            <p x-text="msg.message" class="leading-relaxed"></p>
                            <p class="text-[9px] mt-1 opacity-70 text-right" x-text="formatTime(msg.created_at)"></p>
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Mini Input --}}
            <div class="p-3 bg-white border-t border-slate-100">
                <form @submit.prevent="send()" class="flex gap-2">
                    <input type="text" x-model="newMessage" placeholder="Ketik..." 
                           class="flex-grow text-xs px-3 py-2 bg-slate-100 border-none rounded-full focus:ring-1 focus:ring-primary-500">
                    <button type="submit" class="bg-primary-600 text-white p-2 rounded-full hover:bg-primary-700 shadow-md">
                        <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function floatingChat() {
    return {
        isOpen: false,
        users: [],
        selectedUser: null,
        messages: [],
        newMessage: '',
        onlineUsers: [],
        totalUnread: 0,

        async init() {
            // Load users
            const response = await axios.get('{{ route('dashboard.chat') }}');
            // Parse users from view? Better to have a separate API, 
            // but let's fetch them from a simplified endpoint or existing logic
            this.fetchUsers();
            this.initEcho();
        },

        async fetchUsers() {
            try {
                // Untuk sementara, kita ambil list user yang sama dengan chat page
                // Kita buat API kecil di Controller nanti
                const res = await axios.get('/dashboard/api/chat/users');
                this.users = res.data;
            } catch (e) {}
        },

        initEcho() {
            let checkEcho = setInterval(() => {
                if (window.Echo) {
                    clearInterval(checkEcho);
                    // Join Presence
                    window.Echo.join('chat.presence')
                        .here(u => this.onlineUsers = u)
                        .joining(u => this.onlineUsers.push(u))
                        .leaving(u => this.onlineUsers = this.onlineUsers.filter(x => x.id !== u.id));

                    // Listen for my notifications
                    window.Echo.private('chat.' + {{ Auth::id() }})
                        .listen('.message.sent', (e) => {
                            if (this.selectedUser && e.message.sender_id == this.selectedUser.id) {
                                this.messages.push(e.message);
                                this.scrollToBottom();
                            } else {
                                this.totalUnread++;
                            }
                        });
                }
            }, 500);
        },

        async selectUser(user) {
            this.selectedUser = user;
            this.messages = [];
            try {
                const res = await axios.get(`/dashboard/api/chat/messages/${user.id}`);
                this.messages = res.data;
                this.scrollToBottom();
            } catch (e) {}
        },

        async send() {
            if (!this.newMessage.trim() || !this.selectedUser) return;
            const msg = this.newMessage;
            this.newMessage = '';

            try {
                const res = await axios.post('{{ route('dashboard.chat.send') }}', {
                    receiver_id: this.selectedUser.id,
                    message: msg
                });
                if (res.data.status === 'success') {
                    this.messages.push(res.data.message);
                    this.scrollToBottom();
                }
            } catch (e) {}
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) this.totalUnread = 0;
        },

        isOnline(userId) {
            return this.onlineUsers.some(u => u.id == userId);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.miniMsgContainer;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return isNaN(date.getTime()) ? '...' : date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        }
    }
}
</script>
