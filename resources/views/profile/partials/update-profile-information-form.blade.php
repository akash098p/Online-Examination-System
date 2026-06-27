<section>
    @php
        $requestedDepartmentValue = $pendingAcademicRequest?->requested_department ?? $user->department;
        $requestedSemesterValue = $pendingAcademicRequest?->requested_semester ?? $user->semester;

        $fieldGroups = [
            [
                'title' => 'Personal Information',
                'description' => 'Core details used across the student workspace.',
                'fields' => [
                    ['id' => 'name', 'label' => 'Full Name', 'type' => 'text', 'value' => old('name', $user->name), 'required' => true, 'autocomplete' => 'name'],
                    ['id' => 'email', 'label' => 'Email Address', 'type' => 'email', 'value' => old('email', $user->email), 'required' => true, 'autocomplete' => 'username'],
                    ['id' => 'sex', 'label' => 'Sex', 'type' => 'select', 'value' => old('sex', $user->sex), 'options' => [
                        ['value' => '', 'label' => 'Select'],
                        ['value' => 'male', 'label' => 'Male'],
                        ['value' => 'female', 'label' => 'Female'],
                    ]],
                    ['id' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'value' => old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))],
                ],
            ],
            [
                'title' => 'Academic Information',
                'description' => 'Details that help with assignment, filtering, and academic context.',
                'fields' => [
                    ['id' => 'registration_no', 'label' => 'Registration Number', 'type' => 'text', 'value' => old('registration_no', $user->registration_no)],
                    ['id' => 'college_name', 'label' => 'College Name', 'type' => 'text', 'value' => old('college_name', $user->college_name)],
                    ['id' => 'department', 'label' => 'Department', 'type' => 'select', 'value' => old('department', $requestedDepartmentValue), 'options' => [
                        ['value' => '', 'label' => 'Select'],
                        ...collect(config('academix.departments', []))->map(fn ($department) => ['value' => $department, 'label' => $department])->all(),
                    ]],
                    ['id' => 'semester', 'label' => 'Semester', 'type' => 'select', 'value' => old('semester', $requestedSemesterValue), 'options' => [
                        ['value' => '', 'label' => 'Select'],
                        ...collect(config('academix.semesters', []))->map(fn ($semester) => ['value' => $semester, 'label' => $semester])->all(),
                    ]],
                ],
            ],
            [
                'title' => 'Contact Information',
                'description' => 'Ways the platform or admins can reach you when needed.',
                'fields' => [
                    ['id' => 'phone', 'label' => 'Phone Number', 'type' => 'text', 'value' => old('phone', $user->phone)],
                ],
            ],
        ];

        $hasProfileErrors = $errors->hasAny([
            'name',
            'email',
            'sex',
            'date_of_birth',
            'college_name',
            'department',
            'registration_no',
            'semester',
            'phone',
            'profile_photo',
            'remove_profile_photo',
        ]);
    @endphp

    <header class="hidden sm:flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Profile Details</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">
                {{ __('Manage your information') }}
            </h2>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center rounded-full border border-emerald-400/25 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                {{ $user->hasVerifiedEmail() ? 'Verified account' : 'Verification pending' }}
            </span>

            <button
                type="button"
                id="editBtn"
                class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400"
            >
                Edit Profile
            </button>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-8 space-y-8"
        enctype="multipart/form-data"
        id="profileForm"
        data-start-editing="{{ $hasProfileErrors ? 'true' : 'false' }}"
        data-user-role="{{ $user->role }}"
        data-current-department="{{ $user->department }}"
        data-current-semester="{{ $user->semester }}"
    >
        @csrf
        @method('patch')

        <div id="profileDetailsBody" class="{{ $hasProfileErrors ? '' : 'hidden' }} space-y-8">
            <div class="grid gap-6">
                <section class="profile-form-card">
                    <div class="profile-photo-layout">
                        <div>
                            <div class="flex items-center gap-4">
                                <img
                                    id="profilePhotoPreview"
                                    src="{{ $user->profilePhotoUrl() }}"
                                    data-original-src="{{ $user->profilePhotoUrl() }}"
                                    alt="Current Profile Photo"
                                    class="h-20 w-20 rounded-2xl object-cover ring-4 ring-white/10"
                                >

                                <div class="min-w-0">
                                    <h3 class="text-lg font-semibold text-white">Profile photo</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">Use a clean headshot or recognizable profile image for a more credible academic record.</p>
                                </div>
                            </div>
                        </div>

                        <div class="profile-upload-panel">
                            <x-input-label for="profile_photo" :value="__('Upload New Photo')" class="text-slate-200" />
                            <label for="profile_photo" id="profilePhotoTrigger" class="profile-upload-dropzone mt-2">
                                <span class="profile-upload-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                        <path d="M12 16V8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                                        <path d="M8.5 11.5L12 8l3.5 3.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                                        <path d="M5 16.5v.5A2 2 0 0 0 7 19h10a2 2 0 0 0 2-2v-.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                                    </svg>
                                </span>
                                <span class="profile-upload-copy">
                                    <span class="profile-upload-title">Choose a photo</span>
                                    <span id="profilePhotoFilename" class="profile-upload-filename">No file chosen</span>
                                </span>
                            </label>
                            <input
                                id="profile_photo"
                                name="profile_photo"
                                type="file"
                                class="profile-file-input profile-input"
                                accept=".jpg,.jpeg,.png"
                                disabled
                            >

                            @if($user->profile_photo)
                                <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-300">
                                    <input type="checkbox" name="remove_profile_photo" value="1" class="profile-input rounded border-white/20 bg-slate-900 text-cyan-400" disabled>
                                    Remove current photo
                                </label>
                            @endif

                            <p class="mt-3 text-xs text-slate-400">JPG or PNG, up to 2 MB.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                            <x-input-error class="mt-2" :messages="$errors->get('remove_profile_photo')" />
                        </div>
                    </div>
                </section>

            </div>

            @foreach ($fieldGroups as $group)
                <section class="profile-form-card">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-white">{{ $group['title'] }}</h3>
                    </div>

                    @if ($group['title'] === 'Academic Information' && $user->role === 'student' && $pendingAcademicRequest)
                        <div class="mb-5 rounded-2xl border border-amber-300/20 bg-amber-300/10 px-4 py-3 text-sm text-amber-100">
                            Pending admin approval for:
                            <span class="font-semibold">{{ $pendingAcademicRequest->requested_department ?: 'No department change' }}</span>
                            |
                            <span class="font-semibold">{{ $pendingAcademicRequest->requested_semester ?: 'No semester change' }}</span>
                        </div>
                    @elseif ($group['title'] === 'Academic Information' && $user->role === 'student')
                        <div class="mb-5 rounded-2xl border border-cyan-300/20 bg-cyan-300/10 px-4 py-3 text-sm text-cyan-100">
                            Department or semester changes will be sent to admin for approval before they are applied.
                        </div>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach ($group['fields'] as $field)
                            <div class="{{ count($group['fields']) === 1 ? 'sm:col-span-2' : '' }}">
                                <x-input-label :for="$field['id']" :value="__($field['label'])" class="text-slate-200" />
                                @if (($field['type'] ?? 'text') === 'select')
                                    <select
                                        id="{{ $field['id'] }}"
                                        name="{{ $field['id'] }}"
                                        class="profile-control profile-input mt-2 block w-full"
                                        data-profile-locked="{{ !empty($field['locked']) ? 'true' : 'false' }}"
                                        @if($field['id'] === 'name') autofocus @endif
                                        disabled
                                    >
                                        @foreach ($field['options'] as $option)
                                            <option value="{{ $option['value'] }}" @selected($field['value'] == $option['value'])>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <x-text-input
                                        :id="$field['id']"
                                        :name="$field['id']"
                                        :type="$field['type']"
                                        class="profile-control profile-input mt-2 block w-full"
                                        :value="$field['value']"
                                        :required="$field['required'] ?? false"
                                        :autocomplete="$field['autocomplete'] ?? null"
                                        :autofocus="$field['id'] === 'name'"
                                        :data-profile-locked="!empty($field['locked']) ? 'true' : 'false'"
                                        disabled
                                    />
                                @endif
                                <x-input-error class="mt-2" :messages="$errors->get($field['id'])" />

                                @if ($field['id'] === 'email' && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <p class="mt-3 text-sm text-amber-200">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification" class="font-semibold text-cyan-300 underline transition hover:text-cyan-200">
                                            {{ __('Resend verification email') }}
                                        </button>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div id="saveBox" class="hidden items-center justify-between gap-4 rounded-2xl border border-cyan-400/20 bg-cyan-400/8 px-4 py-4 sm:px-5">
                <div>
                    <p class="text-sm font-semibold text-white">Editing enabled</p>
                    <p class="text-sm text-slate-300">Review each field carefully before saving to keep your academic record accurate.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" id="cancelEditBtn" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/5">
                        Cancel
                    </button>

                    <x-primary-button class="!rounded-xl !border !border-amber-300 !bg-transparent !px-5 !py-2.5 !text-sm !font-semibold !text-amber-300 transition-all duration-300 hover:!bg-amber-300 hover:!text-slate-950 hover:!shadow-[0_0_16px_rgba(252,211,77,0.95),0_0_36px_rgba(252,211,77,0.75)] focus:!bg-amber-300 focus:!text-slate-950 focus:!shadow-[0_0_16px_rgba(252,211,77,0.95),0_0_36px_rgba(252,211,77,0.75)]">
                        {{ __('Save Changes') }}
                    </x-primary-button>
                </div>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <p class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ __('Profile updated successfully.') }}
            </p>
        @endif
    </form>

    <div id="academicChangeConfirmModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/65 px-4 py-6">
        <div class="w-full max-w-md rounded-3xl border border-white/15 bg-slate-900/95 p-6 shadow-2xl backdrop-blur-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Academic Change Request</p>
            <h3 class="mt-3 text-2xl font-semibold text-white">Admin approval required</h3>
            <p class="mt-3 text-sm leading-6 text-slate-300">
                Your department or semester change will be sent to admin for approval first. It will be updated only after admin approves your request.
            </p>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" id="cancelAcademicChangeRequestBtn" class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/5">
                    Cancel
                </button>
                <button type="button" id="confirmAcademicChangeRequestBtn" class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">
                    Submit Request
                </button>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("editBtn");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const form = document.getElementById("profileForm");
    const inputs = document.querySelectorAll(".profile-input");
    const saveBox = document.getElementById("saveBox");
    const profileDetailsBody = document.getElementById("profileDetailsBody");
    const profilePhotoInput = document.getElementById("profile_photo");
    const profilePhotoPreview = document.getElementById("profilePhotoPreview");
    const profilePhotoFilename = document.getElementById("profilePhotoFilename");
    const profilePhotoTrigger = document.getElementById("profilePhotoTrigger");
    const shouldStartEditing = form?.dataset.startEditing === "true";
    const departmentSelect = document.getElementById("department");
    const semesterSelect = document.getElementById("semester");
    const initialDepartmentValue = departmentSelect?.value || "";
    const initialSemesterValue = semesterSelect?.value || "";
    const academicChangeConfirmModal = document.getElementById("academicChangeConfirmModal");
    const confirmAcademicChangeRequestBtn = document.getElementById("confirmAcademicChangeRequestBtn");
    const cancelAcademicChangeRequestBtn = document.getElementById("cancelAcademicChangeRequestBtn");
    let allowAcademicChangeSubmit = false;
    let activeProfilePhotoPreviewUrl = null;

    if (!editBtn || !form || !saveBox) {
        return;
    }

    const setEditingState = (isEditing) => {
        inputs.forEach((input) => {
            if (input.dataset.profileLocked === "true") {
                input.setAttribute("disabled", "disabled");
                return;
            }

            if (isEditing) {
                input.removeAttribute("disabled");
            } else {
                input.setAttribute("disabled", "disabled");
            }
        });

        profilePhotoTrigger?.classList.toggle("is-disabled", !isEditing);
        profileDetailsBody?.classList.toggle("hidden", !isEditing);

        saveBox.classList.toggle("hidden", !isEditing);
        saveBox.classList.toggle("flex", isEditing);
        editBtn.textContent = isEditing ? "Editing..." : "Edit Profile";
        editBtn.disabled = isEditing;
        editBtn.classList.toggle("opacity-60", isEditing);
        editBtn.classList.toggle("cursor-not-allowed", isEditing);

        if (isEditing) {
            const profileForm = document.getElementById("profileForm");
            if (profileForm) {
                profileForm.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }
    };

    const heroEditBtn = document.getElementById("heroEditBtn");

    editBtn.addEventListener("click", () => {
        setEditingState(true);
    });

    heroEditBtn?.addEventListener("click", () => {
        setEditingState(true);
    });

    cancelEditBtn?.addEventListener("click", () => {
        form.reset();
        if (activeProfilePhotoPreviewUrl) {
            URL.revokeObjectURL(activeProfilePhotoPreviewUrl);
            activeProfilePhotoPreviewUrl = null;
        }
        if (profilePhotoPreview?.dataset.originalSrc) {
            profilePhotoPreview.src = profilePhotoPreview.dataset.originalSrc;
        }
        if (profilePhotoFilename) {
            profilePhotoFilename.textContent = "No file chosen";
        }
        setEditingState(false);
    });

    if (shouldStartEditing) {
        setEditingState(true);
    }

    profilePhotoInput?.addEventListener("change", () => {
        const file = profilePhotoInput.files?.[0];
        const fileName = file?.name || "No file chosen";

        if (profilePhotoFilename) {
            profilePhotoFilename.textContent = fileName;
        }

        if (activeProfilePhotoPreviewUrl) {
            URL.revokeObjectURL(activeProfilePhotoPreviewUrl);
            activeProfilePhotoPreviewUrl = null;
        }

        if (profilePhotoPreview && file) {
            activeProfilePhotoPreviewUrl = URL.createObjectURL(file);
            profilePhotoPreview.src = activeProfilePhotoPreviewUrl;
        } else if (profilePhotoPreview?.dataset.originalSrc) {
            profilePhotoPreview.src = profilePhotoPreview.dataset.originalSrc;
        }
    });

    form.addEventListener("submit", (event) => {
        if (form.dataset.userRole !== "student") {
            return;
        }

        if (allowAcademicChangeSubmit) {
            return;
        }

        const departmentChanged = departmentSelect && departmentSelect.value !== initialDepartmentValue;
        const semesterChanged = semesterSelect && semesterSelect.value !== initialSemesterValue;

        if (!departmentChanged && !semesterChanged) {
            return;
        }

        event.preventDefault();
        academicChangeConfirmModal?.classList.remove("hidden");
        academicChangeConfirmModal?.classList.add("flex");
    });

    confirmAcademicChangeRequestBtn?.addEventListener("click", () => {
        allowAcademicChangeSubmit = true;
        academicChangeConfirmModal?.classList.add("hidden");
        academicChangeConfirmModal?.classList.remove("flex");
        form.requestSubmit();
    });

    cancelAcademicChangeRequestBtn?.addEventListener("click", () => {
        academicChangeConfirmModal?.classList.add("hidden");
        academicChangeConfirmModal?.classList.remove("flex");
    });

    academicChangeConfirmModal?.addEventListener("click", (event) => {
        if (event.target !== academicChangeConfirmModal) {
            return;
        }

        academicChangeConfirmModal.classList.add("hidden");
        academicChangeConfirmModal.classList.remove("flex");
    });
});
</script>

