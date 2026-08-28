# Rocket LMS Dashboard UI Reference

> Scope: authenticated Rocket LMS dashboard/panel UI only. This document intentionally excludes the public website and landing-page design.

## Capture context

| Item | Value |
|---|---|
| Reference screen | `https://lms.rocket-soft.org/panel/support/new` |
| Screen | New support request |
| Capture date | 27 August 2026 |
| Desktop viewport | 1186 × 706 px |
| Page scroll height | 738 px |
| Inspection method | Rendered DOM, computed CSS, layout bounds, and visible SVG inspection in the signed-in Codex browser |
| Source stylesheets | `simplebar.css`, `app.min.css`, `panel.min.css` |

Measurements below describe the observed desktop state. Responsive behaviors marked **inferred** come from the rendered Bootstrap-style grid classes and should be verified at mobile/tablet breakpoints before implementation.

## Visual character

The dashboard uses a quiet, application-first visual language:

- a white 70 px global header;
- a fixed-width white navigation rail;
- a very light blue-grey workspace;
- flat white cards with large radii and almost no elevation;
- compact Gilroy typography;
- muted blue-grey icons and secondary copy;
- blue reserved for primary actions and active states;
- 48 px controls and action targets;
- generous empty space rather than decorative imagery.

The result is closer to a productivity application than a marketing site. Content hierarchy comes from spacing, weight, surface colour, and navigation grouping—not gradients, oversized headings, or heavy shadows.

## UI architecture

```text
Authenticated dashboard shell
├── Global header — 70 px high
│   ├── Brand zone — 258 px wide
│   └── Header controls
│       ├── Primary product navigation
│       ├── Currency / locale controls
│       ├── Utility icons
│       └── User menu
├── Application body
│   ├── Sidebar — 258 px wide
│   │   ├── User identity
│   │   ├── User statistics
│   │   ├── Navigation section labels
│   │   ├── Primary navigation rows
│   │   └── Accordion groups and nested routes
│   └── Workspace — remaining width
│       ├── Page title bar
│       └── Scrollable content region
│           └── Responsive grid
│               ├── Primary form/card column
│               └── Optional secondary column
└── Floating utilities
    └── AI/support widget, visually independent from page content
```

### Shell measurements

| Region | Observed specification |
|---|---|
| Global header | `height: 70px`; white; flat; full width |
| Header brand zone | `width: 258px`; vertically centred content |
| Header controls zone | `width: calc(100% - 258px)`; `padding-inline: 30px`; bottom divider |
| Sidebar | `width: 258px`; begins below header; white; independent vertical navigation |
| Workspace | begins at `x: 258px`; background inherits `#F5F8F9` |
| Page title | 32 px from workspace left; 16 px bold |
| Content padding | `20px 32px 40px` |
| Desktop content grid | 12 columns with 8 px half-gutters; form uses `col-12 col-lg-6` |
| Form column | 440 px including gutters; 424 px card surface |

### Architectural rules

1. Keep the header, sidebar, and page content as separate layout regions.
2. Let the workspace scroll independently where possible; navigation should not drift with long forms.
3. Use one reusable page shell for all dashboard routes.
4. Place route-specific content inside the workspace grid; do not rebuild navigation per page.
5. Use cards only to group a task or workflow. Avoid wrapping every small item in a separate card.
6. Keep secondary tools in an optional second column or drawer so the main task remains focused.

## Design tokens

### Colour palette

| Token | Hex | Observed use |
|---|---:|---|
| `--dashboard-bg` | `#F5F8F9` | Main workspace background |
| `--surface` | `#FFFFFF` | Header, sidebar, cards, form controls |
| `--text-strong` | `#121F3E` | Headings, labels, main navigation, primary copy |
| `--text-muted` | `#97A7BF` | Hints, icons, secondary labels |
| `--text-subtle` | `#CDD5E2` | Section labels, chevrons, inactive icon details |
| `--border-soft` | `#E9EDF3` | Input and file-control borders |
| `--surface-soft` | `#FAFCFF` | Very subtle secondary surfaces |
| `--surface-muted` | `#F0F4F9` | Icon/notice wells and muted controls |
| `--primary` | `#0170FF` | Primary action and active navigation state |
| `--danger` | `#F63C3C` | Destructive or alert icon state |
| `--success` | `#3FCD82` | Success state |
| `--rating` | `#FFA200` | Rating stars |

