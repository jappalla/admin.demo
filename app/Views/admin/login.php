<main id="main-content" class="admin-shell mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <section class="panel admin-panel admin-auth relative overflow-hidden border border-white/10 bg-base-900/60 backdrop-blur-xl">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-16 -top-20 h-48 w-48 rounded-full bg-cyan-400/10 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-24 h-64 w-64 rounded-full bg-fuchsia-400/10 blur-3xl"></div>
        </div>

        <div class="relative">
            <span class="inline-flex items-center rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">
                Area Admin
            </span>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white">Accesso Dashboard</h1>
            <p class="intro mt-2 text-white/70">Accedi per gestire Esperienze e Competenze dal backend in modo sicuro.</p>

            <?php if (is_string($successMessage) && $successMessage !== ''): ?>
                <p class="alert success"><?php echo e($successMessage); ?></p>
            <?php endif; ?>

            <?php if (is_string($errorMessage) && $errorMessage !== ''): ?>
                <p class="alert error"><?php echo e($errorMessage); ?></p>
            <?php endif; ?>

            <form class="form-grid mt-6 rounded-2xl border border-white/10 bg-base-950/60 p-5 shadow-lg shadow-black/25" method="post" action="<?php echo e(route_url('admin/login')); ?>">
                <?php echo csrf_field(); ?>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" autocomplete="username" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <button class="button primary w-full sm:w-auto" type="submit">Login</button>
            </form>

            <p class="mt-4 text-sm text-white/50">
                <a href="<?php echo e(route_url('admin/forgot-password')); ?>" class="text-cyan-300 hover:text-cyan-200 transition-colors">
                    Password dimenticata?
                </a>
            </p>
        </div>
    </section>
</main>