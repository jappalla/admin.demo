<main id="main-content" class="admin-shell mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <section class="panel admin-panel admin-auth relative overflow-hidden border border-white/10 bg-base-900/60 backdrop-blur-xl">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-16 -top-20 h-48 w-48 rounded-full bg-cyan-400/10 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-24 h-64 w-64 rounded-full bg-fuchsia-400/10 blur-3xl"></div>
        </div>

        <div class="relative">
            <?php if (!$validToken): ?>
                <span class="inline-flex items-center rounded-full border border-red-300/20 bg-red-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-200">
                    Link non valido
                </span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white">Token scaduto</h1>
                <p class="intro mt-2 text-white/70">Il link di reset non è valido o è scaduto. Richiedi un nuovo link.</p>
                <p class="mt-4 text-sm">
                    <a href="<?php echo e(route_url('admin/forgot-password')); ?>" class="text-cyan-300 hover:text-cyan-200 transition-colors">
                        Richiedi nuovo link →
                    </a>
                </p>
            <?php else: ?>
                <span class="inline-flex items-center rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">
                    Reset Password
                </span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white">Nuova Password</h1>
                <p class="intro mt-2 text-white/70">Inserisci la tua nuova password.</p>

                <?php if (is_string($successMessage) && $successMessage !== ''): ?>
                    <p class="alert success"><?php echo e($successMessage); ?></p>
                <?php endif; ?>

                <?php if (is_string($errorMessage) && $errorMessage !== ''): ?>
                    <p class="alert error"><?php echo e($errorMessage); ?></p>
                <?php endif; ?>

                <form class="form-grid mt-6 rounded-2xl border border-white/10 bg-base-950/60 p-5 shadow-lg shadow-black/25" method="post" action="<?php echo e(route_url('admin/reset-password')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="token" value="<?php echo e($token); ?>">
                    <label>
                        <span>Nuova Password</span>
                        <input type="password" name="password" autocomplete="new-password" required minlength="8" placeholder="Minimo 8 caratteri">
                    </label>
                    <label>
                        <span>Conferma Password</span>
                        <input type="password" name="password_confirm" autocomplete="new-password" required minlength="8" placeholder="Ripeti la password">
                    </label>
                    <button class="button primary w-full sm:w-auto" type="submit">Reimposta Password</button>
                </form>
            <?php endif; ?>

            <p class="mt-4 text-sm text-white/50">
                <a href="<?php echo e(route_url('admin')); ?>" class="text-cyan-300 hover:text-cyan-200 transition-colors">
                    ← Torna al login
                </a>
            </p>
        </div>
    </section>
</main>