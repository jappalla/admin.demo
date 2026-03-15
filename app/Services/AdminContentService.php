<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ContactMessageRepository;
use App\Repositories\ExperienceRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SkillRepository;
use App\Support\Validator;
use DateTimeImmutable;
use InvalidArgumentException;

final class AdminContentService
{
    public function __construct(
        private readonly ExperienceRepository $experiences,
        private readonly SkillRepository $skills,
        private readonly SettingsRepository $settings,
        private readonly ContactMessageRepository $messages
    ) {}

    public function listExperiences(): array
    {
        return $this->experiences->all();
    }

    public function listPublishedExperiences(): array
    {
        return $this->experiences->visible();
    }

    public function createExperience(array $input): int
    {
        $payload = $this->validateExperiencePayload($input);
        return $this->experiences->create($payload);
    }

    public function updateExperience(int $id, array $input): bool
    {
        $normalizedId = Validator::integer($id, 'experience_id', 1);
        $payload = $this->validateExperiencePayload($input);
        return $this->experiences->update($normalizedId, $payload);
    }

    public function deleteExperience(int $id): bool
    {
        $normalizedId = Validator::integer($id, 'experience_id', 1);
        return $this->experiences->delete($normalizedId);
    }

    public function listSkills(): array
    {
        return $this->skills->all();
    }

    public function listPublishedSkills(): array
    {
        return $this->skills->visible();
    }

    public function createSkill(array $input): int
    {
        $payload = $this->validateSkillPayload($input);
        return $this->skills->create($payload);
    }

    public function updateSkill(int $id, array $input): bool
    {
        $normalizedId = Validator::integer($id, 'skill_id', 1);
        $payload = $this->validateSkillPayload($input);
        return $this->skills->update($normalizedId, $payload);
    }

    public function deleteSkill(int $id): bool
    {
        $normalizedId = Validator::integer($id, 'skill_id', 1);
        return $this->skills->delete($normalizedId);
    }

    public function profileContactSettings(): array
    {
        $defaults = $this->defaultProfileContactSettings();
        $saved = $this->settings->getByKeys(array_keys($defaults));

        return array_merge($defaults, $saved);
    }

    public function renderRichText(string $value): string
    {
        return $this->preserveHtmlFragment($value, false, 10000, 'content');
    }

    public function renderProfileHtml(string $profileText): string
    {
        return $this->preserveHtmlFragment($profileText, false, 10000, 'profile_text');
    }

    public function updateProfileContacts(array $input): void
    {
        $profileText = $this->preserveHtmlFragment((string) ($input['profile_text'] ?? ''), true, 10000, 'profile_text');
        $contactEmail = Validator::requiredString((string) ($input['contact_email'] ?? ''), 'contact_email', 190);
        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('contact_email is not a valid email address.');
        }

        $linkedinLabel = Validator::optionalString(
            $this->asNullableString($input['contact_linkedin_label'] ?? null),
            'contact_linkedin_label',
            120
        ) ?? 'Profilo professionale';

        $linkedinUrl = Validator::optionalString(
            $this->asNullableString($input['contact_linkedin_url'] ?? null),
            'contact_linkedin_url',
            255
        ) ?? '';
        if ($linkedinUrl !== '' && !filter_var($linkedinUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('contact_linkedin_url is not a valid URL.');
        }

        $contactPhone = Validator::optionalString(
            $this->asNullableString($input['contact_phone'] ?? null),
            'contact_phone',
            60
        ) ?? '';

        $contactIntroRaw = $this->asNullableString($input['contact_intro'] ?? null);
        $contactIntro = '';
        if (is_string($contactIntroRaw) && trim($contactIntroRaw) !== '') {
            $contactIntro = $this->preserveHtmlFragment($contactIntroRaw, true, 2000, 'contact_intro');
        }

        $this->settings->setMany([
            'profile_text' => $profileText,
            'contact_email' => $contactEmail,
            'contact_linkedin_label' => $linkedinLabel,
            'contact_linkedin_url' => $linkedinUrl,
            'contact_phone' => $contactPhone,
            'contact_intro' => $contactIntro,
        ]);
    }

    public function listContactMessages(int $limit = 30): array
    {
        return $this->messages->latest($limit);
    }

    public function createContactMessage(array $input): int
    {
        $fullName = Validator::requiredString((string) ($input['full_name'] ?? ''), 'full_name', 120);
        $email = Validator::requiredString((string) ($input['email'] ?? ''), 'email', 190);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('email is not a valid address.');
        }

        $subject = Validator::optionalString($this->asNullableString($input['subject'] ?? null), 'subject', 190);
        $message = Validator::requiredString((string) ($input['message'] ?? ''), 'message', 5000);

