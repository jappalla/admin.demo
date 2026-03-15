<?php
declare(strict_types=1);

use App\Repositories\SettingsRepository;
use App\Support\Database;

require dirname(__DIR__) . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This command can run only in CLI mode.\n");
    exit(1);
}

$fresh = in_array('--fresh', $argv, true);
$pdo = Database::connection();
$settings = new SettingsRepository();

$demoExperiences = [
    [
        'role' => 'Lead Platform Engineer',
        'description' => 'Guida tecnica di prodotti web production-grade con focus performance, sicurezza e scalabilita.',
        'start_date' => '2023-01-01',
        'end_date' => null,
        'sort_order' => 1,
        'is_visible' => 1,
    ],
    [
        'role' => 'Senior Full Stack Developer',
        'description' => 'Sviluppo di dashboard amministrative, API e frontend orientati a UX/UI dark mode moderna.',
        'start_date' => '2020-01-01',
        'end_date' => '2022-12-31',
        'sort_order' => 2,
        'is_visible' => 1,
    ],
];

$demoSkills = [
    ['name' => 'PHP', 'category' => 'Backend', 'level' => 'Advanced', 'sort_order' => 1, 'is_visible' => 1],
    ['name' => 'JavaScript', 'category' => 'Frontend', 'level' => 'Advanced', 'sort_order' => 2, 'is_visible' => 1],
    ['name' => 'System Design', 'category' => 'Architecture', 'level' => 'Advanced', 'sort_order' => 3, 'is_visible' => 1],
    ['name' => 'SEO Tecnico', 'category' => 'Growth', 'level' => 'Intermediate', 'sort_order' => 4, 'is_visible' => 1],
];

if ($fresh) {
    $roles = array_map(static fn (array $item): string => $item['role'], $demoExperiences);
    $rolePlaceholders = implode(', ', array_fill(0, count($roles), '?'));
    $deleteExp = $pdo->prepare('DELETE FROM experiences WHERE role IN (' . $rolePlaceholders . ')');
    $deleteExp->execute($roles);

    $names = array_map(static fn (array $item): string => $item['name'], $demoSkills);
    $namePlaceholders = implode(', ', array_fill(0, count($names), '?'));
    $deleteSkills = $pdo->prepare('DELETE FROM skills WHERE name IN (' . $namePlaceholders . ')');
    $deleteSkills->execute($names);
}

$existsExperience = $pdo->prepare('SELECT id FROM experiences WHERE role = :role LIMIT 1');
$insertExperience = $pdo->prepare(
    'INSERT INTO experiences (role, description, start_date, end_date, sort_order, is_visible)
     VALUES (:role, :description, :start_date, :end_date, :sort_order, :is_visible)'
);

$createdExperiences = 0;
foreach ($demoExperiences as $experience) {
    $existsExperience->execute(['role' => $experience['role']]);
    $alreadyPresent = $existsExperience->fetchColumn();
    if ($alreadyPresent !== false) {
        continue;
    }

    $insertExperience->execute([
        'role' => $experience['role'],
        'description' => $experience['description'],
        'start_date' => $experience['start_date'],
        'end_date' => $experience['end_date'],
        'sort_order' => $experience['sort_order'],
        'is_visible' => $experience['is_visible'],
    ]);
    $createdExperiences++;
}

$existsSkill = $pdo->prepare('SELECT id FROM skills WHERE name = :name LIMIT 1');
$insertSkill = $pdo->prepare(
    'INSERT INTO skills (name, category, level, sort_order, is_visible)
     VALUES (:name, :category, :level, :sort_order, :is_visible)'
);

$createdSkills = 0;
foreach ($demoSkills as $skill) {
    $existsSkill->execute(['name' => $skill['name']]);
    $alreadyPresent = $existsSkill->fetchColumn();
    if ($alreadyPresent !== false) {
        continue;
    }

    $insertSkill->execute([
        'name' => $skill['name'],
        'category' => $skill['category'],
        'level' => $skill['level'],
        'sort_order' => $skill['sort_order'],
        'is_visible' => $skill['is_visible'],
    ]);
    $createdSkills++;
}

$settings->setMany([
    'profile_text' => 'Professionista full stack orientato a piattaforme moderne, scalabili e accessibili.',
    'contact_email' => 'contatti@antonio-trapasso.it',
    'contact_linkedin_label' => 'LinkedIn Professionale',
    'contact_linkedin_url' => 'https://www.linkedin.com/in/antonio-trapasso/',
    'contact_phone' => '+39 333 000 0000',
    'contact_intro' => 'Invia un messaggio istantaneo dal form per un contatto rapido.',
]);

fwrite(
    STDOUT,
    sprintf(
        "Demo seed completed. created_experiences=%d; created_skills=%d; fresh_mode=%s\n",
        $createdExperiences,
        $createdSkills,
        $fresh ? 'true' : 'false'
    )
);
