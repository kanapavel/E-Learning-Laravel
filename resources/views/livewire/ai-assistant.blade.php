<div>
    {{-- Bouton flottant --}}
    <button wire:click="toggle"
            style="position:fixed;bottom:20px;right:20px;z-index:9999;width:60px;height:60px;border-radius:50%;background:#2563eb;color:white;border:none;box-shadow:0 4px 12px rgba(37,99,235,0.4);font-size:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform 0.2s;">
        🤖
    </button>

    {{-- Fenêtre de chat --}}
    @if($isOpen)
        <div style="position:fixed;bottom:100px;right:20px;z-index:9998;width:380px;max-width:calc(100vw-40px);background:white;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;max-height:500px;display:flex;flex-direction:column;">

            {{-- En-tête --}}
            <div style="background:#2563eb;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:white;font-weight:bold;font-size:16px;">🤖 Assistant IA</span>
                <div style="display:flex;gap:12px;align-items:center;">
                    @if($courseId)
                        <button wire:click="generateSummary"
                                style="background:transparent;border:none;color:white/70;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:4px;transition:color 0.2s;"
                                title="Synthèse du cours"
                                onmouseover="this.style.color='white'"
                                onmouseout="this.style.color='white/70'"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>📄</span>
                            <span wire:loading>⏳</span>
                        </button>
                    @endif
                    <button wire:click="clearHistory"
                            style="background:transparent;border:none;color:white/70;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:4px;transition:color 0.2s;"
                            title="Effacer la conversation"
                            onmouseover="this.style.color='white'"
                            onmouseout="this.style.color='white/70'">
                        <span>🗑️</span>
                    </button>
                    <button wire:click="toggle"
                            style="background:transparent;border:none;color:white/70;font-size:20px;cursor:pointer;transition:color 0.2s;"
                            onmouseover="this.style.color='white'"
                            onmouseout="this.style.color='white/70'">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div style="flex:1;overflow-y:auto;padding:16px;background:#f8fafc;max-height:300px;" id="chat-messages">
                @foreach($messages as $msg)
                    <div style="text-align:{{ $msg['role'] === 'user' ? 'right' : 'left' }};margin-bottom:8px;">
                        <span style="display:inline-block;padding:8px 14px;border-radius:12px;font-size:14px;max-width:80%;background:{{ $msg['role'] === 'user' ? '#2563eb' : 'white' }};color:{{ $msg['role'] === 'user' ? 'white' : '#1e293b' }};box-shadow:0 1px 3px rgba(0,0,0,0.1);white-space:pre-wrap;">
                            {{ $msg['content'] }}
                        </span>
                    </div>
                @endforeach

                @if($isLoading || $summaryLoading)
                    <div style="text-align:left;margin-bottom:8px;">
                        <span style="display:inline-block;padding:8px 14px;border-radius:12px;font-size:14px;background:#e2e8f0;color:#64748b;">
                            ⏳ L'assistant réfléchit...
                        </span>
                    </div>
                @endif
            </div>

            {{-- Suggestions --}}
            @if(count($suggestedQuestions) > 0)
                <div style="padding:8px 16px;background:#f1f5f9;border-top:1px solid #e2e8f0;display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($suggestedQuestions as $suggestion)
                        <button wire:click="askSuggestion('{{ addslashes($suggestion) }}')"
                                style="background:white;border:1px solid #e2e8f0;border-radius:20px;padding:4px 12px;font-size:12px;color:#1e293b;cursor:pointer;transition:all 0.2s;"
                                onmouseover="this.style.background='#e2e8f0'"
                                onmouseout="this.style.background='white'">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Input --}}
            <div style="padding:12px 16px;background:white;border-top:1px solid #e2e8f0;">
                <form wire:submit.prevent="ask" style="display:flex;gap:8px;">
                    <input type="text"
                           wire:model.defer="question"
                           placeholder="Écris ta question..."
                           style="flex:1;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;outline:none;transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='#2563eb'"
                           onblur="this.style.borderColor='#e2e8f0'">
                    <button type="submit"
                            style="background:#2563eb;color:white;border:none;padding:8px 16px;border-radius:8px;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:4px;"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Envoyer</span>
                        <span wire:loading>⏳</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Script --}}
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('scrollToBottom', function () {
                const container = document.getElementById('chat-messages');
                if (container) {
                    setTimeout(function() {
                        container.scrollTop = container.scrollHeight;
                    }, 100);
                }
            });
        });
    </script>
</div>