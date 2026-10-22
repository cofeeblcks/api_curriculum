# Curriculum Redesign (Panel/Web + Logo/Avatar) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rediseñar panel y web con sistema visual compartido (ink/teal + Fraunces/Manrope), y permitir subir logo (nav/footer/favicon) y avatar (Sobre mí) vía API storage.

**Architecture:** Extiende el CMS existente (Laravel Actions + ApiResponse estilo prime). Nuevos endpoints multipart en Profile; `ProfileResource` expone `logoUrl`/`avatarUrl`. Web y panel consumen las mismas URLs y comparten tokens CSS.

**Tech Stack:** Laravel 13, Sanctum, Pest, Storage public disk; Vite + React 19 + Tailwind 4 + Axios (panel y web).

**Spec:** [`docs/superpowers/specs/2026-10-21-curriculum-redesign-design.md`](../specs/2026-10-21-curriculum-redesign-design.md)

## Global Constraints

- Envelope API: `{ status, message, data, meta }` (camelCase en resources)
- Actions: clases `final` con `execute(...)`
- Logo: `png,jpg,jpeg,webp,svg` máx 2MB → `logo_path`
- Avatar: `png,jpg,jpeg,webp` máx 2MB → `avatar_path`
- Tokens: Ink `#071018` / `#12262F`, Sand `#F4F7F8`, Accent `#0D9488` / `#2DD4BF`
- Tipo: Fraunces (display) + Manrope (UI)
- Avatar **no** va en el hero; logo sí en nav/footer/favicon
- Sin repositories/DTOs; sin S3; sin apple-touch multi-size
- Mensajes de validación en español

## File map

| Área | Crear / modificar |
|------|-------------------|
| API | migración `avatar`→`avatar_path` + `logo_path`; Actions Upload/Delete; Requests; ProfileController methods; ProfileResource; routes; seeder; tests |
| Web | `index.css` tokens; Nav/Footer/About/Hero; `DocumentFavicon`; types; App |
| Panel | `index.css` tokens; AdminLayout/Login; ProfilePage uploaders; services; types |

---

### Task 1: Migración profile assets

**Files:**
- Create: `api/database/migrations/2026_10_21_200000_add_logo_and_rename_avatar_on_profiles_table.php`
- Modify: `api/app/Models/Profile.php`
- Modify: `api/database/seeders/CurriculumSeeder.php` (quitar `avatar` string si aplica)
- Test: `api/tests/Feature/PortfolioTest.php` (actualizar asserts si rompen)

**Interfaces:**
- Produces: columnas `logo_path`, `avatar_path` en `profiles`; fillable del modelo

- [ ] **Step 1: Escribir migración**

```php
Schema::table('profiles', function (Blueprint $table) {
    $table->string('logo_path')->nullable()->after('available');
});
// rename avatar -> avatar_path (SQLite/MariaDB):
Schema::table('profiles', function (Blueprint $table) {
    $table->renameColumn('avatar', 'avatar_path');
});
```

Si `renameColumn` falla en el driver, drop `avatar` + add `avatar_path`.

- [ ] **Step 2: Actualizar modelo**

Fillable: `logo_path`, `avatar_path` (quitar `avatar`).

- [ ] **Step 3: Migrar y verificar**

Run: `cd api && php artisan migrate --force`  
Expected: OK, columnas presentes.

- [ ] **Step 4: Commit**

```bash
cd api && git add database/migrations app/Models/Profile.php database/seeders/CurriculumSeeder.php
git commit -m "feat(api): add logo_path and avatar_path to profiles"
```

---

### Task 2: Upload/Delete Actions + endpoints (TDD)

**Files:**
- Create: `api/app/Actions/Profile/UploadProfileLogo.php`
- Create: `api/app/Actions/Profile/UploadProfileAvatar.php`
- Create: `api/app/Actions/Profile/DeleteProfileLogo.php`
- Create: `api/app/Actions/Profile/DeleteProfileAvatar.php`
- Create: `api/app/Http/Requests/UploadProfileLogoRequest.php`
- Create: `api/app/Http/Requests/UploadProfileAvatarRequest.php`
- Modify: `api/app/Http/Controllers/API/ProfileController.php`
- Modify: `api/app/Http/Resources/ProfileResource.php`
- Modify: `api/routes/api.php`
- Create: `api/tests/Feature/ProfileAssetTest.php`