### GEIC dashboard adaptation

To retain the Rocket LMS architecture while matching the approved GEIC visual system:

| Role | GEIC value |
|---|---:|
| Primary text / dark shell | `#0E2145` |
| Primary action | `#E31E24` |
| Hover and warm emphasis | `#F3951E` |
| Neutral surfaces and borders | retain the Rocket LMS values above |

Do not replace every blue-grey neutral with red. Red should identify selection, action, progress, and important status. Orange is the hover/accent state. This preserves the restrained dashboard hierarchy.

### Typography

| Role | Family | Size | Weight | Notes |
|---|---|---:|---:|---|
| Default UI | Gilroy | 14 px | 400 | Main dashboard copy and controls |
| Medium UI | Gilroy | 14 px | 500 | Optional emphasis |
| Page title | Gilroy | 16 px | 700 | Compact route heading |
| Card title | Gilroy | 14 px | 700 | Section heading inside a card |
| User name | Gilroy | 14 px | 700 | Sidebar identity |
| Navigation group | Gilroy | 12 px | 700 | Uppercase, muted |
| Metadata/stat label | Gilroy | 12 px | 400 | Muted blue-grey |
| Metadata/stat value | Gilroy | 12 px | 700 | Dark value |
| Floating field label | Gilroy | 12 px | 400 | Sits over the field border |

Observed font files:

- Gilroy Regular, Medium, and Bold in WOFF2 format;
- Tajawal Regular, Medium, and Bold for right-to-left locales;
- `font-display: swap` for both families.

Use a locally licensed Gilroy package. If unavailable, use a metrically similar UI sans-serif fallback and verify line wrapping; do not hotlink the Rocket LMS font files.

### Spacing scale

The interface repeatedly uses an 8 px base rhythm:

| Token | Value | Typical use |
|---|---:|---|
| `space-1` | 4 px | Tiny optical adjustment |
| `space-2` | 8 px | Icon/text gap, compact inset |
| `space-3` | 12 px | Control radius and small gaps |
| `space-4` | 16 px | Card padding, standard gaps |
| `space-5` | 20 px | Form-group separation |
| `space-6` | 24 px | Larger component separation |
| `space-8` | 32 px | Workspace horizontal padding |
| `space-10` | 40 px | Workspace bottom padding |
| `space-14` | 56 px | Page-section bottom breathing room |

### Radius and elevation

| Component | Radius | Elevation |
|---|---:|---|
| Primary button | 8 px | none |
| File browse inset | 8 px | none |
| Text input/select/textarea/file field | 12 px | none |
| Content card | 24 px | none |
| Avatar and status badge | 50% / pill | none |

The panel is deliberately flat. Use borders and background contrast first. Add a soft shadow only for floating overlays, menus, and drag states.

## Iconography system

### Visual style

- Simple SVG icons with rounded line geometry.
- Navigation and utility icons are primarily outline/stroke icons.
- Some status and feature icons use solid fills.
- Icons inherit semantic colour from the parent component.
- Avoid mixed icon libraries on the same screen; keep stroke width and corner language consistent.
- Icons are functional labels, not decoration. Pair unfamiliar icons with text or accessible names.

### Size hierarchy

| Size | Role | Observed use |
|---:|---|---|
| 12 px | Disclosure/detail | Sidebar accordion chevrons |
| 14 px | Dense metadata | Currency arrow, rating stars, compact inline status |
| 20 px | Standard UI icon | Header utilities and sidebar navigation |
| 24 px | Emphasis/action | Form notice well, larger contextual and floating controls |
| 64 px | Avatar container | Sidebar user identity; avatar includes a 4 px workspace-colour ring |

### Colour hierarchy

| State | Colour |
|---|---:|
| Default navigation/utility | `#97A7BF` |
| Low-emphasis disclosure | `#CDD5E2` |
| Strong/default text icon | `#121F3E` |
| Active/selected | `#0170FF` in source; `#E31E24` in GEIC |
| Hover | `#F3951E` in GEIC |
| Destructive/error | `#F63C3C` |
| On primary surface | `#FFFFFF` |
| Rating | `#FFA200` |

### Icon containers