        return $this->messages->create([
            'full_name' => $fullName,
            'email' => strtolower($email),
            'subject' => $subject,
            'message' => $message,
            'status' => 'new',
        ]);
    }

    private function defaultProfileContactSettings(): array
    {
        return [
            'profile_text' => 'Professionista full stack con esperienza nella realizzazione di applicazioni web affidabili, ottimizzate per SEO, performance e manutenzione nel tempo.',
            'contact_email' => 'info@antonio-trapasso.it',
            'contact_linkedin_label' => 'Profilo professionale',
            'contact_linkedin_url' => 'https://www.linkedin.com/',
            'contact_phone' => '',
            'contact_intro' => 'Scrivimi direttamente dal form: riceverai una risposta nel minor tempo possibile.',
        ];
    }

    private function validateExperiencePayload(array $input): array
    {
        $role = Validator::requiredString((string) ($input['role'] ?? ''), 'role', 120);
        $description = $this->preserveHtmlFragment((string) ($input['description'] ?? ''), true, 5000, 'description');
        $startDate = $this->normalizeDate($input['start_date'] ?? null, 'start_date');
        $endDate = $this->normalizeDate($input['end_date'] ?? null, 'end_date');
        $sortOrder = Validator::integer((string) ($input['sort_order'] ?? '0'), 'sort_order', 0, 1000);
        $isVisible = $this->normalizeVisibility($input['is_visible'] ?? null);

        if ($startDate !== null && $endDate !== null && $endDate < $startDate) {
            throw new InvalidArgumentException('end_date must be greater than or equal to start_date.');
        }

        return [
            'role' => $role,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'sort_order' => $sortOrder,
            'is_visible' => $isVisible,
        ];
    }

    private function validateSkillPayload(array $input): array
    {
        $name = Validator::requiredString((string) ($input['name'] ?? ''), 'name', 120);
        $category = Validator::optionalString($this->asNullableString($input['category'] ?? null), 'category', 120);
        $level = Validator::optionalString($this->asNullableString($input['level'] ?? null), 'level', 50);
        $linkUrl = Validator::optionalString($this->asNullableString($input['link_url'] ?? null), 'link_url', 255);
        $sortOrder = Validator::integer((string) ($input['sort_order'] ?? '0'), 'sort_order', 0, 1000);
        $isVisible = $this->normalizeVisibility($input['is_visible'] ?? null);

        if ($linkUrl !== null) {
            if (!filter_var($linkUrl, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('link_url is not a valid URL.');
            }

            $scheme = strtolower((string) parse_url($linkUrl, PHP_URL_SCHEME));
            if (!in_array($scheme, ['http', 'https'], true)) {
                throw new InvalidArgumentException('link_url must use http or https scheme.');
            }
        }

        return [
            'name' => $name,
            'category' => $category,
            'level' => $level,
            'link_url' => $linkUrl,
            'sort_order' => $sortOrder,
            'is_visible' => $isVisible,
        ];
    }

    private function normalizeDate(mixed $value, string $fieldName): ?string
    {
        $stringValue = $this->asNullableString($value);
        $normalized = Validator::optionalString($stringValue, $fieldName, 10);
        if ($normalized === null) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $normalized);
        if ($date === false || $date->format('Y-m-d') !== $normalized) {
            throw new InvalidArgumentException($fieldName . ' must match Y-m-d format.');
        }

        return $normalized;
    }

    private function normalizeVisibility(mixed $value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }

    private function asNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private function preserveHtmlFragment(
        string $value,
        bool $strict,
        int $maxLength = 10000,
        string $fieldName = 'content'
    ): string {
        $trimmed = trim($value);
        if ($trimmed === '') {
            if ($strict) {
                throw new InvalidArgumentException($fieldName . ' is required.');
            }

            return '';
        }

        if (strlen($value) > $maxLength) {
            if ($strict) {
                throw new InvalidArgumentException($fieldName . ' exceeds max length of ' . $maxLength . '.');
            }

            $value = substr($value, 0, $maxLength);
        }

        // Sanitize HTML: allow safe tags only, strip <script>, <style>, event attributes
        $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><span><div><blockquote><code><pre><hr><table><thead><tbody><tr><th><td><img><figure><figcaption><small><sub><sup><mark><del><ins>';
        $sanitized = strip_tags($value, $allowedTags);

        // Remove dangerous attributes (on* event handlers, javascript: hrefs)
        $sanitized = (string) preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $sanitized);
        $sanitized = (string) preg_replace('/\s+on\w+\s*=\s*\S+/i', '', $sanitized);
        $sanitized = (string) preg_replace('/href\s*=\s*["\']?\s*javascript\s*:[^"\']*["\']?/i', 'href="#"', $sanitized);

        return $sanitized;
    }
}
