# Product Design Contract

Keep this document specific, short, and durable. It is the visual and interaction source of truth shared by design, implementation, browser QA, and review. Replace template prompts with accepted decisions; do not preserve a menu of unused options.

## Owner direction

Capture this from the user's ordinary brief or `/design`; do not require every field or a separate approval step. Keep explicit choices separate from agent-proposed details. Preserve it across bootstrap/resume; revise only what the current request supersedes.

- Owner-stated style / design system: ultra-beautiful, modern Iranian mobile shop; speed + beauty are top priorities; no Elementor (native lightweight theme); dark/light dual theme required.
- Exact brand colors and intended roles (owner-supplied 2026-10-11, CSS variables):
  light — text #080c13 / background #f2f5fb / primary #3064d6 / secondary #7a9ff2 / accent #3a75fb
  dark — text #edf1f7 / background #04060c / primary #295ed1 / secondary #0d3487 / accent #043dc3
  Selector contract: `:root[data-theme="light"|"dark"]`. Contrast pairs unmeasured yet — must prove before ship.
- Theme(s), typography, density, shape, motion, RTL/locales: fa_IR, RTL-first; typography TBD (Vazirmatn subset proposed, not yet owner-approved).
- Must keep / avoid: must keep WooCommerce + official Torob plugin + Zibal gateway + RankMath SEO compatibility; avoid page builders, jQuery-heavy components, multi-cache stacks.
- Agent-proposed details / unresolved choices: Vazirmatn stack w/ Tahoma fallback (no webfont file yet — system rendering; bundle woff2 as follow-up); token file theme/mobilekh/assets/css/tokens.css (created, light+dark); signature = gradient hero ring + signature product cards + pill category nav; homepage query caching via transients = open follow-up.
- Canonical code token source (path; unknown until inspected or implemented): theme/mobilekh/assets/css/tokens.css — implemented; semantic roles text/muted/background/surface/primary/secondary/accent/border/success/danger/warning + radius/spacing scale.

Before implementation, semantic values below specify the intended palette; distinguish owner-stated values from proposed details. After implementation, the code token source owns resolved values; this document owns intent and token mappings. Update both for an accepted design change. Never maintain a second competing palette. Color/style choices here affect the product, not Pi's terminal theme.

## Experience brief

- Product / surface:
- Primary audience:
- Single job of this surface:
- Desired user feeling before → after:
- Success signal:

## Brand character

Describe useful tensions rather than vague adjectives.

- [character], not [failure mode]
- [character], not [failure mode]
- [character], not [failure mode]

## Reference calibration

| Reference / local image | Owner preference | Adopt / avoid and reason | Inspection status |
|---|---|---|---|
|  | liked / disliked / unspecified |  | inspected / not inspected |

Use a few relevant examples when supplied; do not invent owner approval or visual observations. Judge the rendered result against these preferences and record concrete mismatches, not a generic beauty score. References calibrate principles; they are not permission to clone another product.

## Direction

- Visual thesis:
- Signature element:
- Aesthetic risk or intentional restraint:
- What must feel familiar:
- What must never look generic:

## Semantic tokens

### Color

Use only required roles/themes. Values must be exact (for example HEX or OKLCH), not just “green”. If the user supplied only a color name, label the chosen value as proposed. Record actual foreground/background pairs and measured ratios; leave unmeasured contrast unproven.

| Role / state | Theme | Exact value or resolved code token | Foreground/background pair | Contrast proof |
|---|---|---|---|---|
| canvas / surface |  |  |  |  |
| text / muted text |  |  |  |  |
| action / on-action |  |  |  |  |
| accent |  |  |  |  |
| border / focus |  |  |  |  |
| danger / success / warning |  |  |  |  |

### Typography

| Role | Family / fallback | Scale / weight / leading | Purpose |
|---|---|---|---|
| display |  |  |  |
| body |  |  |  |
| utility / data |  |  |  |

Record font source and license. Define a fallback that preserves hierarchy and metrics acceptably.

### Geometry and depth

- Spacing/rhythm:
- Grid/content measure:
- Radius logic:
- Border/shadow logic:
- Icon/media treatment:

### Media and art direction

- Photography / illustration / data-visualization language:
- Subject, framing, crop, lighting, texture, and color treatment:
- Icon family and stroke/fill rules:
- Asset source, ownership/license, and attribution:
- Responsive art direction and meaningful alt-text rules:
- Fallback when the preferred asset cannot load:

## Composition and responsiveness

- Desktop composition:
- Mobile recomposition:
- Dense/long-content behavior:
- Supported viewport/device baseline:
- RTL/localization behavior:

## Components and states

| Component / pattern | Variants | Required states | Reuse or change |
|---|---|---|---|
|  |  | default / hover / focus / active / disabled / error |  |

Required journey states:

- loading:
- empty:
- error/retry:
- success:
- permission/offline where relevant:

## Motion and feedback

- Orchestrated moment (or explicit none):
- State-transition motion:
- Duration/easing tokens:
- Reduced-motion alternative:
- Sound/haptics where applicable:

## Content voice

- Vocabulary and tone:
- Action-label rules:
- Error and empty-state rules:
- Realistic content fixtures:

## Quality budgets

- Accessibility target: WCAG 2.2 AA unless the product accepts another target.
- Text/non-text contrast target:
- Keyboard/focus/touch target:
- Performance target: LCP ≤ 2.5 s, INP ≤ 200 ms, CLS ≤ 0.1 at p75 unless stricter product budgets are accepted.
- Pre-release lab budget and production RUM/rollout proof:
- Image/font/JS budget:
- Supported browsers and input modes:

## Screen acceptance

| Flow / screen | Critical states | Viewports/locales | Visual proof |
|---|---|---|---|
|  |  |  |  |

## Decisions intentionally deferred

-

## Decision log

| Date | Decision | Evidence / rationale | Revisit when |
|---|---|---|---|
|  |  |  |  |