Use an icon well when an icon represents a state or standalone action:

```text
48 × 48 px container
├── 8–12 px radius depending on context
├── soft neutral or semantic-tint background
└── 20–24 px centred icon
```

For sidebar rows, do not add a container around every icon. Use a 20 px icon, a consistent text gap, and a 40 px row height. Accordion chevrons remain 12 px and align to the far edge.

### Recommended icon component API

```text
Icon
├── name: semantic identifier, not file name
├── size: 12 | 14 | 20 | 24
├── tone: muted | subtle | strong | primary | danger | success | inverse
├── style: outline | solid
└── decorative: true/false

IconButton
├── Icon
├── 40–48 px interactive target
├── accessible label
├── hover/focus state
└── optional badge
```

Name icons by meaning (`dashboard`, `calendar`, `support`, `upload`, `chevron-down`) rather than by appearance (`square-1`, `arrow-2`). This makes replacement and accessibility much easier.

## Component specifications

### Global header

- Height: 70 px.
- White background with a subtle bottom divider on the controls region.
- Brand region matches sidebar width at 258 px.
- Remaining controls region uses 30 px horizontal padding.
- Standard utility icons: 20 px, `#97A7BF`.
- Compact chevrons: 14 px.
- Keep profile/currency menus visually subordinate to the current task.

### Sidebar navigation

- Width: 258 px.
- White surface, no shadow, no rounded outer container.
- Profile avatar: 64 px circle with a 4 px `#F5F8F9` ring.
- Main navigation rows: 40 px height.
- Row padding: `0 20px 0 32px`.
- Group labels: 12 px bold uppercase, `#CDD5E2`, 8 px margin below.
- Standard icon: 20 px; disclosure chevron: 12 px.
- Active nested item: primary colour; inactive nested items: `#97A7BF`.
- Accordion parents use strong text and far-edge chevrons.

### Workspace title and grid

- Page title: 16 px bold, `#121F3E`.
- Left offset: 32 px from the workspace edge.
- Content padding: 20 px top, 32 px inline, 40 px bottom.
- Use a 12-column responsive grid.
- The observed form is half-width on large screens and full-width below the large breakpoint.

### Content card

- White surface.
- 24 px radius.
- 16 px padding.
- No resting shadow or visible outer border.
- Title: 14 px bold.
- Separate form groups with 20 px vertical space.

### Floating-label form control

```css
height: 48px;
padding: 14px 16px;
border: 1px solid #E9EDF3;
border-radius: 12px;
background: #FFFFFF;
font: 400 14px/1.2 Gilroy, sans-serif;
color: #121F3E;
```

The 12 px label is absolutely positioned across the upper border with a white 2 px horizontal backing. Preserve a real `<label>` relationship; the visual treatment must not replace accessible labelling.

### Textarea

- Same tokens as the standard control.
- Observed height: approximately 198 px.
- Resize behavior should be vertical only unless the form layout requires a fixed height.

### File control

- Outer field: 392 px × 48 px in the captured desktop card.
- White background, 1 px `#E9EDF3` border, 12 px radius.
- Outer padding: `14px 16px`.
- File name/action text: 14 px dark.
- Browse inset: 36 px high, `#E9EDF3` background, 8 px radius, 8 px padding, muted text.
- Recommended dashboard enhancement: convert this compact field into a reusable drag-and-drop uploader when media is the task, while retaining **Browse** and **Select from library** actions.

### Notice/action row

- Use a 48 px pale icon well with a 24 px icon.
- Place notice copy 8 px after the icon.
- Keep the primary action on the far right.
- Collapse to a vertical stack on narrow screens.

### Primary button

- Height: 48 px.
- Horizontal padding: 20 px.
- Radius: 8 px.
- Font: 14 px regular.
- Source background/border: `#0170FF`; GEIC background/border: `#E31E24`.
- Text: white.
- No resting shadow.
- GEIC hover: `#F3951E`; keep text white and use a 150–200 ms colour transition.
- Focus must use a visible outer ring independent of hover colour.

## Responsive architecture

The captured page contains `col-12 col-lg-6`, establishing this baseline:

| Breakpoint behavior | Rule |
|---|---|
| Large desktop | 258 px sidebar + flexible workspace; form uses 50% grid width |
| Tablet | Collapse or overlay the sidebar; content card may remain half/full width based on available space |
| Mobile | Hide desktop header navigation, use a compact top bar, make form/card full width, and stack notice/action rows |

