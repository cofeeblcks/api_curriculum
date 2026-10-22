# Curriculum — Rediseño panel/web + logo/avatar

**Fecha:** 2026-10-21  
**Estado:** Pendiente de revisión del usuario  
**Alcance:** Rediseño profesional de `web` y `panel`, sistema visual compartido, upload de logo y avatar; logo también como favicon.

## Decisiones acordadas

| Tema | Decisión |
|------|----------|
| Relación panel ↔ web | Sistema visual compartido (misma marca, tipografía, tokens) |
| Assets | Logo de marca **y** foto de perfil (avatar) separados |
| Dirección visual | Refinar ink + teal actual (más pulido, menos template) |
| Enfoque | Design system unificado + uploads reales en API (storage público) |

## 1. Sistema visual compartido

**Job del producto:** portfolio de un desarrollador fullstack backend/DBA — confianza técnica, no estética SaaS genérica.

### Tokens

| Token | Hex | Uso |
|-------|-----|-----|
| Ink 950 | `#071018` | Fondos oscuros, sidebar del panel |
| Ink 900 | `#12262F` | Superficies elevadas en oscuro |
| Sand 50 | `#F4F7F8` | Fondos claros (main del panel, secciones claras web) |
| Accent | `#0D9488` | CTAs, nav activa, foco |
| Accent soft | `#2DD4BF` | Highlights sobre ink |
| Line dark | `rgba(255,255,255,0.08)` | Bordes en oscuro |
| Line light | `rgba(7,16,24,0.10)` | Bordes en claro |

### Tipografía

- **Display:** Fraunces — nombre en hero, títulos de sección (web), títulos de página (panel)
- **UI/cuerpo:** Manrope — nav, formularios, cuerpo, panel

### Principios

1. Un solo momento memorable: hero de la web (nombre + marca).
2. Panel = misma marca en densidad de herramienta (sidebar ink, contenido sand).
3. Sin card farm con sombra suave; sin ALL-CAPS eyebrow en cada bloque.
4. Motion mínimo: entrada del hero en web; en panel solo feedback de acciones. Respetar `prefers-reduced-motion`.

## 2. Logo, avatar y favicon

### Datos

En `profiles`:

- `logo_path` (nullable string) — archivo de marca
- `avatar_path` (nullable string) — foto de perfil

Migración: el campo actual `avatar` se renombra o se reemplaza por `avatar_path`; se añade `logo_path`. El seeder y resources se actualizan.

### Upload (API, patrón prime)

| Método | Ruta | Auth | Body |
|--------|------|------|------|
| `POST` | `/api/profile/logo` | `auth:sanctum` | multipart `logo` |
| `POST` | `/api/profile/avatar` | `auth:sanctum` | multipart `avatar` |
| `DELETE` | `/api/profile/logo` | `auth:sanctum` | — |
| `DELETE` | `/api/profile/avatar` | `auth:sanctum` | — |

- Actions `final` con `execute()`: validar, borrar archivo anterior si existe, guardar en `storage/app/public/profile/`, actualizar path, devolver modelo.
- Validación: logo `png,jpg,jpeg,webp,svg` máx 2MB; avatar `png,jpg,jpeg,webp` máx 2MB.
- Requiere `php artisan storage:link`.
- Envelope `{ status, message, data, meta }` con Resource camelCase: `logoUrl`, `avatarUrl` (URLs absolutas vía `Storage::url` + `APP_URL`).

### Consumo

**Web**

- Logo: nav + footer; fallback a iniciales si no hay logo.
- Favicon: `<link rel="icon" href={logoUrl}>` dinámico; si no hay logo, favicon estático del build.
- Avatar: sección Sobre mí (retrato); **no** overlay en el hero.

**Panel**

- Sidebar: logo (o iniciales) + “Curriculum”.
- Página Perfil: dos uploaders con preview (logo cuadrado, avatar circular), reemplazar/quitar.

## 3. Web (portfolio)

Secciones (orden fijo): Nav → Hero → Sobre mí → Skills → Experiencia → Formación → Contacto → Footer.

```text
[LOGO] nav ····· Contactar
HERO ink full-bleed: disponible?, NOMBRE, headline, CTAs
SOBRE MÍ sand: [avatar] + bio + stats
SKILLS ink: categorías + chips
EXPERIENCIA sand: timeline
FORMACIÓN ink: grid 2 cols
CONTACTO sand: datos | formulario
FOOTER: logo + redes + ©
```

Cambios vs. implementación actual: logo real + favicon dinámico; avatar en Sobre mí; menos labels tipográficos; un solo motion de entrada en hero.

## 4. Panel (admin)

```text
Sidebar ink          | Main sand
[LOGO] Curriculum    | Título (Fraunces)
nav activa accent    | Contenido denso, sin card farm
user + logout        | Perfil: uploads logo/avatar + form
```

- Login: marca sobre ink, formulario elevado.
- Errores/vacíos concretos en español (“Sube un PNG o SVG de hasta 2MB”).

## 5. Arquitectura y flujo

```text
Panel (multipart) → POST /api/profile/{logo|avatar}
  → Form Request → Action::execute() → Storage + Profile
  ← ProfileResource { logoUrl, avatarUrl, ... }

Web ← GET /api/portfolio (incluye logoUrl, avatarUrl)
  → Nav/Footer/Favicon usan logoUrl
  → About usa avatarUrl
```

Sin repositories ni DTOs. Controllers delgados. Tests Pest Feature: upload logo/avatar (Storage::fake), portfolio expone URLs, delete limpia archivo.

## 6. Fuera de alcance

- Generación automática de múltiples tamaños de favicon (apple-touch, etc.) más allá de `rel="icon"`
- CDN externo / S3 (local `public` disk basta para MVP)
- Cambio de dirección visual a claro-editorial o monocromo
- Multi-usuario / varias marcas

## 7. Criterios de éxito

1. Subir logo en panel → aparece en sidebar, nav web, footer web y favicon del browser.
2. Subir avatar en panel → aparece en Sobre mí de la web (no en hero).
3. Panel y web comparten tokens de color y tipografía.
4. Sin logo/avatar: fallbacks claros (iniciales / favicon estático).
5. Tests de upload y portfolio en verde.