**Interfaces:**
- Consumes: `Profile` singleton; `Storage::disk('public')`
- Produces:
  - `UploadProfileLogo::execute(UploadedFile $file): Profile`
  - `UploadProfileAvatar::execute(UploadedFile $file): Profile`
  - `DeleteProfileLogo::execute(): Profile`
  - `DeleteProfileAvatar::execute(): Profile`
  - Resource fields: `logoUrl: string|null`, `avatarUrl: string|null` (absolutas)
  - Routes: `POST/DELETE /api/profile/logo`, `POST/DELETE /api/profile/avatar`

- [ ] **Step 1: Test fallido de upload logo**

```php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('uploads a logo and returns logoUrl', function () {
    Storage::fake('public');
    Sanctum::actingAs(User::factory()->create());
    Profile::query()->create(['name' => 'Test', 'available' => true]);

    $file = UploadedFile::fake()->image('logo.png', 128, 128);

    $this->postJson('/api/profile/logo', ['logo' => $file])
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.logoUrl', fn ($url) => is_string($url) && str_contains($url, '/storage/'));

    Storage::disk('public')->assertExists(
        Profile::query()->first()->logo_path
    );
});
```

(Añadir tests similares para avatar, delete, validación 422.)

- [ ] **Step 2: Correr test — debe fallar**

Run: `cd api && php artisan test --compact tests/Feature/ProfileAssetTest.php`  
Expected: FAIL (ruta/método no existe)

- [ ] **Step 3: Implementar Actions**

Patrón:

```php
final class UploadProfileLogo
{
    public function execute(UploadedFile $file): Profile
    {
        $profile = Profile::query()->firstOrFail();
        if ($profile->logo_path) {
            Storage::disk('public')->delete($profile->logo_path);
        }
        $path = $file->store('profile', 'public');
        $profile->forceFill(['logo_path' => $path])->save();
        return $profile->fresh();
    }
}
```

Avatar igual con `avatar_path`. Deletes anulan path y borran archivo.

- [ ] **Step 4: Requests**

Logo: `'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048']`  
Avatar: `'avatar' => ['required', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048']`  
Mensajes ES: “Sube un PNG o SVG de hasta 2MB.” / equivalente avatar.

- [ ] **Step 5: Controller + Resource + routes**

```php
// ProfileResource
'logoUrl' => $this->logo_path ? url(Storage::url($this->logo_path)) : null,
'avatarUrl' => $this->avatar_path ? url(Storage::url($this->avatar_path)) : null,
// remove old 'avatar' key
```

```php
Route::post('profile/logo', [ProfileController::class, 'storeLogo']);
Route::delete('profile/logo', [ProfileController::class, 'destroyLogo']);
Route::post('profile/avatar', [ProfileController::class, 'storeAvatar']);
Route::delete('profile/avatar', [ProfileController::class, 'destroyAvatar']);
```

- [ ] **Step 6: storage:link + tests verdes**

Run: `cd api && php artisan storage:link && php artisan test --compact tests/Feature/ProfileAssetTest.php tests/Feature/PortfolioTest.php`  
Expected: PASS

- [ ] **Step 7: Commit**

```bash
cd api && git add app/Actions/Profile app/Http routes/api.php app/Models tests/Feature/ProfileAssetTest.php
git commit -m "feat(api): upload and delete profile logo and avatar"
```

---

### Task 3: Tokens CSS compartidos (web + panel)

**Files:**
- Modify: `web/src/index.css`
- Modify: `panel/src/index.css`
- Modify: `web/index.html` / `panel/index.html` (fonts Fraunces + Manrope si hace falta)

**Interfaces:**
- Produces: mismos `@theme` tokens Ink/Sand/Accent + `.font-display`

- [ ] **Step 1: Alinear tokens en ambos `index.css`** según spec (Ink 950/900, Sand 50, Accent `#0D9488` / `#2DD4BF`).

- [ ] **Step 2: Verificar build**

Run: `cd web && pnpm run build && cd ../panel && pnpm run build`  
Expected: exit 0

- [ ] **Step 3: Commit en cada repo**

```bash
cd web && git add src/index.css index.html && git commit -m "style: align brand tokens with redesign spec"
cd panel && git add src/index.css index.html && git commit -m "style: align brand tokens with redesign spec"
```

---

### Task 4: Web — logo, favicon, avatar, polish