For the GEIC dashboard, use these practical targets:

- desktop shell at `>= 1024px`;
- collapsible icon rail between `768px` and `1023px`;
- off-canvas navigation below `768px`;
- 16 px mobile workspace padding;
- full-width 48 px primary actions on narrow screens;
- never horizontally scroll forms.

## State model

Every reusable dashboard component should define:

- default;
- hover;
- keyboard focus-visible;
- active/selected;
- disabled;
- loading;
- validation error;
- validation success.

Only resting-state computed styles were captured. Hover, focus, loading, and validation behavior should be implemented from the GEIC token layer and verified interactively rather than inferred from a static screen.

## Accessibility requirements

- Maintain at least 44 × 44 px pointer targets; the observed 48 px controls satisfy this.
- Do not use colour alone for selected, error, or success states.
- Pair icon-only buttons with `aria-label` or visible tooltips.
- Keep labels programmatically associated with their inputs.
- Ensure muted `#97A7BF` text is used for secondary information, not essential long-form copy without a contrast check.
- Preserve a visible focus ring on fields, buttons, sidebar items, menus, and uploader actions.
- Keep drag-and-drop optional; keyboard and file-picker alternatives are required.
- Announce upload progress and errors through an `aria-live` region.
- Mark decorative SVGs `aria-hidden="true"` and remove them from the tab order.

## Suggested implementation primitives

```text
DashboardShell
├── DashboardHeader
│   ├── Brand
│   ├── HeaderNav
│   ├── UtilityIconButton
│   └── UserMenu
├── DashboardSidebar
│   ├── UserSummary
│   ├── NavSection
│   ├── NavItem
│   └── NavAccordion
└── DashboardWorkspace
    ├── PageHeader
    ├── ResponsiveGrid
    └── TaskCard
        ├── FloatingField
        ├── SelectField
        ├── TextareaField
        ├── FileUploader
        │   ├── DropZone
        │   ├── BrowseAction
        │   ├── MediaLibraryAction
        │   └── UploadStatus
        └── FormActionRow
```

These primitives should power every GEIC admin route. Page templates should compose them rather than introducing route-specific copies of the same field, upload, card, navigation, or button markup.

## CSS token starter

```css
:root {
  --dashboard-font: "Gilroy", "Inter", system-ui, sans-serif;
  --dashboard-bg: #f5f8f9;
  --dashboard-surface: #ffffff;
  --dashboard-text: #0e2145;
  --dashboard-muted: #97a7bf;
  --dashboard-subtle: #cdd5e2;
  --dashboard-border: #e9edf3;
  --dashboard-primary: #e31e24;
  --dashboard-hover: #f3951e;
  --dashboard-danger: #f63c3c;
  --dashboard-success: #3fcd82;

  --dashboard-header-height: 70px;
  --dashboard-sidebar-width: 258px;
  --dashboard-control-height: 48px;
  --dashboard-radius-control: 12px;
  --dashboard-radius-button: 8px;
  --dashboard-radius-card: 24px;
}
```

## Dashboard implementation checklist

- [ ] Shared 70 px header and 258 px desktop sidebar
- [ ] Independent workspace/content scrolling
- [ ] Gilroy typography and Tajawal RTL fallback where licensed
- [ ] Unified 12/14/20/24 px icon scale
- [ ] One semantic icon component and one icon-button component
- [ ] 40 px navigation rows with consistent icon/text alignment
- [ ] 48 px fields and action buttons
- [ ] Floating labels remain accessible labels
- [ ] Reusable drag/drop uploader with Browse and Media Library choices
- [ ] 24 px task-card radius and flat resting surfaces
- [ ] GEIC red primary and orange hover behavior
- [ ] Keyboard focus, disabled, loading, validation, and upload states
- [ ] Desktop, tablet, and mobile shell verification

## Source-versus-adaptation boundary

This document records the visual and architectural behavior of the rendered Rocket LMS dashboard to guide an original GEIC implementation. It does not copy Rocket LMS templates, proprietary source code, icons, or font binaries. The GEIC dashboard should reproduce the application principles and measured proportions using project-owned components and licensed assets.
