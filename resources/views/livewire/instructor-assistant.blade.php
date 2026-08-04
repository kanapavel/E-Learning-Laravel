<div>
    {{-- Bouton flottant (visible uniquement pour les instructeurs) --}}
    @if(auth()->check() && auth()->user()->isInstructor())
        <button wire:click="openAssistant"
                style="position:fixed;bottom:100px;right:20px;z-index:9999;width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg, #7c3aed, #4f46e5);color:white;border:none;box-shadow:0 4px 12px rgba(124,58,237,0.4);font-size:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform 0.2s;"
                onmouseover="this.style.transform='scale(1.05)'"
                onmouseout="this.style.transform='scale(1)'">
            🎯
        </button>
    @endif

    {{-- Fenêtre principale --}}
    @if($isOpen && auth()->check() && auth()->user()->isInstructor())
        <div style="position:fixed;bottom:180px;right:20px;z-index:9998;width:420px;max-width:calc(100vw-40px);background:white;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;max-height:600px;display:flex;flex-direction:column;">

            {{-- En-tête --}}
            <div style="background:linear-gradient(135deg, #7c3aed, #4f46e5);padding:14px 20px;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:white;font-weight:bold;font-size:16px;">🎯 Assistant Instructeur</span>
                <button wire:click="$set('isOpen', false)" style="background:transparent;border:none;color:white/70;font-size:20px;cursor:pointer;">✕</button>
            </div>

            {{-- Tabs --}}
            <div style="display:flex;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                <button wire:click="switchTab('plan')"
                        style="flex:1;padding:10px 8px;font-size:12px;font-weight:600;border:none;background:{{ $activeTab === 'plan' ? 'white' : 'transparent' }};color:{{ $activeTab === 'plan' ? '#7c3aed' : '#64748b' }};border-bottom:{{ $activeTab === 'plan' ? '2px solid #7c3aed' : 'none' }};cursor:pointer;transition:all 0.2s;">
                    📋 Plan
                </button>
                <button wire:click="switchTab('content')"
                        style="flex:1;padding:10px 8px;font-size:12px;font-weight:600;border:none;background:{{ $activeTab === 'content' ? 'white' : 'transparent' }};color:{{ $activeTab === 'content' ? '#7c3aed' : '#64748b' }};border-bottom:{{ $activeTab === 'content' ? '2px solid #7c3aed' : 'none' }};cursor:pointer;transition:all 0.2s;">
                    ✍️ Contenu
                </button>
                <button wire:click="switchTab('quiz')"
                        style="flex:1;padding:10px 8px;font-size:12px;font-weight:600;border:none;background:{{ $activeTab === 'quiz' ? 'white' : 'transparent' }};color:{{ $activeTab === 'quiz' ? '#7c3aed' : '#64748b' }};border-bottom:{{ $activeTab === 'quiz' ? '2px solid #7c3aed' : 'none' }};cursor:pointer;transition:all 0.2s;">
                    📝 Quiz
                </button>
                <button wire:click="switchTab('optimize')"
                        style="flex:1;padding:10px 8px;font-size:12px;font-weight:600;border:none;background:{{ $activeTab === 'optimize' ? 'white' : 'transparent' }};color:{{ $activeTab === 'optimize' ? '#7c3aed' : '#64748b' }};border-bottom:{{ $activeTab === 'optimize' ? '2px solid #7c3aed' : 'none' }};cursor:pointer;transition:all 0.2s;">
                    🚀 Optimiser
                </button>
            </div>

            {{-- Contenu des onglets --}}
            <div style="flex:1;overflow-y:auto;padding:16px;background:#f8fafc;max-height:450px;">

                @if($course)
                    {{-- ═══════ TAB PLANIFICATION ═══════ --}}
                    @if($activeTab === 'plan')
                        <div style="space-y:12px;">
                            <p style="font-size:12px;color:#64748b;">Générez un plan de cours à partir d'un sujet.</p>

                            <div style="margin-bottom:8px;">
                                <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Sujet *</label>
                                <input type="text" wire:model.defer="subject" placeholder="Ex: Programmation Python" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Niveau</label>
                                    <select wire:model.defer="level" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                        <option value="débutant">Débutant</option>
                                        <option value="intermédiaire">Intermédiaire</option>
                                        <option value="avancé">Avancé</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Durée</label>
                                    <input type="text" wire:model.defer="duration" placeholder="10 heures" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Public cible</label>
                                <input type="text" wire:model.defer="targetAudience" placeholder="Débutants, étudiants..." style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                            </div>

                            <button wire:click="generatePlan" wire:loading.attr="disabled" style="width:100%;padding:10px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                                <span wire:loading.remove>🚀 Générer le plan</span>
                                <span wire:loading>⏳ Génération...</span>
                            </button>

                            @if($generatedPlan)
                                <div style="background:#e8f5e9;padding:12px;border-radius:8px;margin-top:8px;">
                                    <h4 style="font-weight:bold;font-size:14px;color:#2e7d32;">{{ $generatedPlan['title'] }}</h4>
                                    <p style="font-size:12px;color:#1b5e20;margin-top:4px;">{{ Str::limit($generatedPlan['description'], 200) }}</p>
                                    <div style="display:flex;gap:8px;margin-top:8px;">
                                        <button wire:click="applyPlan" style="flex:1;padding:6px;background:#4caf50;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">✅ Appliquer</button>
                                        <button wire:click="$set('generatedPlan', null)" style="flex:1;padding:6px;background:#e0e0e0;color:#333;border:none;border-radius:6px;font-size:12px;cursor:pointer;">Annuler</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                    {{-- ═══════ TAB CONTENU ═══════ --}}
                    @elseif($activeTab === 'content')
                        <div style="space-y:12px;">
                            <p style="font-size:12px;color:#64748b;">Générez le contenu d'une leçon et des exercices.</p>

                            @if($chapters->isEmpty())
                                <div style="background:#fff3cd;padding:10px;border-radius:8px;font-size:12px;color:#856404;">⚠️ Aucun chapitre. Créez un plan de cours d'abord.</div>
                            @else
                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Chapitre *</label>
                                    <select wire:model="selectedChapter" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                        <option value="">Sélectionner...</option>
                                        @foreach($chapters as $chapter)
                                            <option value="{{ $chapter->id }}">{{ $chapter->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Titre de la leçon *</label>
                                    <input type="text" wire:model.defer="lessonTitle" placeholder="Ex: Les bases..." style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                    <div>
                                        <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Style</label>
                                        <select wire:model.defer="contentStyle" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                            <option value="éducatif">Éducatif</option>
                                            <option value="simple">Simple</option>
                                            <option value="technique">Technique</option>
                                            <option value="narratif">Narratif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Longueur</label>
                                        <select wire:model.defer="contentLength" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                            <option value="court">Court</option>
                                            <option value="moyen" selected>Moyen</option>
                                            <option value="long">Long</option>
                                        </select>
                                    </div>
                                </div>

                                <button wire:click="generateLessonContent" wire:loading.attr="disabled" style="width:100%;padding:10px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                                    <span wire:loading.remove>✍️ Générer le contenu</span>
                                    <span wire:loading>⏳ Génération...</span>
                                </button>

                                @if($generatedContent)
                                    <div style="background:#e8f5e9;padding:12px;border-radius:8px;margin-top:8px;max-height:200px;overflow-y:auto;">
                                        <h4 style="font-weight:bold;font-size:14px;color:#2e7d32;">{{ $lessonTitle }}</h4>
                                        <p style="font-size:12px;color:#1b5e20;">{{ Str::limit($generatedContent['introduction'] ?? '', 150) }}</p>
                                        @if($generatedExercises)
                                            <div style="font-size:12px;color:#1b5e20;margin-top:4px;">🏋️ {{ count($generatedExercises) }} exercices générés</div>
                                        @endif
                                        <button wire:click="createLesson" style="width:100%;margin-top:8px;padding:6px;background:#4caf50;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">📥 Créer la leçon</button>
                                    </div>
                                @endif
                            @endif
                        </div>

                    {{-- ═══════ TAB QUIZ ═══════ --}}
                    @elseif($activeTab === 'quiz')
                        <div style="space-y:12px;">
                            <p style="font-size:12px;color:#64748b;">Générez un quiz complet à partir d'un sujet.</p>

                            <div>
                                <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Sujet du quiz *</label>
                                <input type="text" wire:model.defer="quizTopic" placeholder="Ex: Les bases de Laravel" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Difficulté</label>
                                    <select wire:model.defer="quizDifficulty" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                        <option value="débutant">Débutant</option>
                                        <option value="moyen" selected>Moyen</option>
                                        <option value="avancé">Avancé</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:12px;font-weight:600;color:#1e293b;display:block;margin-bottom:4px;">Questions</label>
                                    <select wire:model.defer="quizCount" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>

                            <button wire:click="generateQuiz" wire:loading.attr="disabled" style="width:100%;padding:10px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                                <span wire:loading.remove>📝 Générer le quiz</span>
                                <span wire:loading>⏳ Génération...</span>
                            </button>

                            @if($generatedQuiz)
                                <div style="background:#e8f5e9;padding:12px;border-radius:8px;margin-top:8px;">
                                    <div style="font-size:12px;color:#2e7d32;">✅ {{ count($generatedQuiz) }} questions générées</div>
                                    <button wire:click="saveQuiz" style="width:100%;margin-top:8px;padding:6px;background:#4caf50;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">💾 Sauvegarder le quiz</button>
                                </div>
                            @endif
                        </div>

                    {{-- ═══════ TAB OPTIMISATION ═══════ --}}
                    @elseif($activeTab === 'optimize')
                        <div style="space-y:12px;">
                            <p style="font-size:12px;color:#64748b;">Obtenez des suggestions pour améliorer votre cours.</p>

                            <button wire:click="optimizeCourse" wire:loading.attr="disabled" style="width:100%;padding:10px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                                <span wire:loading.remove>🚀 Analyser et optimiser</span>
                                <span wire:loading>⏳ Analyse...</span>
                            </button>

                            @if($optimizationResults)
                                <div style="max-height:300px;overflow-y:auto;space-y:8px;">
                                    @foreach($optimizationResults as $category => $suggestions)
                                        <div style="background:white;padding:10px;border-radius:8px;border:1px solid #e2e8f0;">
                                            <h5 style="font-size:12px;font-weight:bold;color:#1e293b;text-transform:capitalize;margin-bottom:4px;">
                                                {{ $category }}
                                            </h5>
                                            <ul style="list-style:none;padding:0;margin:0;">
                                                @foreach($suggestions as $suggestion)
                                                    <li style="font-size:12px;color:#64748b;padding:2px 0;display:flex;gap:6px;">
                                                        <span style="color:#7c3aed;">•</span>
                                                        {{ $suggestion }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                @else
                    {{-- Pas de cours sélectionné --}}
                    <div style="text-align:center;padding:30px 0;">
                        <div style="font-size:48px;margin-bottom:16px;">📚</div>
                        <p style="font-size:14px;color:#64748b;">Ouvrez un cours existant ou</p>
                        <p style="font-size:14px;color:#64748b;">utilisez le planificateur pour en créer un.</p>
                        <a href="{{ route('instructor.courses.index') }}" style="display:inline-block;margin-top:12px;padding:8px 20px;background:#7c3aed;color:white;border-radius:8px;text-decoration:none;font-size:13px;">Voir mes cours →</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>