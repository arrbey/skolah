@extends('layouts.dashboard')

@section('title', 'Realtime Chat - Skolah.com')

@section('content')
<div class="h-[calc(100vh-140px)]">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex h-full border border-slate-100" 
         x-data="chatApp()" 
         x-init="initEcho()">
        
        {{-- User List --}}
        <div class="w-1/3 border-r border-slate-100 flex flex-col bg-slate-50/50">
            <div class="p-4 border-b border-slate-100 bg-white">
                <h2 class="text-lg font-black text-slate-900">Pesan</h2>
                <div class="mt-3 relative">
                    <input type="text" placeholder="Cari mentor..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-100 border-none rounded-xl text-xs focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div class="overflow-y-auto flex-grow">
                @foreach($users as $user)
                    <a href="{{ route('dashboard.chat', $user->id) }}" 
                       class="flex items-center gap-3 p-3 hover:bg-white transition-all border-b border-slate-50 {{ optional($activeChat)->id == $user->id ? 'bg-white border-l-4 border-l-blue-600' : '' }}">
                        <div class="relative shrink-0">
                            <img src="{{ avatarUrl($user) }}" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                            <div class="absolute bottom-0 right-0 w-2.5 h-2.5 border-2 border-white rounded-full transition-colors duration-500"
                                 :class="isOnline({{ $user->id }}) ? 'bg-green-500' : 'bg-slate-300'"></div>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h4 class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</h4>
                            </div>
                            <p class="text-[10px] text-slate-500 truncate lowercase">{{ $user->roles->first()->name ?? 'User' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="flex-grow flex flex-col bg-white">
            @if($activeChat)
                {{-- Header --}}
                <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ avatarUrl($activeChat) }}" 
                             class="w-9 h-9 rounded-full object-cover">
                        <div class="flex-grow min-w-0">
                            <h2 class="text-sm font-bold text-slate-800 truncate">{{ $activeChat->name }}</h2>
                            <p class="text-[10px] font-medium uppercase tracking-wider transition-colors duration-500"
                               :class="isOnline({{ $activeChat->id }}) ? 'text-green-500' : 'text-slate-400'"
                               x-text="isOnline({{ $activeChat->id }}) ? 'Online' : 'Offline'"></p>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-grow overflow-y-auto p-4 space-y-4 bg-slate-50/30" id="message-container" x-ref="messageContainer">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $msg->sender_id == Auth::id() ? 'bg-blue-600 text-white rounded-t-xl rounded-l-xl' : 'bg-white text-slate-700 rounded-t-xl rounded-r-xl border border-slate-100 shadow-sm' }} p-3">
                                @if($msg->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $msg->image) }}" 
                                             class="rounded-lg max-w-full max-h-[300px] object-cover cursor-pointer hover:opacity-90 transition-all"
                                             @click="previewImage = '{{ asset('storage/' . $msg->image) }}'">
                                    </div>
                                @endif
                                @if($msg->message)
                                    <p class="text-xs leading-relaxed">{{ $msg->message }}</p>
                                @endif
                                <p class="text-[9px] mt-1 opacity-70 text-right">{{ $msg->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    <template x-for="msg in newMessages" :key="msg.id">
                        <div class="flex" :class="msg.sender_id == {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                            <div :class="msg.sender_id == {{ Auth::id() }} ? 'bg-blue-600 text-white rounded-t-xl rounded-l-xl' : 'bg-white text-slate-700 rounded-t-xl rounded-r-xl border border-slate-100 shadow-sm'" class="max-w-[80%] p-3">
                                <template x-if="msg.image || msg.image_url">
                                    <div class="mb-2">
                                        <img :src="msg.image_url || '{{ asset('storage') }}/' + msg.image" 
                                             class="rounded-lg max-w-full max-h-[300px] object-cover cursor-pointer hover:opacity-90 transition-all"
                                             @click="previewImage = msg.image_url || '{{ asset('storage') }}/' + msg.image">
                                    </div>
                                </template>
                                <template x-if="msg.message">
                                    <p class="text-xs leading-relaxed" x-text="msg.message"></p>
                                </template>
                                <p class="text-[9px] mt-1 opacity-70 text-right" x-text="formatTime(msg.created_at)"></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Preview Image Modal --}}
                <div x-show="previewImage" 
                     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4"
                     x-transition
                     @click="previewImage = null"
                     x-cloak>
                    <img :src="previewImage" class="max-w-full max-h-full rounded-xl shadow-2xl">
                    <button class="absolute top-6 right-6 text-white bg-white/10 p-2 rounded-full hover:bg-white/20 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Input --}}
                <div class="p-3 bg-white border-t border-slate-100">
                    {{-- Image Preview Before Send --}}
                    <div x-show="imagePreview" class="mb-3 relative inline-block" x-cloak>
                        <img :src="imagePreview" class="w-20 h-20 object-cover rounded-xl border-2 border-blue-500">
                        <button @click="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form @submit.prevent="send()" class="flex items-end gap-2">
                        <div class="flex gap-1 mb-1">
                            {{-- Emoji Button --}}
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" 
                                        class="p-2 text-slate-400 hover:text-blue-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute bottom-12 left-0 bg-white border border-slate-100 shadow-2xl rounded-2xl p-3 grid grid-cols-6 gap-2 w-56 z-50"
                                     x-cloak>
                                    <template x-for="emoji in ['😀','😂','😍','🙌','🔥','👏','👍','💡','✅','🚀','🎓','💪']">
                                        <button type="button" @click="addEmoji(emoji); open = false" 
                                                class="text-xl hover:scale-125 transition-all" x-text="emoji"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- File Button --}}
                            <button type="button" @click="$refs.fileInput.click()" 
                                    class="p-2 text-slate-400 hover:text-blue-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                            <input type="file" x-ref="fileInput" class="hidden" @change="handleImageUpload($event)" accept="image/*">
                        </div>

                        <div class="flex-grow relative">
                            <input type="text" x-model="newMessage" placeholder="Ketik pesan..." 
                                   class="w-full pl-4 pr-10 py-2.5 bg-slate-100 border-none rounded-xl text-xs focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white p-2.5 rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all disabled:opacity-50"
                                :disabled="!newMessage.trim() && !imageFile">
                            <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                        <img src="https://img.icons8.com/3d-fluency/100/chat-message.png" class="w-16 h-16">
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-1">Mulai Diskusi</h3>
                    <p class="text-xs text-slate-500 max-w-xs">Pilih mentor atau teman kursus di sebelah kiri untuk mulai mengobrol.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function chatApp() {
    return {
        newMessage: '',
        newMessages: [],
        receiverId: {{ $activeChat->id ?? 'null' }},
        imageFile: null,
        imagePreview: null,
        previewImage: null,
        onlineUsers: [],
        
        initEcho() {
            // Presence Channel untuk status Online/Offline
            let checkEchoPresence = setInterval(() => {
                if (window.Echo) {
                    clearInterval(checkEchoPresence);
                    window.Echo.join('chat.presence')
                        .here((users) => {
                            this.onlineUsers = users;
                        })
                        .joining((user) => {
                            this.onlineUsers.push(user);
                        })
                        .leaving((user) => {
                            this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
                        });
                }
            }, 100);

            if (!this.receiverId) return;
            this.scrollToBottom();

            let checkEcho = setInterval(() => {
                if (window.Echo) {
                    clearInterval(checkEcho);
                    window.Echo.private('chat.' + {{ Auth::id() }})
                        .listen('.message.sent', (e) => {
                            if (e.message.sender_id == this.receiverId) {
                                this.newMessages.push(e.message);
                                this.scrollToBottom();
                            }
                        });
                }
            }, 100);
        },

        isOnline(userId) {
            return this.onlineUsers.some(u => u.id == userId);
        },

        async send() {
            if (!this.newMessage.trim() && !this.imageFile) return;

            const formData = new FormData();
            formData.append('receiver_id', this.receiverId);
            formData.append('message', this.newMessage);
            if (this.imageFile) {
                formData.append('image', this.imageFile);
            }

            // Reset input segera untuk UX yang lebih cepat
            this.newMessage = '';
            this.removeImage();

            try {
                const response = await axios.post('{{ route('dashboard.chat.send') }}', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                if (response.data.status === 'success') {
                    const exists = this.newMessages.find(m => m.id === response.data.message.id);
                    if (!exists) {
                        this.newMessages.push(response.data.message);
                        this.scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                const errorMsg = error.response?.data?.message || 'Gagal mengirim pesan.';
                alert(errorMsg);
            }
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removeImage() {
            this.imageFile = null;
            this.imagePreview = null;
            this.$refs.fileInput.value = '';
        },

        addEmoji(emoji) {
            this.newMessage += emoji;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatTime(dateString) {
            if (!dateString) return new Date().getHours().toString().padStart(2, '0') + ':' + new Date().getMinutes().toString().padStart(2, '0');
            const date = new Date(dateString);
            return isNaN(date.getTime()) 
                ? '...' 
                : date.getHours().toString().padStart(2, '0') + ':' + 
                  date.getMinutes().toString().padStart(2, '0');
        }
    }
}
</script>
@endsection