<style>
    .profile-photo-layout {
        display: grid;
        gap: 1.5rem;
        align-items: center;
    }

    .profile-upload-panel {
        min-width: 0;
    }

    .profile-upload-dropzone {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
        border-radius: 20px;
        border: 1px dashed rgba(103, 232, 249, 0.24);
        background:
            linear-gradient(180deg, rgba(34, 211, 238, 0.08), rgba(255, 255, 255, 0.03)),
            rgba(2, 6, 23, 0.34);
        padding: 1rem 1.1rem;
        color: rgb(226 232 240);
        cursor: pointer;
        transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
    }

    .profile-upload-dropzone:hover {
        border-color: rgba(103, 232, 249, 0.4);
        transform: translateY(-1px);
    }

    .profile-upload-dropzone.is-disabled {
        cursor: not-allowed;
        opacity: 0.72;
        transform: none;
    }

    .profile-upload-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        background: rgba(34, 211, 238, 0.12);
        border: 1px solid rgba(103, 232, 249, 0.2);
        color: rgb(165 243 252);
        flex-shrink: 0;
    }

    .profile-upload-copy {
        display: grid;
        gap: 0.2rem;
        min-width: 0;
    }

    .profile-upload-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: white;
    }

    .profile-upload-filename {
        font-size: 0.9rem;
        color: rgb(191 219 254);
        word-break: break-word;
    }

    .profile-file-input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .profile-form-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.025)),
            rgba(15, 23, 42, 0.22);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.14),
            0 16px 34px rgba(2, 6, 23, 0.16);
        border-radius: 24px;
        padding: 1.25rem;
    }

    .profile-form-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), transparent 32%);
        pointer-events: none;
    }

    .profile-control {
        border-radius: 16px !important;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02)),
            rgba(2, 6, 23, 0.4) !important;
        color: rgb(241 245 249) !important;
        min-height: 3rem;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.08),
            0 8px 20px rgba(2, 6, 23, 0.12) !important;
    }

    .profile-control:disabled,
    .profile-input:disabled {
        cursor: not-allowed;
        opacity: 0.72;
    }

    .profile-control::placeholder {
        color: rgb(148 163 184);
    }

    @media (min-width: 1024px) {
        .profile-photo-layout {
            grid-template-columns: minmax(0, 0.95fr) minmax(20rem, 1.05fr);
        }
    }
</style>
