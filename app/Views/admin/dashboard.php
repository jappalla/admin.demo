<div class="admin-layout">
    <!-- ═══ Sidebar ═══ -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <span class="sidebar-brand">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6" />
                    <polyline points="8 6 2 12 8 18" />
                </svg>
                <span>Admin<span style="opacity:.45">.dev</span></span>
            </span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Chiudi menu">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <p class="sidebar-section">Profilo</p>
            <button class="sidebar-link active" data-tab="settings" onclick="switchTab('settings')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" />
                </svg>
                Profilo e Contatti
            </button>

            <p class="sidebar-section">Esperienze</p>
            <button class="sidebar-link" data-tab="exp-create" onclick="switchTab('exp-create')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Aggiungi Esperienza
            </button>
            <button class="sidebar-link" data-tab="exp-list" onclick="switchTab('exp-list')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7H4a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1Z" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                </svg>
                Esperienze esistenti
                <span class="sidebar-count"><?php echo count($experiences); ?></span>
            </button>

            <p class="sidebar-section">Competenze</p>
            <button class="sidebar-link" data-tab="skill-create" onclick="switchTab('skill-create')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Aggiungi Competenza
            </button>
            <button class="sidebar-link" data-tab="skill-list" onclick="switchTab('skill-list')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                Competenze esistenti
                <span class="sidebar-count"><?php echo count($skills); ?></span>
            </button>

            <p class="sidebar-section">Comunicazioni</p>
            <button class="sidebar-link" data-tab="messages" onclick="switchTab('messages')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                Messaggi
                <?php $totalMsgCount = (int) ($msgTotal ?? count($messages));
                if ($totalMsgCount > 0): ?>
                    <span class="sidebar-badge"><?php echo $totalMsgCount; ?></span>
                <?php endif; ?>
            </button>

            <p class="sidebar-section">Link</p>
            <a class="sidebar-link" href="<?php echo e(route_url('')); ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    <polyline points="15 3 21 3 21 9" />
                    <line x1="10" x2="21" y1="14" y2="3" />
                </svg>
                Vai al Frontend
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span><?php echo e((string) ($user['email'] ?? '')); ?></span>
            </div>
            <form method="post" action="<?php echo e(route_url('admin/logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="sidebar-logout" type="submit">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" x2="9" y1="12" y2="12" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ═══ Main content ═══ -->
    <main id="main-content" class="admin-main">
        <!-- Mobile hamburger -->
        <button class="sidebar-hamburger" onclick="toggleSidebar()" aria-label="Apri menu">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="3" x2="21" y1="12" y2="12" />
                <line x1="3" x2="21" y1="6" y2="6" />
                <line x1="3" x2="21" y1="18" y2="18" />
            </svg>
        </button>

        <!-- Top Bar -->
        <section class="panel admin-panel relative overflow-hidden border border-white/10 bg-base-900/60 backdrop-blur-xl">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-20 -top-20 h-56 w-56 rounded-full bg-cyan-400/10 blur-3xl"></div>
                <div class="absolute -right-20 -bottom-20 h-60 w-60 rounded-full bg-indigo-400/10 blur-3xl"></div>
            </div>

            <div class="admin-topbar relative">
                <div>
                    <p class="eyebrow">Dashboard Admin</p>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white">Gestione Contenuti</h1>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="admin-stats">
                <div class="stat-card">
                    <span class="stat-icon" style="background:rgba(6,182,212,.15);color:#22d3ee">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7H4a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1Z" />
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                        </svg>
                    </span>
                    <div>
                        <p class="stat-value"><?php echo count($experiences); ?></p>
                        <p class="stat-label">Esperienze</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon" style="background:rgba(139,92,246,.15);color:#a78bfa">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </span>
                    <div>
                        <p class="stat-value"><?php echo count($skills); ?></p>
                        <p class="stat-label">Competenze</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon" style="background:rgba(16,185,129,.15);color:#34d399">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </span>
                    <div>
                        <p class="stat-value"><?php echo (int) ($msgTotal ?? count($messages)); ?></p>
                        <p class="stat-label">Messaggi</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB: Settings -->
        <div class="tab-content active" id="tab-settings">
            <div class="mb-4 flex items-center gap-3">
                <span class="inline-flex rounded-full border border-fuchsia-300/20 bg-fuchsia-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-fuchsia-200">Home Settings</span>
                <h2 class="m-0 text-2xl font-bold text-white">Profilo e Contatti (Home)</h2>
            </div>
            <form class="form-grid rounded-2xl border border-white/10 bg-base-950/60 p-5" method="post" action="<?php echo e(route_url('admin/settings/update')); ?>">
                <?php echo csrf_field(); ?>
                <label class="wide">
                    <span>Testo Profilo (Home, supporta HTML)</span>
                    <textarea name="profile_text" rows="7" required><?php echo e((string) ($settings['profile_text'] ?? '')); ?></textarea>
                    <small>Tag consentiti: codice HTML completo (classi Tailwind incluse, markup preservato integralmente).</small>
                </label>
                <label>
                    <span>Email Contatto</span>
                    <input type="email" name="contact_email" maxlength="190" value="<?php echo e((string) ($settings['contact_email'] ?? '')); ?>" required>
                </label>
                <label>
                    <span>Telefono</span>
                    <input type="text" name="contact_phone" maxlength="60" value="<?php echo e((string) ($settings['contact_phone'] ?? '')); ?>">
                </label>
                <label>
                    <span>LinkedIn Label</span>
                    <input type="text" name="contact_linkedin_label" maxlength="120" value="<?php echo e((string) ($settings['contact_linkedin_label'] ?? '')); ?>">
                </label>
                <label>
                    <span>LinkedIn URL</span>
                    <input type="url" name="contact_linkedin_url" maxlength="255" value="<?php echo e((string) ($settings['contact_linkedin_url'] ?? '')); ?>">
                </label>
                <label class="wide">
                    <span>Testo Intro Form Contatti (supporta HTML)</span>
                    <textarea name="contact_intro" rows="2" maxlength="2000"><?php echo e((string) ($settings['contact_intro'] ?? '')); ?></textarea>
                </label>
                <button class="button primary w-full sm:w-auto" type="submit">Salva Profilo/Contatti</button>
            </form>

            <?php if (is_string($successMessage ?? null) && $successMessage !== ''): ?>
                <p class="alert success"><?php echo e($successMessage); ?></p>
            <?php endif; ?>
            <?php if (is_string($errorMessage ?? null) && $errorMessage !== ''): ?>
                <p class="alert error"><?php echo e($errorMessage); ?></p>
            <?php endif; ?>
        </div>

        <!-- TAB: Add Experience -->
        <div class="tab-content" id="tab-exp-create">
            <section class="panel admin-panel border border-white/10 bg-base-900/55">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex rounded-full border border-accent-blue/20 bg-accent-blue/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-200">Create</span>
                    <h2 class="m-0 text-2xl font-bold text-white">Aggiungi Esperienza</h2>
                </div>
                <form class="form-grid compact rounded-2xl border border-white/10 bg-base-950/60 p-5" method="post" action="<?php echo e(route_url('admin/experience/create')); ?>">
                    <?php echo csrf_field(); ?>
                    <label>
                        <span>Ruolo</span>
                        <input type="text" name="role" required maxlength="120">
                    </label>
                    <label>
                        <span>Data inizio</span>
                        <input type="date" name="start_date">
                    </label>
                    <label>
                        <span>Data fine</span>
                        <input type="date" name="end_date">
                    </label>
                    <label>
                        <span>Ordinamento</span>
                        <input type="number" name="sort_order" min="0" value="0">
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_visible" value="1" checked>
                        <span>Pubblicata</span>
                    </label>
                    <label class="wide">
                        <span>Descrizione (supporta HTML)</span>
                        <textarea name="description" rows="3" required></textarea>
                    </label>
                    <button class="button primary w-full sm:w-auto" type="submit">Aggiungi Esperienza</button>
                </form>
            </section>
        </div>

        <!-- TAB: Experiences List -->
        <div class="tab-content" id="tab-exp-list">
            <section class="panel admin-panel border border-white/10 bg-base-900/55">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">Table</span>
                    <h2 class="m-0 text-2xl font-bold text-white">Esperienze esistenti</h2>
                </div>
                <div class="table-wrap rounded-2xl border border-white/10 bg-base-950/60 p-2">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ruolo</th>
                                <th>Date</th>
                                <th>Ord.</th>
                                <th>Vis.</th>
                                <th>Azione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($experiences === []): ?>
                                <tr>
                                    <td colspan="6">Nessuna esperienza registrata.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($experiences as $experience): ?>
                                <tr>
                                    <td><?php echo e((string) $experience['id']); ?></td>
                                    <td>
                                        <form class="inline-form" method="post" action="<?php echo e(route_url('admin/experience/update')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e((string) $experience['id']); ?>">
                                            <input type="text" name="role" value="<?php echo e((string) $experience['role']); ?>" required maxlength="120">
                                            <textarea name="description" rows="2" required title="Descrizione (supporta HTML)"><?php echo e((string) $experience['description']); ?></textarea>
                                    </td>
                                    <td>
                                        <input type="date" name="start_date" value="<?php echo e((string) ($experience['start_date'] ?? '')); ?>">
                                        <input type="date" name="end_date" value="<?php echo e((string) ($experience['end_date'] ?? '')); ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="sort_order" min="0" value="<?php echo e((string) $experience['sort_order']); ?>">
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="is_visible"
                                            value="1"
                                            <?php echo ((int) $experience['is_visible'] === 1) ? 'checked' : ''; ?>>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="button secondary" type="submit">Salva</button>
                                        </form>
                                        <form
                                            method="post"
                                            action="<?php echo e(route_url('admin/experience/delete')); ?>"
                                            data-confirm="Confermi eliminazione esperienza?">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e((string) $experience['id']); ?>">
                                            <button class="button danger" type="submit">Elimina</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- TAB: Add Skill -->
        <div class="tab-content" id="tab-skill-create">
            <section class="panel admin-panel border border-white/10 bg-base-900/55">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-200">Create</span>
                    <h2 class="m-0 text-2xl font-bold text-white">Aggiungi Competenza</h2>
                </div>
                <form class="form-grid compact rounded-2xl border border-white/10 bg-base-950/60 p-5" method="post" action="<?php echo e(route_url('admin/skill/create')); ?>">
                    <?php echo csrf_field(); ?>
                    <label>
                        <span>Nome</span>
                        <input type="text" name="name" required maxlength="120">
                    </label>
                    <label>
                        <span>Categoria</span>
                        <input type="text" name="category" maxlength="120">
                    </label>
                    <label>
                        <span>Livello</span>
                        <input type="text" name="level" maxlength="50">
                    </label>
                    <label>
                        <span>Link competenza</span>
                        <input type="url" name="link_url" maxlength="255" placeholder="https://...">
                    </label>
                    <label>
                        <span>Ordinamento</span>
                        <input type="number" name="sort_order" min="0" value="0">
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_visible" value="1" checked>
                        <span>Pubblicata</span>
                    </label>
                    <button class="button primary w-full sm:w-auto" type="submit">Aggiungi Competenza</button>
                </form>
            </section>
        </div>

        <!-- TAB: Skills List -->
        <div class="tab-content" id="tab-skill-list">
            <section class="panel admin-panel border border-white/10 bg-base-900/55">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">Table</span>
                    <h2 class="m-0 text-2xl font-bold text-white">Competenze esistenti</h2>
                </div>
                <div class="table-wrap rounded-2xl border border-white/10 bg-base-950/60 p-2">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Livello</th>
                                <th>Link</th>
                                <th>Ord.</th>
                                <th>Vis.</th>
                                <th>Azione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($skills === []): ?>
                                <tr>
                                    <td colspan="8">Nessuna competenza registrata.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($skills as $skill): ?>
                                <tr>
                                    <td><?php echo e((string) $skill['id']); ?></td>
                                    <td>
                                        <form class="inline-form" method="post" action="<?php echo e(route_url('admin/skill/update')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e((string) $skill['id']); ?>">
                                            <input type="text" name="name" value="<?php echo e((string) $skill['name']); ?>" required maxlength="120">
                                    </td>
                                    <td><input type="text" name="category" value="<?php echo e((string) ($skill['category'] ?? '')); ?>" maxlength="120"></td>
                                    <td><input type="text" name="level" value="<?php echo e((string) ($skill['level'] ?? '')); ?>" maxlength="50"></td>
                                    <td><input type="url" name="link_url" value="<?php echo e((string) ($skill['link_url'] ?? '')); ?>" maxlength="255" placeholder="https://..."></td>
                                    <td><input type="number" name="sort_order" min="0" value="<?php echo e((string) $skill['sort_order']); ?>"></td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="is_visible"
                                            value="1"
                                            <?php echo ((int) $skill['is_visible'] === 1) ? 'checked' : ''; ?>>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="button secondary" type="submit">Salva</button>
                                        </form>
                                        <form
                                            method="post"
                                            action="<?php echo e(route_url('admin/skill/delete')); ?>"
                                            data-confirm="Confermi eliminazione competenza?">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e((string) $skill['id']); ?>">
                                            <button class="button danger" type="submit">Elimina</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- TAB: Messages -->
        <div class="tab-content" id="tab-messages">
            <section class="panel admin-panel border border-white/10 bg-base-900/55">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex rounded-full border border-violet-300/20 bg-violet-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-violet-200">Inbox</span>
                    <h2 class="m-0 text-2xl font-bold text-white">Messaggi istantanei ricevuti</h2>
                    <span class="ml-auto text-sm text-white/50"><?php echo (int) ($msgTotal ?? 0); ?> totali</span>
                </div>
                <div class="table-wrap rounded-2xl border border-white/10 bg-base-950/60 p-2">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Oggetto</th>
                                <th>Messaggio</th>
                                <th>Stato</th>
                                <th>Data</th>
                                <th>Azione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($messages === []): ?>
                                <tr>
                                    <td colspan="8">Nessun messaggio ricevuto.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($messages as $message): ?>
                                <tr class="<?php echo ($message['status'] ?? '') === 'new' ? 'bg-violet-400/5' : ''; ?>">
                                    <td><?php echo e((string) $message['id']); ?></td>
                                    <td><?php echo e((string) $message['full_name']); ?></td>
                                    <td><?php echo e((string) $message['email']); ?></td>
                                    <td><?php echo e((string) ($message['subject'] ?? '')); ?></td>
                                    <td class="max-w-xs truncate" title="<?php echo e((string) $message['message']); ?>"><?php echo e((string) $message['message']); ?></td>
                                    <td>
                                        <?php if (($message['status'] ?? '') === 'new'): ?>
                                            <span class="inline-flex rounded-full bg-amber-400/15 px-2 py-0.5 text-xs font-medium text-amber-300">Nuovo</span>
                                        <?php else: ?>
                                            <span class="inline-flex rounded-full bg-emerald-400/15 px-2 py-0.5 text-xs font-medium text-emerald-300">Letto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="whitespace-nowrap"><?php echo e((string) $message['created_at']); ?></td>
                                    <td class="actions-cell">
                                        <?php if (($message['status'] ?? '') === 'new'): ?>
                                            <form method="post" action="<?php echo e(route_url('admin/message/read')); ?>" class="inline-form">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e((string) $message['id']); ?>">
                                                <button class="button secondary" type="submit" title="Segna come letto">&#10003;</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" action="<?php echo e(route_url('admin/message/delete')); ?>" data-confirm="Confermi eliminazione messaggio?" class="inline-form">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e((string) $message['id']); ?>">
                                            <button class="button danger" type="submit" title="Elimina">&#10005;</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (($msgTotalPages ?? 1) > 1): ?>
                    <nav class="mt-4 flex items-center justify-center gap-2">
                        <?php if (($msgPage ?? 1) > 1): ?>
                            <a href="<?php echo e(route_url('admin/panel') . '?msg_page=' . (($msgPage ?? 1) - 1)); ?>" class="button secondary px-3 py-1 text-sm" onclick="setTimeout(function(){switchTab('messages')},50)">&laquo; Prec</a>
                        <?php endif; ?>
                        <span class="text-sm text-white/60">Pagina <?php echo (int) ($msgPage ?? 1); ?> di <?php echo (int) ($msgTotalPages ?? 1); ?></span>
                        <?php if (($msgPage ?? 1) < ($msgTotalPages ?? 1)): ?>
                            <a href="<?php echo e(route_url('admin/panel') . '?msg_page=' . (($msgPage ?? 1) + 1)); ?>" class="button secondary px-3 py-1 text-sm" onclick="setTimeout(function(){switchTab('messages')},50)">Succ &raquo;</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </section>
        </div>

        <script>
            function switchTab(tabName) {
                document.querySelectorAll('.tab-content').forEach(function(el) {
                    el.classList.remove('active');
                });
                document.querySelectorAll('.sidebar-link').forEach(function(el) {
                    el.classList.remove('active');
                });
                var target = document.getElementById('tab-' + tabName);
                if (target) target.classList.add('active');
                var btn = document.querySelector('.sidebar-link[data-tab="' + tabName + '"]');
                if (btn) btn.classList.add('active');
                // Close mobile sidebar after selection
                if (window.innerWidth < 1024) toggleSidebar(false);
            }

            function toggleSidebar(forceState) {
                var sidebar = document.getElementById('adminSidebar');
                var overlay = document.getElementById('sidebarOverlay');
                var isOpen = sidebar.classList.contains('open');
                var newState = typeof forceState === 'boolean' ? forceState : !isOpen;
                sidebar.classList.toggle('open', newState);
                overlay.classList.toggle('open', newState);
                document.body.style.overflow = newState ? 'hidden' : '';
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form[data-confirm]').forEach(function(f) {
                    f.addEventListener('submit', function(e) {
                        if (!confirm(f.getAttribute('data-confirm'))) e.preventDefault();
                    });
                });
                // Auto-switch to messages tab when msg_page param is present
                var params = new URLSearchParams(window.location.search);
                if (params.has('msg_page')) {
                    switchTab('messages');
                }
            });
        </script>
    </main>
</div>