**Files:**
- Modify: `web/src/lib/api/types/api.ts` (`logoUrl`, `avatarUrl`; quitar `avatar`)
- Create: `web/src/components/DocumentFavicon.tsx`
- Modify: `web/src/components/sections/Nav.tsx`
- Modify: `web/src/components/sections/Footer.tsx`
- Modify: `web/src/components/sections/About.tsx`
- Modify: `web/src/components/sections/Hero.tsx` (sin avatar; motion único)
- Modify: `web/src/App.tsx` (montar favicon)
- Optionally refine Skills/Experience/Education/Contact copy spacing (sin ALL-CAPS clutter)

**Interfaces:**
- Consumes: `portfolio.profile.logoUrl`, `portfolio.profile.avatarUrl`
- Produces: favicon dinámico; nav/footer con logo; About con avatar

- [ ] **Step 1: Actualizar tipos Profile**

```ts
logoUrl: string | null
avatarUrl: string | null
// remove avatar
```

- [ ] **Step 2: DocumentFavicon**

```tsx
useEffect(() => {
  const link = document.querySelector("link[rel='icon']") as HTMLLinkElement | null
  if (logoUrl && link) link.href = logoUrl
}, [logoUrl])
```

Fallback: dejar `/favicon.svg` del build si `logoUrl` es null.

- [ ] **Step 3: Nav/Footer usan `<img src={logoUrl}>` o iniciales**

- [ ] **Step 4: About muestra avatar (img circular/retrato) + bio + stats**

- [ ] **Step 5: Build**

Run: `cd web && pnpm run build`  
Expected: PASS

- [ ] **Step 6: Commit**

```bash
cd web && git add src && git commit -m "feat(web): logo favicon avatar and visual polish"
```

---

### Task 5: Panel — layout marca + uploaders

**Files:**
- Modify: `panel/src/lib/api/types/api.ts`
- Modify: `panel/src/lib/api/services/profile.ts` (upload/delete multipart)
- Modify: `panel/src/components/AdminLayout.tsx`
- Modify: `panel/src/pages/LoginPage.tsx`
- Modify: `panel/src/pages/ProfilePage.tsx`
- Create: `panel/src/components/AssetUploader.tsx` (opcional, reusable)
- Modify: remaining pages lightly for sand/ink consistency

**Interfaces:**
- Consumes: `profileService.uploadLogo(file)`, `uploadAvatar`, `deleteLogo`, `deleteAvatar`
- Produces: sidebar con logo; perfil con preview + file input

- [ ] **Step 1: Services multipart**

```ts
uploadLogo(file: File) {
  const body = new FormData()
  body.append('logo', file)
  return api.post('/profile/logo', body) // client already strips Content-Type for FormData if needed
}
```

Asegurar en `client.ts` que FormData no fuerce `application/json`.

- [ ] **Step 2: ProfilePage uploaders** — preview, input file, botones reemplazar/eliminar, errores `ApiError`.

- [ ] **Step 3: AdminLayout** — fondo ink sidebar, accent activo, logo desde `profileService.get()` (cargar una vez o context ligero).

- [ ] **Step 4: LoginPage** — misma marca (logo estático fallback / iniciales).

- [ ] **Step 5: Build**

Run: `cd panel && pnpm run build`  
Expected: PASS

- [ ] **Step 6: Commit**

```bash
cd panel && git add src && git commit -m "feat(panel): brand layout and logo/avatar uploaders"
```

---

### Task 6: Verificación end-to-end

**Files:** ninguno nuevo (smoke manual + tests)

- [ ] **Step 1: API tests**

Run: `cd api && php artisan test --compact`  
Expected: all PASS

- [ ] **Step 2: Smoke manual**

1. `php artisan serve` + `storage:link`
2. Panel login → Perfil → subir logo PNG → ver preview y sidebar
3. Subir avatar → preview circular
4. Web: logo en nav/footer, favicon en tab, avatar en Sobre mí
5. DELETE logo → fallback iniciales + favicon estático

- [ ] **Step 3: Actualizar READMEs** (api/panel/web) con nota de `storage:link` y formatos de upload

- [ ] **Step 4: Commit docs**

```bash
cd api && git add README.md docs/superpowers && git commit -m "docs: note storage link and asset upload for redesign"
```

---

## Self-review checklist

- [x] Spec coverage: tokens, uploads, web layout, panel layout, favicon, tests
- [x] No TBD placeholders
- [x] Types: `logoUrl`/`avatarUrl` consistentes API → panel → web
- [x] Avatar excluded from hero explicitly in Task 4
